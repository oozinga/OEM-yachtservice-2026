<?php get_header(); ?>

<main id="content">
<?php while ( have_posts() ) : the_post();
    $tag       = get_post_meta( get_the_ID(), '_oem_tag', true );
    $vessel    = get_post_meta( get_the_ID(), '_oem_vessel', true );
    $delivered = get_post_meta( get_the_ID(), '_oem_delivered', true );
    $facts     = get_post_meta( get_the_ID(), '_oem_facts', true );
    if ( ! is_array( $delivered ) ) $delivered = [];
    if ( ! is_array( $facts ) ) $facts = [];
?>

<section class="oem-page-header" style="padding:72px 0 32px;">
  <div class="oem-section">
    <p class="oem-breadcrumb"><a href="<?php echo get_post_type_archive_link( 'oem_project' ); ?>">Projects</a> / <span><?php the_title(); ?></span></p>
    <?php if ( $tag ) : ?>
      <div class="oem-project-card__tag" style="margin-bottom:12px;"><?php echo esc_html( $tag ); ?></div>
    <?php endif; ?>
    <h1 class="oem-h1 oem-h1--detail"><?php the_title(); ?></h1>
    <?php if ( $vessel ) : ?>
      <p style="font:400 17px/1.65 'Source Sans 3',sans-serif;color:var(--oem-charcoal-70);margin:8px 0 0;"><?php echo esc_html( $vessel ); ?></p>
    <?php endif; ?>
  </div>
</section>

<div class="oem-section" style="margin-bottom:48px;">
  <?php if ( has_post_thumbnail() ) : ?>
    <div style="border-radius:8px;overflow:hidden;"><?php the_post_thumbnail( 'full', ['style' => 'width:100%;height:440px;object-fit:cover;display:block;'] ); ?></div>
  <?php else : ?>
    <div class="oem-image-placeholder oem-image-placeholder--hero"><i class="fa-solid fa-image" style="font-size:48px;"></i></div>
  <?php endif; ?>
</div>

<section class="oem-content-section" style="padding-top:0;">
  <div class="oem-section">
    <div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:48px;align-items:start;">
      <div>
        <h2 style="font:700 26px/1.2 'Source Sans 3',sans-serif;margin:0 0 16px;">The job</h2>
        <div class="oem-body"><?php the_content(); ?></div>

        <?php if ( $delivered ) : ?>
          <h2 style="font:700 26px/1.2 'Source Sans 3',sans-serif;margin:32px 0 16px;">Delivered</h2>
          <ul class="oem-scope-list">
            <?php foreach ( $delivered as $item ) : ?>
              <li><?php echo esc_html( $item ); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <?php if ( $facts ) : ?>
      <div class="oem-fact-sheet">
        <h3 style="font:700 20px/1.3 'Source Sans 3',sans-serif;margin:0 0 20px;">Fact sheet</h3>
        <?php foreach ( $facts as $f ) : ?>
          <div class="oem-fact-sheet__row">
            <span class="oem-fact-sheet__key"><?php echo esc_html( $f['k'] ); ?></span>
            <span class="oem-fact-sheet__val"><?php echo esc_html( $f['v'] ); ?></span>
          </div>
        <?php endforeach; ?>
        <a href="<?php echo home_url( '/contact/' ); ?>" class="oem-btn oem-btn--red" style="margin-top:24px;display:inline-flex;">Discuss a similar scope <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php endwhile; ?>
</main>

<?php get_footer(); ?>
