<?php get_header(); ?>

<!-- ── HERO ──────────────────────────────────────────────────────────── -->
<section class="hero" style="--hero-bg: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-home.png'); ?>');">
  <div class="hero__overlay"></div>
  <div class="container hero__inner">
    <div class="hero__content">
      <span class="hero__eyebrow">PERFURAÇÃO DE POÇOS ARTESIANOS</span>
      <h1>Água pura e abundante<br>para sua propriedade</h1>
      <p class="hero__subtitle">
        Perfurações até <strong>800 metros</strong>, entrega em até <strong>24 horas</strong>.
        Atendemos residências, fazendas, condomínios e indústrias com documentação completa.
      </p>
      <div class="hero__actions">
        <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="btn btn--primary">Solicitar Orçamento</a>
        <a href="<?php echo esc_url(home_url('/portfolio/')); ?>" class="btn btn--ghost">Ver Portfólio &rarr;</a>
      </div>
      <ul class="hero__trust">
        <li><span class="hero__trust-icon">&#10004;</span> CREA &amp; ART</li>
        <li><span class="hero__trust-icon">&#10004;</span> 4 anos de mercado</li>
        <li><span class="hero__trust-icon">&#10004;</span> Equipe certificada</li>
        <li><span class="hero__trust-icon">&#10004;</span> 6 estados</li>
      </ul>
    </div>
  </div>
</section>

<!-- ── STATS STRIP ───────────────────────────────────────────────────── -->
<section class="home-stats">
  <div class="container">
    <div class="home-stats__grid">
      <?php
      $stats = [
        ['800m', 'Profundidade máxima'],
        ['24h',  'Prazo de entrega'],
        ['6',    'Estados atendidos'],
        ['+200', 'Obras entregues'],
      ];
      foreach ($stats as [$val, $label]) : ?>
        <div class="home-stats__item">
          <span class="home-stats__value"><?php echo esc_html($val); ?></span>
          <span class="home-stats__label"><?php echo esc_html($label); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── SERVIÇOS ──────────────────────────────────────────────────────── -->
<section class="section services-preview">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">O QUE FAZEMOS</span>
      <h2>Soluções completas em captação de água</h2>
      <p>Do estudo geológico à entrega da água na sua caixa: nossa equipe cuida de cada etapa.</p>
    </div>

    <div class="services-grid">
      <?php
      $services = [
        ['Perfuração de Poços',       'Perfurações de até 800 m com maquinário moderno e equipe experiente.', '/servicos/'],
        ['Manutenção e Limpeza',      'Recuperação de vazão, desobstrução e limpeza de poços existentes.',  '/servicos/'],
        ['Análise de Viabilidade',    'Estudo hidrogeológico antes da obra para garantir o sucesso do projeto.', '/servicos/'],
        ['Laudos e Documentação',     'CREA, ART e laudos técnicos: regularização total perante os órgãos.', '/servicos/'],
      ];
      foreach ($services as [$title, $desc, $link]) : ?>
        <article class="service-card">
          <div class="service-card__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.7s6 6.5 6 11a6 6 0 1 1-12 0c0-4.5 6-11 6-11Z"/></svg>
          </div>
          <h3><?php echo esc_html($title); ?></h3>
          <p><?php echo esc_html($desc); ?></p>
          <a href="<?php echo esc_url(home_url($link)); ?>" class="card-link">Saiba mais &rarr;</a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── DIFERENCIAIS ──────────────────────────────────────────────────── -->
<section class="section differentials">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">POR QUE ESCOLHER A ORIGEM</span>
      <h2>Compromisso técnico do início ao fim</h2>
    </div>

    <div class="differentials__grid">
      <?php
      $diffs = [
        ['Documentação Completa', 'CREA, ART e laudos técnicos em todas as obras — conformidade legal garantida.'],
        ['Tecnologia de Ponta',   'Sondas modernas e equipamentos para perfurações de grande profundidade.'],
        ['Equipe Especializada',  'Profissionais treinados em segurança, geologia e operação de máquinas.'],
        ['Prazo e Garantia',      'Entrega em até 24 h após o contato, com garantia escrita do serviço executado.'],
      ];
      foreach ($diffs as [$title, $desc]) : ?>
        <div class="diff-card">
          <span class="diff-card__check" aria-hidden="true">&#10004;</span>
          <h3><?php echo esc_html($title); ?></h3>
          <p><?php echo esc_html($desc); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── CTA FINAL ─────────────────────────────────────────────────────── -->
<section class="home-cta">
  <div class="container home-cta__inner">
    <div>
      <h2>Pronto para ter água de qualidade na sua propriedade?</h2>
      <p>Fale com nossa equipe e receba um orçamento gratuito em até 24 horas.</p>
    </div>
    <div class="home-cta__actions">
      <a href="<?php echo esc_url(home_url('/contato/')); ?>" class="btn btn--white">Solicitar Orçamento</a>
      <a href="https://wa.me/5534999328198?text=Olá!%20Gostaria%20de%20solicitar%20um%20orçamento."
         class="btn btn--whatsapp" target="_blank" rel="noopener noreferrer">
         <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/wap.png'); ?>" alt="" width="20" height="20"> Falar no WhatsApp
      </a>
    </div>
  </div>
</section>

<?php get_footer(); ?>
