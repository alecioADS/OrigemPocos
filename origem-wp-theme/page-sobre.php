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
        A Origem Poços Artesianos nasceu com o propósito de oferecer soluções
        completas em captação de água subterrânea, unindo tecnologia,
        qualidade e compromisso com cada cliente.
    </p>

    <p>
        Ao longo dos anos, construímos uma trajetória sólida no mercado de
        perfuração e manutenção de poços artesianos, atendendo propriedades
        rurais, indústrias, empresas e residências em diversas regiões.
    </p>

    <p>
        Nossa equipe é formada por profissionais qualificados e utiliza
        equipamentos modernos, garantindo serviços executados com segurança,
        eficiência e dentro das normas técnicas.
    </p>

    <p>
        Mais do que perfurar poços, buscamos proporcionar segurança hídrica,
        produtividade e tranquilidade aos nossos clientes.
    </p>

    <p class="about-note">
        Nossa documentação está sempre em ordem. CREA, Laudos Técnicos e ART
        garantem total segurança ao cliente.
    </p>
</div>

      <div class="about-image">
        <img
          src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/img-sobre.png'); ?>"
          alt="Equipe Origem Poços Artesianos em operação noturna"
        >
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
