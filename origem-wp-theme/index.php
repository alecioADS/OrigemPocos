<?php get_header(); ?>

<section class="section">
  <div class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <h1><?php the_title(); ?></h1>
      <div><?php the_content(); ?></div>
    <?php endwhile; else : ?>
      <p>Nenhum conteúdo encontrado.</p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
