<?php
/**
 * Fotograaf Portfolio — Elementor Pro child theme functions.
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
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@400;700&display=swap',
        [], null );
});

// ─── Custom Post Type: Portfolio Item ───

add_action( 'init', function() {
    register_post_type( 'portfolio_item', [
        'labels' => [
            'name'          => 'Portfolio',
            'singular_name' => 'Portfolio Item',
            'add_new_item'  => 'Add New Portfolio Item',
            'edit_item'     => 'Edit Portfolio Item',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => [ 'slug' => 'portfolio' ],
        'menu_icon'    => 'dashicons-camera',
        'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'show_in_rest' => true,
    ]);
});

// ─── Meta Box for Portfolio Fields ───

add_action( 'add_meta_boxes', function() {
    add_meta_box( 'portfolio_details', 'Portfolio Details', function( $post ) {
        wp_nonce_field( 'portfolio_meta', 'portfolio_meta_nonce' );
        $categorie = get_post_meta( $post->ID, '_portfolio_categorie', true );
        $camera    = get_post_meta( $post->ID, '_portfolio_camera', true );
        $locatie   = get_post_meta( $post->ID, '_portfolio_locatie', true );
        ?>
        <p><label>Categorie:<br>
            <input type="text" name="_portfolio_categorie" value="<?php echo esc_attr( $categorie ); ?>" class="widefat">
        </label></p>
        <p><label>Camera:<br>
            <input type="text" name="_portfolio_camera" value="<?php echo esc_attr( $camera ); ?>" class="widefat">
        </label></p>
        <p><label>Locatie:<br>
            <input type="text" name="_portfolio_locatie" value="<?php echo esc_attr( $locatie ); ?>" class="widefat">
        </label></p>
        <?php
    }, 'portfolio_item', 'normal', 'high' );
});

add_action( 'save_post_portfolio_item', function( $post_id ) {
    if ( ! isset( $_POST['portfolio_meta_nonce'] ) ||
         ! wp_verify_nonce( $_POST['portfolio_meta_nonce'], 'portfolio_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    foreach ( [ '_portfolio_categorie', '_portfolio_camera', '_portfolio_locatie' ] as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
        }
    }
});
