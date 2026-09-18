<?php
/**
 * OEM Yacht Service — Elementor Pro child theme functions.
 * Only CPT registration, meta box, and two shortcodes for array-type meta.
 */

// --- Theme Setup ---

add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    register_nav_menus([
        'primary' => 'Primary Navigation',
    ]);
});

add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

// --- Adobe Fonts (Acumin Pro 300/400/700) ---

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/bbm2zev.css', [], null );
});

// --- Custom Post Type: Projects ---

add_action( 'init', function() {
    register_post_type( 'oem_project', [
        'labels' => [
            'name'               => 'Projects',
            'singular_name'      => 'Project',
            'add_new'            => 'Add project',
            'add_new_item'       => 'Add new project',
            'edit_item'          => 'Edit project',
            'new_item'           => 'New project',
            'view_item'          => 'View project',
            'search_items'       => 'Search projects',
            'not_found'          => 'No projects found',
            'not_found_in_trash' => 'No projects in trash',
            'all_items'          => 'All projects',
            'menu_name'          => 'Projects',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'projects', 'with_front' => false],
        'menu_icon'    => 'dashicons-portfolio',
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
});

// --- Meta Box ---

add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'oem_project_fields',
        'Project details',
        'oem_project_meta_box_render',
        'oem_project',
        'normal',
        'high'
    );
});

function oem_project_meta_box_render( $post ) {
    wp_nonce_field( 'oem_project_meta', 'oem_project_nonce' );
    $tag       = get_post_meta( $post->ID, '_oem_tag', true );
    $vessel    = get_post_meta( $post->ID, '_oem_vessel', true );
    $delivered = get_post_meta( $post->ID, '_oem_delivered', true );
    $facts     = get_post_meta( $post->ID, '_oem_facts', true );
    if ( ! is_array( $delivered ) ) $delivered = [];
    if ( ! is_array( $facts ) ) $facts = array_fill( 0, 5, ['k' => '', 'v' => ''] );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="oem_tag">Tag</label></th>
            <td><input type="text" id="oem_tag" name="oem_tag" value="<?php echo esc_attr( $tag ); ?>" class="regular-text" placeholder="e.g. Refit, Service, Newbuild support"></td>
        </tr>
        <tr>
            <th><label for="oem_vessel">Vessel</label></th>
            <td><input type="text" id="oem_vessel" name="oem_vessel" value="<?php echo esc_attr( $vessel ); ?>" class="large-text" placeholder="e.g. 85 m fleet support vessel, Northern Europe"></td>
        </tr>
        <tr>
            <th><label for="oem_delivered">Delivered</label></th>
            <td>
                <textarea id="oem_delivered" name="oem_delivered" rows="5" class="large-text" placeholder="One item per line"><?php echo esc_textarea( implode( "\n", $delivered ) ); ?></textarea>
                <p class="description">One deliverable per line.</p>
            </td>
        </tr>
        <tr>
            <th>Fact sheet</th>
            <td>
                <?php for ( $i = 0; $i < 5; $i++ ) :
                    $fk = $facts[$i]['k'] ?? '';
                    $fv = $facts[$i]['v'] ?? '';
                ?>
                <div style="display:flex;gap:8px;margin-bottom:6px;">
                    <input type="text" name="oem_facts_k[]" value="<?php echo esc_attr( $fk ); ?>" placeholder="Label" style="width:140px;">
                    <input type="text" name="oem_facts_v[]" value="<?php echo esc_attr( $fv ); ?>" placeholder="Value" style="flex:1;">
                </div>
                <?php endfor; ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_oem_project', function( $post_id ) {
    if ( ! isset( $_POST['oem_project_nonce'] ) || ! wp_verify_nonce( $_POST['oem_project_nonce'], 'oem_project_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    if ( isset( $_POST['oem_tag'] ) )
        update_post_meta( $post_id, '_oem_tag', sanitize_text_field( $_POST['oem_tag'] ) );
    if ( isset( $_POST['oem_vessel'] ) )
        update_post_meta( $post_id, '_oem_vessel', sanitize_text_field( $_POST['oem_vessel'] ) );
    if ( isset( $_POST['oem_delivered'] ) ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", sanitize_textarea_field( $_POST['oem_delivered'] ) ) ) );
        update_post_meta( $post_id, '_oem_delivered', array_values( $lines ) );
    }
    if ( isset( $_POST['oem_facts_k'] ) && isset( $_POST['oem_facts_v'] ) ) {
        $facts = [];
        foreach ( $_POST['oem_facts_k'] as $i => $k ) {
            $k = sanitize_text_field( $k );
            $v = sanitize_text_field( $_POST['oem_facts_v'][$i] ?? '' );
            if ( $k || $v ) $facts[] = ['k' => $k, 'v' => $v];
        }
        update_post_meta( $post_id, '_oem_facts', $facts );
    }
});

// Sort projects by menu_order on archive
add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'oem_project' ) ) {
        $query->set( 'orderby', 'menu_order' );
        $query->set( 'order', 'ASC' );
        $query->set( 'posts_per_page', -1 );
    }
});

// --- Shortcodes for array-type project meta (used in Elementor Single template) ---

add_shortcode( 'oem_delivered', function() {
    $delivered = get_post_meta( get_the_ID(), '_oem_delivered', true );
    if ( ! is_array( $delivered ) || empty( $delivered ) ) return '';
    $html = '<ul style="list-style:none;padding:0;margin:0;">';
    foreach ( $delivered as $item ) {
        $html .= '<li style="padding:8px 0 8px 20px;border-bottom:1px solid #ECECEE;position:relative;font:400 16px/1.6 \'acumin-pro\',sans-serif;color:#4D4D54;">';
        $html .= '<span style="position:absolute;left:0;color:#E40034;">&#x2022;</span>';
        $html .= esc_html( $item ) . '</li>';
    }
    $html .= '</ul>';
    return $html;
});

add_shortcode( 'oem_facts', function() {
    $facts = get_post_meta( get_the_ID(), '_oem_facts', true );
    if ( ! is_array( $facts ) || empty( $facts ) ) return '';
    $html = '';
    foreach ( $facts as $f ) {
        $html .= '<div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #ECECEE;">';
        $html .= '<span style="font:700 15px/1.4 \'acumin-pro\',sans-serif;color:#1B1B1F;">' . esc_html( $f['k'] ) . '</span>';
        $html .= '<span style="font:400 15px/1.4 \'acumin-pro\',sans-serif;color:#4D4D54;">' . esc_html( $f['v'] ) . '</span>';
        $html .= '</div>';
    }
    return $html;
});
