<footer class="site-footer">
  <div class="container">
    <div class="footer-inner">
      <p class="footer-brand">ORIGEM POÇOS ARTESIANOS</p>
      <p class="footer-coverage">Atuação em: MG &middot; GO &middot; SP &middot; DF &middot; MT &middot; BA</p>
      <p class="footer-tagline">Perfurações até 800 m &middot; Entrega em até 24h &middot; CREA, ART e laudos</p>
      <p class="footer-copy">&copy; <?php echo esc_html(date('Y')); ?> Origem Poços Artesianos &mdash; Todos os direitos reservados</p>
    </div>
  </div>
</footer>

<a href="https://wa.me/5534999328198?text=Olá!%20Gostaria%20de%20solicitar%20um%20orçamento."
   class="whatsapp-float"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="<?php esc_attr_e('Falar pelo WhatsApp', 'origem'); ?>">
  <img
    src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/wap.png'); ?>"
    alt=""
    width="64" height="64"
    loading="lazy"
    decoding="async"
  >
</a>

<?php wp_footer(); ?>
</body>
</html>
