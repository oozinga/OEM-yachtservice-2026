<?php get_header(); ?>

<main id="content">

<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">Projects</div>
    <h1 class="oem-h1 oem-h1--page">Selected work.</h1>
    <p class="oem-intro">Eight recent projects that illustrate our range — from single-system service calls to multi-discipline refits and fleet-wide digital rollouts.</p>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <?php if ( have_posts() ) : ?>
    <div class="oem-card-grid">
      <?php while ( have_posts() ) : the_post();
        $tag    = get_post_meta( get_the_ID(), '_oem_tag', true );
        $vessel = get_post_meta( get_the_ID(), '_oem_vessel', true );
      ?>
      <a href="<?php the_permalink(); ?>" class="oem-project-card">
        <?php if ( has_post_thumbnail() ) : ?>
          <div class="oem-project-card__image"><?php the_post_thumbnail( 'medium_large' ); ?></div>
        <?php else : ?>
          <div class="oem-project-card__image"><i class="fa-solid fa-image"></i></div>
        <?php endif; ?>
        <div class="oem-project-card__tag"><?php echo esc_html( $tag ); ?></div>
        <h3 class="oem-project-card__title"><?php the_title(); ?></h3>
        <p class="oem-project-card__vessel"><?php echo esc_html( $vessel ); ?></p>
      </a>
      <?php endwhile; ?>
    </div>
    <?php else : ?>
      <p>No projects found.</p>
    <?php endif; ?>
  </div>
</section>

</main>

<?php get_footer(); ?>
