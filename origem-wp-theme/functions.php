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
        '1.0',
        true
    );
});

/* ── Processamento do formulário de contato ────────────────────────── */
add_action('init', function () {
    if (
        isset($_POST['origem_contact_nonce']) &&
        wp_verify_nonce($_POST['origem_contact_nonce'], 'origem_contact_form') &&
        isset($_POST['origem_submit'])
    ) {
        $nome       = sanitize_text_field($_POST['contact_nome']       ?? '');
        $telefone   = sanitize_text_field($_POST['contact_telefone']   ?? '');
        $endereco   = sanitize_text_field($_POST['contact_endereco']   ?? '');
        $tipo       = sanitize_text_field($_POST['contact_tipo']       ?? '');
        $prof       = sanitize_text_field($_POST['contact_prof']       ?? '');
        $obs        = sanitize_textarea_field($_POST['contact_obs']    ?? '');

        $to      = get_option('admin_email');
        $subject = "Novo orçamento — {$nome}";
        $body    = "Nome: {$nome}\nTelefone: {$telefone}\nEndereço: {$endereco}\nTipo: {$tipo}\nProfundidade: {$prof}\nObservações: {$obs}";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        $sent = wp_mail($to, $subject, $body, $headers);

        $status = $sent ? 'success' : 'error';
        wp_safe_redirect(add_query_arg('msg', $status, wp_get_referer()));
        exit;
    }
});
