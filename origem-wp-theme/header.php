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

      <a href="<?php echo esc_url(home_url('/')); ?>" class="navbar__brand">
        <img
          src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.jpeg"
          alt="Origem Poços Artesianos"
          class="navbar__logo"
        >
        <div class="brand-text">
          <span class="brand-name">ORIGEM</span>
          <span class="brand-sub">POÇOS ARTESIANOS</span>
        </div>
      </a>

      <button class="navbar__toggle" aria-label="Abrir menu" onclick="document.getElementById('nav-links').classList.toggle('open')">
        <span></span><span></span><span></span>
      </button>

      <ul class="navbar__links" id="nav-links">
        <?php
        $pages = [
          'Início'    => home_url('/'),
          'Serviços' => home_url('/servicos'),
          'Sobre Nós' => home_url('/sobre/'),
          'Portfólio' => home_url('/portfolio/'),
          'Contato'   => home_url('/contato/'),
        ];
        foreach ($pages as $label => $url) :
          $active = (rtrim($_SERVER['REQUEST_URI'], '/') === rtrim(parse_url($url, PHP_URL_PATH), '/')) ? 'active' : '';
        ?>
          <li><a href="<?php echo esc_url($url); ?>" class="<?php echo $active; ?>"><?php echo esc_html($label); ?></a></li>
        <?php endforeach; ?>
      </ul>

      <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="btn btn--primary navbar__cta">Fale Conosco</a>

    </nav>
  </div>
</header>