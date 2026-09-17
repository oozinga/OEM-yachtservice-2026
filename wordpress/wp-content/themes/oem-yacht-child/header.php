<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="oem-header">
  <div class="oem-header__inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="oem-header__logo">
      <img src="<?php echo esc_url(oem_get_logo_url('red')); ?>" alt="OEM Yacht Service">
    </a>
    <?php
    wp_nav_menu([
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'oem-nav',
      'walker'         => new OEM_Nav_Walker(),
      'fallback_cb'    => false,
    ]);
    ?>
    <div class="oem-header__spacer"></div>
    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="oem-header__cta">Contact</a>
  </div>
</header>
