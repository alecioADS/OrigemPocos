<?php
/* Template Name: Sobre Nós */
get_header();
?>

<!-- ── HERO BANNER ───────────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <h1>Sobre a Origem</h1>
    <p>Conheça nossa história e nossos diferenciais</p>
  </div>
</section>

<!-- ── HISTÓRIA ─────────────────────────────────────────────────────── -->
<section class="section" style="background: var(--bege-claro);">
  <div class="container">
    <div class="about-content">

      <div class="about-text">
        <h2>Nossa História</h2>
        <p>
          Com 4 anos de mercado, a Origem Poços Artesianos se consolidou como referência
          em perfuração e manutenção de poços artesianos no Centro-Oeste e estados vizinhos.
        </p>
        <p>
          Atuamos em 6 estados — MG, GO, SP, DF, MT e BA — atendendo desde residências
          até grandes empresas e fazendas com soluções completas de captação de água subterrânea.
        </p>
        <p class="about-note">
          Nossa documentação está sempre em ordem: CREA, Laudos Técnicos e ART garantem
          total conformidade legal em todas as obras realizadas.
        </p>
      </div>

      <div class="about-image">
        [ Foto da Equipe /<br>Maquinário em Operação ]
      </div>

    </div>
  </div>
</section>

<!-- ── STATS ─────────────────────────────────────────────────────────── -->
<div class="stats-grid">
  <?php
  $stats = [
    ['6',    'Estados atendidos'],
    ['4+',   'Anos de mercado'],
    ['800m', 'Profundidade máxima'],
    ['24h',  'Prazo de entrega'],
  ];
  foreach ($stats as [$val, $label]) : ?>
    <div class="stat-card">
      <span class="stat-value"><?php echo esc_html($val); ?></span>
      <span class="stat-label"><?php echo esc_html($label); ?></span>
    </div>
  <?php endforeach; ?>
</div>

<!-- ── CERTIFICAÇÕES ─────────────────────────────────────────────────── -->
<div class="cert-bar">
  <div class="container">
    <p>&#10004;&nbsp; CREA Regularizado &nbsp;&nbsp;&nbsp; &#10004;&nbsp; Laudos Técnicos &nbsp;&nbsp;&nbsp; &#10004;&nbsp; ART em todas as obras &nbsp;&nbsp;&nbsp; &#10004;&nbsp; Equipe Certificada</p>
  </div>
</div>

<?php get_footer(); ?>
