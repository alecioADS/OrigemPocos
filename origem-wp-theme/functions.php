<?php
defined('ABSPATH') || exit;

/* ── Suporte e menus ───────────────────────────────────────────────── */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);

    register_nav_menus(['primary' => 'Menu Principal']);
});

/* ── Enqueue estilos e scripts ─────────────────────────────────────── */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
        [],
        null
    );
    wp_enqueue_style(
        'origem-style',
        get_stylesheet_uri(),
        ['inter-font'],
        wp_get_theme()->get('Version')
    );
    wp_enqueue_script(
        'origem-script',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
});

/* ── Helper: limpa CRLF para evitar e-mail header injection ───────── */
function origem_strip_crlf($value) {
    return preg_replace('/[\r\n]+/', ' ', (string) $value);
}

/* ── Processamento do formulário de contato ────────────────────────── */
add_action('init', function () {
    if (! isset($_POST['origem_submit'], $_POST['origem_contact_nonce'])) {
        return;
    }

    $nonce = isset($_POST['origem_contact_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['origem_contact_nonce']))
        : '';

    if (! wp_verify_nonce($nonce, 'origem_contact_form')) {
        wp_safe_redirect(add_query_arg('msg', 'error', wp_get_referer() ?: home_url('/contato/')));
        exit;
    }

    // Honeypot — bots geralmente preenchem todos os campos.
    $honeypot = isset($_POST['origem_website'])
        ? sanitize_text_field(wp_unslash($_POST['origem_website']))
        : '';
    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('msg', 'success', wp_get_referer() ?: home_url('/contato/')));
        exit;
    }

    // Rate limit por IP — só conta quando o envio for VÁLIDO (impede que erro
    // de validação bloqueie o próximo retry do usuário).
    $ip_raw  = isset($_SERVER['REMOTE_ADDR']) ? wp_unslash($_SERVER['REMOTE_ADDR']) : '';
    $ip      = filter_var($ip_raw, FILTER_VALIDATE_IP) ?: 'unknown';
    $rl_key  = 'origem_contact_rl_' . md5($ip);
    if (get_transient($rl_key)) {
        wp_safe_redirect(add_query_arg('msg', 'wait', wp_get_referer() ?: home_url('/contato/')));
        exit;
    }

    $nome     = origem_strip_crlf(sanitize_text_field(wp_unslash($_POST['contact_nome']     ?? '')));
    $telefone = origem_strip_crlf(sanitize_text_field(wp_unslash($_POST['contact_telefone'] ?? '')));
    $endereco = origem_strip_crlf(sanitize_text_field(wp_unslash($_POST['contact_endereco'] ?? '')));
    $tipo     = origem_strip_crlf(sanitize_text_field(wp_unslash($_POST['contact_tipo']     ?? '')));
    $prof     = origem_strip_crlf(sanitize_text_field(wp_unslash($_POST['contact_prof']     ?? '')));
    $obs      = sanitize_textarea_field(wp_unslash($_POST['contact_obs'] ?? ''));

    // Validação dos campos obrigatórios — passa o nome do campo que faltou.
    $missing = [];
    if ($nome     === '') $missing[] = 'nome';
    if ($telefone === '') $missing[] = 'telefone';
    if ($endereco === '') $missing[] = 'endereco';
    if (! empty($missing)) {
        wp_safe_redirect(add_query_arg([
            'msg'     => 'missing',
            'campos'  => implode(',', $missing),
        ], wp_get_referer() ?: home_url('/contato/')));
        exit;
    }

    // Limites de tamanho para evitar abuso.
    $nome     = mb_substr($nome, 0, 120);
    $telefone = mb_substr($telefone, 0, 40);
    $endereco = mb_substr($endereco, 0, 250);
    $tipo     = mb_substr($tipo, 0, 60);
    $prof     = mb_substr($prof, 0, 80);
    $obs      = mb_substr($obs, 0, 2000);

    // Tudo válido — agora sim aplica o rate-limit (30s entre envios bem-sucedidos).
    set_transient($rl_key, 1, 30);

    $to      = sanitize_email(get_option('admin_email'));
    $subject = sprintf('Novo orçamento — %s', $nome);
    $body    = "Nome: {$nome}\nTelefone: {$telefone}\nEndereço: {$endereco}\nTipo: {$tipo}\nProfundidade: {$prof}\nObservações:\n{$obs}";
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    $sent = wp_mail($to, $subject, $body, $headers);

    // Envia também para a planilha Google (Apps Script webhook).
    $sheet_ok = origem_send_to_google_sheets([
        'nome'      => $nome,
        'telefone'  => $telefone,
        'endereco'  => $endereco,
        'tipo'      => $tipo,
        'prof'      => $prof,
        'obs'       => $obs,
        'origem_ip' => $ip,
        'data_iso'  => current_time('c'),
    ]);

    // Considera success se ao menos um dos canais (e-mail ou planilha) confirmou.
    $status = ($sent || $sheet_ok) ? 'success' : 'error';

    wp_safe_redirect(add_query_arg('msg', $status, wp_get_referer() ?: home_url('/contato/')));
    exit;
});

/* ── Helpers de configuração — prioridade: constante > option > env ─ */
function origem_get_setting($key) {
    // 1. Constante em wp-config.php (mais seguro em produção)
    if (defined($key) && constant($key)) {
        return (string) constant($key);
    }
    // 2. Option salva via Settings → Origem (shared hosting)
    $option = get_option('origem_' . strtolower($key));
    if ($option) {
        return (string) $option;
    }
    // 3. Variável de ambiente (Docker / local)
    return (string) (getenv($key) ?: '');
}

/* ── Envio dos dados do formulário para Google Sheets ─────────────── */
function origem_send_to_google_sheets(array $payload) {
    $webhook = origem_get_setting('ORIGEM_SHEETS_WEBHOOK_URL');
    $token   = origem_get_setting('ORIGEM_SHEETS_TOKEN');

    // Se a integração não estiver configurada, não falha — apenas pula.
    if (! $webhook || ! filter_var($webhook, FILTER_VALIDATE_URL)) {
        return false;
    }

    $payload['token'] = (string) $token;

    // POST inicial sem seguir redirect automaticamente.
    // O Apps Script responde 302 e o Location precisa ser seguido com GET
    // (e não POST), o que o WP_Http não faz por padrão.
    $response = wp_remote_post($webhook, [
        'timeout'     => 10,
        'redirection' => 0,
        'blocking'    => true,
        'headers'     => ['Content-Type' => 'application/json'],
        'body'        => wp_json_encode($payload),
        'sslverify'   => true,
    ]);

    if (is_wp_error($response)) {
        error_log('[Origem] Sheets POST falhou: ' . $response->get_error_message());
        return false;
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    // Apps Script redireciona para script.googleusercontent.com/macros/echo.
    if ($code === 301 || $code === 302) {
        $location = wp_remote_retrieve_header($response, 'location');
        if (! $location) {
            return false;
        }
        $response = wp_remote_get($location, [
            'timeout'     => 10,
            'redirection' => 5,
            'sslverify'   => true,
        ]);
        if (is_wp_error($response)) {
            error_log('[Origem] Sheets GET (redirect) falhou: ' . $response->get_error_message());
            return false;
        }
        $code = (int) wp_remote_retrieve_response_code($response);
    }

    if ($code < 200 || $code >= 300) {
        error_log('[Origem] Sheets webhook HTTP ' . $code);
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $json = json_decode($body, true);
    return is_array($json) && ! empty($json['ok']);
}

/* ── Security Headers HTTP ─────────────────────────────────────────── */
add_action('send_headers', function () {
    if (is_admin()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "script-src 'self'; " .
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
        "font-src 'self' https://fonts.gstatic.com; " .
        "img-src 'self' data:; " .
        "frame-src https://www.google.com; " .
        "connect-src 'self'; " .
        "base-uri 'self'; " .
        "form-action 'self'; " .
        "frame-ancestors 'self'; " .
        "object-src 'none'"
    );
});

/* ── Endurecimento do WordPress ────────────────────────────────────── */

// Remove informação de versão (fingerprinting).
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

// Esconde versão de scripts/estilos (apenas no front).
add_filter('style_loader_src', 'origem_strip_version', 9999);
add_filter('script_loader_src', 'origem_strip_version', 9999);
function origem_strip_version($src) {
    if (is_admin()) {
        return $src;
    }
    if (strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

// Desabilita XML-RPC (vetor comum de brute-force/DDoS).
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function ($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// Bloqueia enumeração de usuários via ?author=N.
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }
    if (! empty($_GET['author']) || (isset($_SERVER['QUERY_STRING']) && preg_match('/(^|&)author=\d+/', wp_unslash($_SERVER['QUERY_STRING'])))) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

// Restringe REST API: nega enumeração de usuários para anônimos.
add_filter('rest_endpoints', function ($endpoints) {
    if (is_user_logged_in()) {
        return $endpoints;
    }
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

// Mensagem de erro de login genérica (não revela se usuário existe).
add_filter('login_errors', function () {
    return __('Credenciais inválidas.', 'origem');
});

// Desabilita edição de arquivos pelo painel (defense in depth).
if (! defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/* ── Página de configurações: Settings → Origem ──────────────────── */
add_action('admin_menu', function () {
    add_options_page(
        'Origem — Integrações',
        'Origem',
        'manage_options',
        'origem-settings',
        'origem_render_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('origem_settings_group', 'origem_origem_sheets_webhook_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ]);
    register_setting('origem_settings_group', 'origem_origem_sheets_token', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ]);
});

function origem_render_settings_page() {
    if (! current_user_can('manage_options')) {
        return;
    }
    $webhook = esc_attr(get_option('origem_origem_sheets_webhook_url', ''));
    $token   = esc_attr(get_option('origem_origem_sheets_token', ''));
    $masked  = $token ? str_repeat('•', max(0, strlen($token) - 4)) . substr($token, -4) : '';
    ?>
    <div class="wrap">
      <h1>Origem — Configurações</h1>
      <p>Configure aqui a integração do formulário de contato com o Google Sheets.
         Os dois campos abaixo correspondem ao webhook do Apps Script.</p>

      <form method="post" action="options.php">
        <?php settings_fields('origem_settings_group'); ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="origem_webhook">URL do Apps Script</label></th>
            <td>
              <input type="url" id="origem_webhook"
                     name="origem_origem_sheets_webhook_url"
                     value="<?php echo $webhook; ?>"
                     class="regular-text code"
                     placeholder="https://script.google.com/macros/s/.../exec">
              <p class="description">URL completa da implantação do Web App (termina em <code>/exec</code>).</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="origem_token">Token compartilhado</label></th>
            <td>
              <input type="text" id="origem_token"
                     name="origem_origem_sheets_token"
                     value="<?php echo $token; ?>"
                     class="regular-text code"
                     autocomplete="off">
              <?php if ($masked): ?>
                <p class="description">Atual: <code><?php echo esc_html($masked); ?></code></p>
              <?php endif; ?>
              <p class="description">Mesmo valor da constante <code>EXPECTED_TOKEN</code> no Apps Script.</p>
            </td>
          </tr>
        </table>
        <?php submit_button('Salvar configurações'); ?>
      </form>

      <hr>
      <h2>Testar conexão</h2>
      <p>Após salvar, você pode disparar um envio de teste:</p>
      <p>
        <a href="<?php echo esc_url(add_query_arg(['origem_test_webhook' => '1', '_wpnonce' => wp_create_nonce('origem_test')], admin_url('options-general.php?page=origem-settings'))); ?>"
           class="button button-secondary">Enviar linha de teste para a planilha</a>
      </p>
      <?php
      if (isset($_GET['origem_test_result'])) {
          $ok = $_GET['origem_test_result'] === 'success';
          $cls = $ok ? 'notice-success' : 'notice-error';
          $msg = $ok ? 'Sucesso! Confira a planilha no Google Drive.' : 'Falha. Verifique a URL, o token e os logs.';
          echo '<div class="notice ' . esc_attr($cls) . '"><p>' . esc_html($msg) . '</p></div>';
      }
      ?>

      <hr>
      <h2>Configurar site (uso único)</h2>
      <p>Estes botões aplicam de uma vez as configurações esperadas pelo tema:</p>
      <ol>
        <li>Cria as 5 páginas (Início, Serviços, Sobre Nós, Portfólio, Contato) com os templates corretos.</li>
        <li>Define <strong>Início</strong> como página estática inicial.</li>
        <li>Ajusta os permalinks para <code>/%postname%/</code>.</li>
      </ol>
      <p>
        <a href="<?php echo esc_url(add_query_arg(['origem_setup_site' => '1', '_wpnonce' => wp_create_nonce('origem_setup_site')], admin_url('options-general.php?page=origem-settings'))); ?>"
           class="button button-primary">Executar configuração agora</a>
      </p>
      <?php
      if (isset($_GET['origem_setup_done'])) {
          $report = get_transient('origem_setup_report');
          delete_transient('origem_setup_report');
          if ($report) {
              echo '<div class="notice notice-success"><p><strong>Configuração executada:</strong></p><ul style="list-style:disc;padding-left:24px;">';
              foreach ((array) $report as $line) {
                  echo '<li>' . esc_html($line) . '</li>';
              }
              echo '</ul></div>';
          }
      }
      ?>
    </div>
    <?php
}

add_action('admin_init', function () {
    if (! isset($_GET['origem_test_webhook'])) return;
    if (! current_user_can('manage_options')) return;
    if (! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'origem_test')) return;

    $ok = origem_send_to_google_sheets([
        'nome'      => 'Teste via Painel',
        'telefone'  => '0',
        'endereco'  => 'Disparo manual de admin',
        'tipo'      => 'Teste',
        'prof'      => '-',
        'obs'       => 'Teste de conexão a partir de Settings → Origem',
        'origem_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'data_iso'  => current_time('c'),
    ]);
    wp_safe_redirect(add_query_arg(
        ['page' => 'origem-settings', 'origem_test_result' => $ok ? 'success' : 'fail'],
        admin_url('options-general.php')
    ));
    exit;
});

/* ── Configuração do site (cria páginas, define home, permalinks) ─── */
add_action('admin_init', function () {
    if (! isset($_GET['origem_setup_site'])) return;
    if (! current_user_can('manage_options')) return;
    if (! wp_verify_nonce($_GET['_wpnonce'] ?? '', 'origem_setup_site')) return;

    $report = [];

    $pages = [
        ['title' => 'Início',     'slug' => 'inicio',    'template' => ''],
        ['title' => 'Serviços',   'slug' => 'servicos',  'template' => 'page-servicos.php'],
        ['title' => 'Sobre Nós',  'slug' => 'sobre',     'template' => 'page-sobre.php'],
        ['title' => 'Portfólio',  'slug' => 'portfolio', 'template' => 'page-portfolio.php'],
        ['title' => 'Contato',    'slug' => 'contato',   'template' => 'page-contato.php'],
    ];

    $page_ids = [];
    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug'], OBJECT, 'page');
        if ($existing) {
            $id = (int) $existing->ID;
            // Atualiza template caso esteja diferente
            if ($p['template']) {
                update_post_meta($id, '_wp_page_template', $p['template']);
            }
            $report[] = sprintf('Página "%s" (slug: %s) já existia — template aplicado.', $p['title'], $p['slug']);
        } else {
            $id = wp_insert_post([
                'post_title'   => $p['title'],
                'post_name'    => $p['slug'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
                'meta_input'   => $p['template'] ? ['_wp_page_template' => $p['template']] : [],
            ], true);
            if (is_wp_error($id)) {
                $report[] = sprintf('Falha ao criar "%s": %s', $p['title'], $id->get_error_message());
                continue;
            }
            $report[] = sprintf('Página "%s" criada (ID %d).', $p['title'], $id);
        }
        $page_ids[$p['slug']] = $id;
    }

    // Define a página "Início" como home estática.
    if (! empty($page_ids['inicio'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_ids['inicio']);
        $report[] = 'Página estática inicial definida como "Início".';
    }

    // Permalinks bonitos.
    global $wp_rewrite;
    $current_structure = get_option('permalink_structure');
    if ($current_structure !== '/%postname%/') {
        update_option('permalink_structure', '/%postname%/');
        if (function_exists('flush_rewrite_rules')) {
            flush_rewrite_rules(false);
        }
        $report[] = 'Permalinks ajustados para /%postname%/.';
    } else {
        $report[] = 'Permalinks já estavam em /%postname%/.';
    }

    set_transient('origem_setup_report', $report, 5 * MINUTE_IN_SECONDS);

    wp_safe_redirect(add_query_arg(
        ['page' => 'origem-settings', 'origem_setup_done' => '1'],
        admin_url('options-general.php')
    ));
    exit;
});
