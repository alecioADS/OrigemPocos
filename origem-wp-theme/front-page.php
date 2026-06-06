<?php get_header(); ?>

<!-- ── HERO ──────────────────────────────────────────────────────────── -->
<section class="hero">
  <div class="container hero__grid">

    <div class="hero__content">
      <span class="hero__states">MG &nbsp;·&nbsp; GO &nbsp;·&nbsp; SP &nbsp;·&nbsp; DF &nbsp;·&nbsp; MT &nbsp;·&nbsp; BA</span>
      <h1>Poços Artesianos<br>de Alta Profundidade</h1>
      <p class="hero__subtitle">
        Perfurações até 800 metros · Entrega em até 24 horas<br>
        Atendemos residências, empresas, fazendas e condomínios.
      </p>
      <div class="hero__actions">
        <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="btn btn--primary">Solicitar Orçamento</a>
        <a href="<?php echo esc_url(home_url('/portfolio/')); ?>" class="btn btn--dark">Ver Portfólio &rarr;</a>
      </div>
    </div>

    <div class="hero__image">
      <img
        src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-home.png"
        alt="Equipe Origem Poços Artesianos em operação"
      >
    </div>
    <ul class="hero__badges">
       <li>&#10004;&nbsp; 4 Anos de Mercado</li>
      <li>&#10004;&nbsp; Documentação Completa (CREA/ART)</li>
      <li>&#10004;&nbsp; Maquinário de Ponta</li>
      <li>&#10004;&nbsp; Equipe Especializada</li>
    </ul>

  </div>
</section>

<!-- ── SERVIÇOS (preview) ────────────────────────────────────────────── -->
<section class="section services-preview">
  <div class="container">
    <div class="section-header">
      <h2>Nossos Serviços</h2>
      <p>Soluções completas para captação de água</p>
    </div>
    <div class="services-grid">
      <?php
      $services = [
        ['Perfuração de Poços',    '/servicos/'],
        ['Manutenção e Limpeza',   '/servicos/'],
        ['Análise de Viabilidade', '/servicos/'],
        ['Laudos e Documentação',  '/servicos/'],
      ];
      foreach ($services as [$title, $link]) : ?>
        <div class="service-card">
          <h3><?php echo esc_html($title); ?></h3>
          <a href="<?php echo esc_url(home_url($link)); ?>" class="card-link">Saiba mais &rarr;</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>