<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="container">
    <nav class="navbar">

      <a href="<?php echo esc_url(home_url('/')); ?>" class="navbar__brand" aria-label="<?php esc_attr_e('Origem Poços Artesianos — Início', 'origem'); ?>">
        <img
          src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logoorigemfinal.png'); ?>"
          alt="<?php esc_attr_e('Origem Poços Artesianos', 'origem'); ?>"
          class="navbar__logo"
          width="80" height="80"
        >
      </a>

      <button class="navbar__toggle" id="navbar-toggle" aria-label="<?php esc_attr_e('Abrir menu', 'origem'); ?>" aria-expanded="false" aria-controls="nav-links">
        <span></span><span></span><span></span>
      </button>

      <ul class="navbar__links" id="nav-links">
        <?php
        $pages = [
          'Início'    => home_url('/'),
          'Serviços'  => home_url('/servicos'),
          'Sobre Nós' => home_url('/sobre/'),
          'Portfólio' => home_url('/portfolio/'),
          'Contato'   => home_url('/contato/'),
        ];

        $current_path = isset($_SERVER['REQUEST_URI'])
          ? rtrim(esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])), '/')
          : '';

        foreach ($pages as $label => $url) :
          $page_path = rtrim((string) wp_parse_url($url, PHP_URL_PATH), '/');
          $active    = ($current_path === $page_path) ? 'active' : '';
        ?>
          <li><a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($active); ?>"><?php echo esc_html($label); ?></a></li>
        <?php endforeach; ?>
      </ul>

      <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="btn btn--primary navbar__cta">Fale Conosco</a>

    </nav>
  </div>
</header>
