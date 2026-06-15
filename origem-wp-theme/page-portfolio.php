<?php
/* Template Name: Portfólio */
get_header();
?>

<!-- ── HERO BANNER ───────────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <h1>Portfólio de Obras</h1>
    <p>Confira alguns dos projetos realizados pela nossa equipe</p>
  </div>
</section>

<!-- ── GRID DE PROJETOS ──────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="portfolio-grid">

      <?php
      $projects = [
        ['port1.jpeg', 'Poço Residencial — MG'],
        ['port2.jpeg', 'Poço Industrial — GO'],
        ['port3.jpeg', 'Fazenda — MT'],
        ['port4.jpeg', 'Condomínio — SP'],
        ['port5.jpeg', 'Empresa Rural — BA'],
        ['port6.jpeg', 'Perfuração 800m — DF'],
      ];
      foreach ($projects as [$img, $label]) : ?>
        <div class="portfolio-card">
          <div class="portfolio-card__image">
            <img
              src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/' . $img); ?>"
              alt="<?php echo esc_attr($label); ?>"
            >
          </div>
          <div class="portfolio-card__label"><?php echo esc_html($label); ?></div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- ── CTA WHATSAPP ──────────────────────────────────────────────────── -->
<section class="portfolio-cta">
  <div class="container">
    <p>Quer ver mais projetos? Entre em contato e solicite uma visita técnica.</p>
    <a href="https://wa.me/5534999328198?text=Olá!%20Gostaria%20de%20saber%20mais%20sobre%20os%20projetos."
       target="_blank" rel="noopener noreferrer">
      &rarr; Falar pelo WhatsApp
    </a>
  </div>
</section>

<?php get_footer(); ?>
