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
        'Poço Residencial — MG',
        'Poço Industrial — GO',
        'Fazenda — MT',
        'Condomínio — SP',
        'Empresa Rural — BA',
        'Perfuração 800m — DF',
      ];
      foreach ($projects as $project) : ?>
        <div class="portfolio-card">
          <div class="portfolio-card__image">
            [ Foto do Projeto ]
          </div>
          <div class="portfolio-card__label"><?php echo esc_html($project); ?></div>
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
      &rarr; WhatsApp
    </a>
  </div>
</section>

<?php get_footer(); ?>
