<?php
/* Template Name: Serviços */
get_header();
?>

<!-- ── HERO BANNER ───────────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <h1>Nossos Serviços</h1>
    <p>Soluções completas para captação de água subterrânea</p>
  </div>
</section>

<!-- ── LISTA DE SERVIÇOS ─────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="services-list">

      <?php
      $services = [
        [
          'title' => 'Perfuração de Poços Artesianos',
          'desc'  => 'Perfurações de até 800 metros de profundidade com equipamentos de última geração para garantir vazão e qualidade da água.',
        ],
        [
          'title' => 'Poços para Empresas e Indústrias',
          'desc'  => 'Projetos dimensionados para alta demanda. Ideal para empresas, condomínios, hotéis e indústrias.',
        ],
        [
          'title' => 'Manutenção e Recuperação',
          'desc'  => 'Limpeza, desobstrução e recuperação de poços existentes com garantia de qualidade e prazo.',
        ],
        [
          'title' => 'Análise de Viabilidade Hídrica',
          'desc'  => 'Estudo geológico e hidrogeológico da área antes da perfuração para garantir o sucesso do projeto.',
        ],
        [
          'title' => 'Laudos, CREA e ART',
          'desc'  => 'Toda a documentação regulatória em ordem: CREA, Laudos Técnicos e ART para conformidade legal.',
        ],
        [
          'title' => 'Bombeamento e Automação',
          'desc'  => 'Instalação de bombas submersas e sistemas automatizados de controle de nível e distribuição de água.',
        ],
      ];
      foreach ($services as $s) : ?>
        <div class="service-full-card">
          <h3><?php echo esc_html($s['title']); ?></h3>
          <p><?php echo esc_html($s['desc']); ?></p>
          <a href="<?php echo esc_url(home_url('/contato/')); ?>">Solicitar este serviço &rarr;</a>
        </div>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- ── CTA BANNER ────────────────────────────────────────────────────── -->
<section class="cta-banner">
  <div class="container">
    <h2>Pronto para ter água de qualidade na sua propriedade?</h2>
    <a href="https://wa.me/5534999328198?text=Olá!%20Gostaria%20de%20solicitar%20um%20orçamento."
       class="btn btn--white"
       target="_blank" rel="noopener noreferrer">
      Fale pelo WhatsApp
    </a>
  </div>
</section>

<?php get_footer(); ?>
