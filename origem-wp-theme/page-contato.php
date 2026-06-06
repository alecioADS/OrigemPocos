<?php
/* Template Name: Contato */
get_header();

$msg = $_GET['msg'] ?? '';
?>

<!-- ── HERO BANNER ───────────────────────────────────────────────────── -->
<section class="page-hero">
  <div class="container">
    <h1>Entre em Contato</h1>
    <p>Solicite seu orçamento gratuito agora mesmo</p>
  </div>
</section>

<!-- ── LAYOUT ────────────────────────────────────────────────────────── -->
<section class="section" style="background: var(--bege-claro);">
  <div class="container">
    <div class="contact-layout">

      <!-- Formulário -->
      <div class="contact-form-wrapper">
        <h2>Formulário de Orçamento</h2>
        <p class="form-note">Preencha os dados abaixo e entraremos em contato em breve.</p>

        <?php if ($msg === 'success') : ?>
          <div class="form-message form-message--success">
            Solicitação enviada com sucesso! Entraremos em contato em breve.
          </div>
        <?php elseif ($msg === 'error') : ?>
          <div class="form-message form-message--error">
            Erro ao enviar. Por favor, tente novamente ou entre em contato pelo WhatsApp.
          </div>
        <?php endif; ?>

        <form method="post" action="">
          <?php wp_nonce_field('origem_contact_form', 'origem_contact_nonce'); ?>

          <div class="form-group">
            <label for="contact_nome">Nome completo</label>
            <input type="text" id="contact_nome" name="contact_nome" placeholder="Seu nome completo" required>
          </div>

          <div class="form-group">
            <label for="contact_telefone">Telefone / WhatsApp</label>
            <input type="tel" id="contact_telefone" name="contact_telefone" placeholder="(34) 9 9999-9999" required>
          </div>

          <div class="form-group">
            <label for="contact_endereco">Endereço / Localização da perfuração</label>
            <input type="text" id="contact_endereco" name="contact_endereco" placeholder="Cidade, Estado" required>
          </div>

          <div class="form-group">
            <label for="contact_tipo">Tipo de propriedade</label>
            <select id="contact_tipo" name="contact_tipo">
              <option value="">Selecione...</option>
              <option value="Residência">Residência</option>
              <option value="Fazenda">Fazenda</option>
              <option value="Empresa">Empresa / Indústria</option>
              <option value="Condomínio">Condomínio</option>
              <option value="Outro">Outro</option>
            </select>
          </div>

          <div class="form-group">
            <label for="contact_prof">Profundidade estimada necessária</label>
            <input type="text" id="contact_prof" name="contact_prof" placeholder="Ex: 200 metros, não sei...">
          </div>

          <div class="form-group">
            <label for="contact_obs">Observações adicionais</label>
            <textarea id="contact_obs" name="contact_obs" placeholder="Informações complementares sobre o projeto..."></textarea>
          </div>

          <button type="submit" name="origem_submit" class="btn btn--primary form-submit">
            Enviar Solicitação
          </button>
        </form>
      </div>

      <!-- Informações de contato -->
      <div class="contact-info">
        <h3>Informações de Contato</h3>

        <div class="contact-info-item">
          <p class="info-label">WhatsApp Business</p>
          <p>
            <a href="https://wa.me/5534999328198" target="_blank" rel="noopener noreferrer"
               style="color: var(--verde-claro);">
              (34) 9 9932-8198
            </a>
          </p>
        </div>

        <div class="contact-map-mini">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.0!2d-48.1880!3d-18.6480!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sRua%20Jo%C3%A3o%20dos%20Santos%20Moutinho%2C%20Araguari%2C%20MG!5e0!3m2!1spt-BR!2sbr!4v1"
            width="100%"
            height="200"
            style="border:0; border-radius: 8px;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>

          <p class="info-label">Endereço</p>
          <p>Rua João dos Santos Moutinho<br>Araguari &mdash; MG<br>CEP 38442-194</p>
        </div>

        <div class="contact-info-item">
          <p class="info-label">Área de Atendimento</p>
          <p>MG &middot; GO &middot; SP &middot; DF &middot; MT &middot; BA</p>
        </div>

        <div class="contact-info-item">
          <p class="info-label">Prazo</p>
          <p>Até 24 horas após o contato</p>
        </div>
      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>