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

// --- Adobe Fonts (Acumin Pro 400/700) ---

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

// --- Custom Post Type: Team Members ---

add_action( 'init', function() {
    register_post_type( 'oem_team_member', [
        'labels' => [
            'name'               => 'Team Members',
            'singular_name'      => 'Team Member',
            'add_new'            => 'Add team member',
            'add_new_item'       => 'Add new team member',
            'edit_item'          => 'Edit team member',
            'new_item'           => 'New team member',
            'view_item'          => 'View team member',
            'search_items'       => 'Search team members',
            'not_found'          => 'No team members found',
            'not_found_in_trash' => 'No team members in trash',
            'all_items'          => 'All team members',
            'menu_name'          => 'Team Members',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'team', 'with_front' => false],
        'menu_icon'    => 'dashicons-groups',
        'supports'     => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'show_in_rest' => true,
    ]);
});

// --- Team Member Meta Box ---

add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'oem_team_member_fields',
        'Team member details',
        'oem_team_member_meta_box_render',
        'oem_team_member',
        'normal',
        'high'
    );
});

function oem_team_member_meta_box_render( $post ) {
    wp_nonce_field( 'oem_team_member_meta', 'oem_team_member_nonce' );
    $department = get_post_meta( $post->ID, '_oem_team_department', true );
    $role       = get_post_meta( $post->ID, '_oem_team_role', true );
    $base       = get_post_meta( $post->ID, '_oem_team_base', true );
    $quote      = get_post_meta( $post->ID, '_oem_team_quote', true );
    $expertise  = get_post_meta( $post->ID, '_oem_team_expertise', true );
    $facts      = get_post_meta( $post->ID, '_oem_team_facts', true );
    if ( ! is_array( $expertise ) ) $expertise = [];
    if ( ! is_array( $facts ) ) $facts = array_fill( 0, 5, ['k' => '', 'v' => ''] );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="oem_team_department">Department</label></th>
            <td>
                <select id="oem_team_department" name="oem_team_department">
                    <option value="">— Select —</option>
                    <option value="Management &amp; Engineering" <?php selected( $department, 'Management & Engineering' ); ?>>Management & Engineering</option>
                    <option value="Field Teams &amp; Specialists" <?php selected( $department, 'Field Teams & Specialists' ); ?>>Field Teams & Specialists</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="oem_team_role">Role / Specialisation</label></th>
            <td><input type="text" id="oem_team_role" name="oem_team_role" value="<?php echo esc_attr( $role ); ?>" class="regular-text" placeholder="e.g. Control Technology, Hydraulics"></td>
        </tr>
        <tr>
            <th><label for="oem_team_base">Base / Location</label></th>
            <td><input type="text" id="oem_team_base" name="oem_team_base" value="<?php echo esc_attr( $base ); ?>" class="regular-text" placeholder="e.g. Bolsward, Field Europe"></td>
        </tr>
        <tr>
            <th><label for="oem_team_quote">Personal quote</label></th>
            <td><textarea id="oem_team_quote" name="oem_team_quote" rows="2" class="large-text" placeholder="e.g. Every vessel deserves the same standard of care."><?php echo esc_textarea( $quote ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="oem_team_expertise">Expertise</label></th>
            <td>
                <textarea id="oem_team_expertise" name="oem_team_expertise" rows="5" class="large-text" placeholder="One item per line"><?php echo esc_textarea( implode( "\n", $expertise ) ); ?></textarea>
                <p class="description">One expertise area per line.</p>
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
                    <input type="text" name="oem_team_facts_k[]" value="<?php echo esc_attr( $fk ); ?>" placeholder="Label" style="width:140px;">
                    <input type="text" name="oem_team_facts_v[]" value="<?php echo esc_attr( $fv ); ?>" placeholder="Value" style="flex:1;">
                </div>
                <?php endfor; ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_oem_team_member', function( $post_id ) {
    if ( ! isset( $_POST['oem_team_member_nonce'] ) || ! wp_verify_nonce( $_POST['oem_team_member_nonce'], 'oem_team_member_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    $text_fields = ['oem_team_department', 'oem_team_role', 'oem_team_base'];
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[$field] ) )
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[$field] ) );
    }
    if ( isset( $_POST['oem_team_quote'] ) )
        update_post_meta( $post_id, '_oem_team_quote', sanitize_textarea_field( $_POST['oem_team_quote'] ) );
    if ( isset( $_POST['oem_team_expertise'] ) ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", sanitize_textarea_field( $_POST['oem_team_expertise'] ) ) ) );
        update_post_meta( $post_id, '_oem_team_expertise', array_values( $lines ) );
    }
    if ( isset( $_POST['oem_team_facts_k'] ) && isset( $_POST['oem_team_facts_v'] ) ) {
        $facts = [];
        foreach ( $_POST['oem_team_facts_k'] as $i => $k ) {
            $k = sanitize_text_field( $k );
            $v = sanitize_text_field( $_POST['oem_team_facts_v'][$i] ?? '' );
            if ( $k || $v ) $facts[] = ['k' => $k, 'v' => $v];
        }
        update_post_meta( $post_id, '_oem_team_facts', $facts );
    }
});

// Sort team members by menu_order on archive
add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'oem_team_member' ) ) {
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

// --- Shortcodes for Team Members ---

add_shortcode( 'oem_team_expertise', function() {
    $expertise = get_post_meta( get_the_ID(), '_oem_team_expertise', true );
    if ( ! is_array( $expertise ) || empty( $expertise ) ) return '';
    $html = '<ul style="list-style:none;padding:0;margin:0;">';
    foreach ( $expertise as $item ) {
        $html .= '<li style="display:flex;gap:12px;padding:8px 0;font:400 15.5px/1.55 \'acumin-pro\',sans-serif;color:#4D4D54;">';
        $html .= '<span style="width:16px;height:2px;background:#E40034;margin-top:10px;flex-shrink:0;"></span>';
        $html .= esc_html( $item ) . '</li>';
    }
    $html .= '</ul>';
    return $html;
});

add_shortcode( 'oem_team_facts', function() {
    $facts = get_post_meta( get_the_ID(), '_oem_team_facts', true );
    if ( ! is_array( $facts ) || empty( $facts ) ) return '';
    $html = '<div style="border-top:4px solid #E40034;padding-top:24px;">';
    foreach ( $facts as $f ) {
        $html .= '<div style="padding:10px 0;border-bottom:1px solid #ECECEE;">';
        $html .= '<div style="font:600 12px/1 \'acumin-pro\',sans-serif;letter-spacing:.06em;text-transform:uppercase;color:#7A7A82;margin-bottom:4px;">' . esc_html( $f['k'] ) . '</div>';
        $html .= '<div style="font:400 15.5px/1.4 \'acumin-pro\',sans-serif;color:#1B1B1F;">' . esc_html( $f['v'] ) . '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
});

add_shortcode( 'oem_team_quote', function() {
    $quote = get_post_meta( get_the_ID(), '_oem_team_quote', true );
    if ( empty( $quote ) ) return '';
    return '<p style="font:italic 400 18px/1.6 \'acumin-pro\',sans-serif;color:#E40034;margin:24px 0 0;">&ldquo;' . esc_html( $quote ) . '&rdquo;</p>';
});

add_shortcode( 'oem_team_grid', function( $atts ) {
    $atts = shortcode_atts( ['department' => ''], $atts );
    $args = [
        'post_type'      => 'oem_team_member',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ];
    if ( $atts['department'] ) {
        $args['meta_query'] = [[
            'key'   => '_oem_team_department',
            'value' => $atts['department'],
        ]];
    }
    $members = get_posts( $args );
    if ( empty( $members ) ) return '';

    $html  = '<div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:32px 24px;">';
    $html .= '<style>';
    $html .= '@media(max-width:900px){.oem-tg{grid-template-columns:repeat(3,minmax(0,1fr))!important}}';
    $html .= '@media(max-width:640px){.oem-tg{grid-template-columns:repeat(2,minmax(0,1fr))!important}}';
    $html .= '.oem-tc:hover .oem-tp{opacity:.85}';
    $html .= '</style>';
    $html  = '<div class="oem-tg" style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:32px 24px;">';

    foreach ( $members as $m ) {
        $role = get_post_meta( $m->ID, '_oem_team_role', true );
        $base = get_post_meta( $m->ID, '_oem_team_base', true );
        $link = get_permalink( $m->ID );
        $thumb = get_the_post_thumbnail( $m->ID, 'medium_large', ['style' => 'width:100%;height:100%;object-fit:cover;display:block;'] );
        $photo = $thumb ?: '<div style="width:48px;height:48px;border-radius:50%;background:#7A7A82;opacity:.3;"></div>';

        $html .= '<a href="' . esc_url( $link ) . '" class="oem-tc" style="text-decoration:none;text-align:left;">';
        $html .= '<div class="oem-tp" style="width:100%;aspect-ratio:4/5;background:#ECECEE;display:flex;align-items:center;justify-content:center;margin-bottom:14px;overflow:hidden;transition:opacity .2s;">' . $photo . '</div>';
        $html .= '<div style="font:600 17px/1.35 \'acumin-pro\',sans-serif;color:#1B1B1F;margin:0 0 3px;">' . esc_html( $m->post_title ) . '</div>';
        if ( $role ) $html .= '<div style="font:500 13.5px/1.4 \'acumin-pro\',sans-serif;color:#E40034;margin:0 0 1px;">' . esc_html( $role ) . '</div>';
        if ( $base ) $html .= '<div style="font:400 13.5px/1.4 \'acumin-pro\',sans-serif;color:#7A7A82;margin:0;">' . esc_html( $base ) . '</div>';
        $html .= '</a>';
    }
    $html .= '</div>';
    return $html;
});
