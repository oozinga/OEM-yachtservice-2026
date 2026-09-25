<?php
/**
 * Tandarts Praktijk — Elementor Pro child theme functions.
 */

add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    register_nav_menus([ 'primary' => 'Primary Navigation' ]);
});

add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap',
        [], null );
});
