<?php
/**
 * Fotograaf Portfolio — Elementor Pro Setup Script
 * Run once: wp eval-file wp-content/themes/fotograaf/setup-elementor.php --allow-root
 */

$marker = get_option( 'fotograaf_elementor_setup_done' );
if ( $marker ) {
    echo "Setup already completed. Delete option 'fotograaf_elementor_setup_done' to re-run.\n";
    return;
}

function eid() {
    return substr( md5( uniqid( mt_rand(), true ) ), 0, 7 );
}

// ─── 1. Global Kit ───

$kit_id   = get_option( 'elementor_active_kit' );
$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
if ( ! is_array( $settings ) ) $settings = [];

$settings['system_colors'] = [
    [ '_id' => 'primary',   'title' => 'Primary (zwart)',    'color' => '#000000' ],
    [ '_id' => 'secondary', 'title' => 'Secondary (wit)',    'color' => '#FFFFFF' ],
    [ '_id' => 'text',      'title' => 'Body text',          'color' => '#333333' ],
    [ '_id' => 'accent',    'title' => 'Accent (oranje)',    'color' => '#FF6B35' ],
];

$settings['system_typography'] = [
    [ '_id' => 'primary',   'title' => 'Heading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
      'typography_font_weight' => '700' ],
    [ '_id' => 'secondary', 'title' => 'Subheading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
      'typography_font_weight' => '400' ],
    [ '_id' => 'text',      'title' => 'Body',
      'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
      'typography_font_weight' => '400',
      'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
      'typography_line_height' => [ 'unit' => 'em', 'size' => 1.7 ] ],
    [ '_id' => 'accent',    'title' => 'Caption',
      'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
      'typography_font_weight' => '400' ],
];

$settings['container_width'] = [ 'unit' => 'px', 'size' => 1200 ];
update_post_meta( $kit_id, '_elementor_page_settings', $settings );
echo "1. Global Kit configured.\n";

// ─── 2. Theme Builder Templates ───

function create_template( $title, $type, $conditions, $data ) {
    $id = wp_insert_post([
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_type'   => 'elementor_library',
    ]);
    update_post_meta( $id, '_elementor_template_type', $type );
    update_post_meta( $id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $id, '_elementor_data', wp_json_encode( $data ) );

    $all = get_option( 'elementor_pro_theme_builder_conditions', [] );
    $all[ $type ][ $id ] = $conditions;
    update_option( 'elementor_pro_theme_builder_conditions', $all );
    return $id;
}

// Header
$header_data = [[
    'id' => eid(), 'elType' => 'section',
    'settings' => [
        'layout' => 'full_width', 'structure' => '30',
        'background_background' => 'classic', 'background_color' => '#000000',
        'padding' => [ 'unit' => 'px', 'top' => '20', 'right' => '40', 'bottom' => '20', 'left' => '40' ],
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
              'settings' => [
                  'title' => 'STUDIO', 'header_size' => 'h3',
                  'title_color' => '#FFFFFF',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                  'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 24 ],
                  'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 3 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'nav-menu',
              'settings' => [
                  'menu' => '', 'layout' => 'horizontal', 'align_items' => 'center',
                  'pointer' => 'none',
                  'text_color' => '#FFFFFF',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
                  'typography_font_weight' => '400', 'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                  'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 2 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
              'settings' => [
                  'text' => 'Contact', 'align' => 'right',
                  'link' => [ 'url' => '#contact', 'is_external' => false, 'nofollow' => false ],
                  'background_color' => '#FF6B35', 'button_text_color' => '#FFFFFF',
                  'border_radius' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
                  'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
                  'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                  'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 2 ],
              ], 'elements' => [],
          ]]],
    ],
]];
$header_id = create_template( 'Header', 'header', [ 'include/general' ], $header_data );
echo "2a. Header template created (ID: $header_id).\n";

// Footer
$footer_data = [[
    'id' => eid(), 'elType' => 'section',
    'settings' => [
        'layout' => 'full_width', 'structure' => '30',
        'background_background' => 'classic', 'background_color' => '#000000',
        'padding' => [ 'unit' => 'px', 'top' => '60', 'right' => '40', 'bottom' => '60', 'left' => '40' ],
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                'settings' => [ 'title' => 'STUDIO', 'header_size' => 'h4',
                    'title_color' => '#FFFFFF', 'typography_typography' => 'custom',
                    'typography_font_family' => 'Playfair Display', 'typography_font_weight' => '700',
                    'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 3 ] ],
                'elements' => [] ],
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                'settings' => [ 'editor' => '<p>Capturing moments, creating memories.</p>',
                    'text_color' => 'rgba(255,255,255,0.6)',
                    'typography_typography' => 'custom', 'typography_font_family' => 'Lato', 'typography_font_weight' => '400' ],
                'elements' => [] ],
          ]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#FF6B35;">Navigatie</strong></p><p><a href="/" style="color:#fff;">Home</a></p><p><a href="/portfolio/" style="color:#fff;">Portfolio</a></p><p><a href="#contact" style="color:#fff;">Contact</a></p>' ],
              'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#FF6B35;">Contact</strong></p><p style="color:#fff;">info@studio-fotografie.nl</p><p style="color:#fff;">+31 6 12 34 56 78</p><p style="color:#fff;">Amsterdam, Nederland</p>' ],
              'elements' => [],
          ]]],
    ],
]];
$footer_id = create_template( 'Footer', 'footer', [ 'include/general' ], $footer_data );
echo "2b. Footer template created (ID: $footer_id).\n";

// Single Portfolio Template
$single_data = [
    // Hero with featured image
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width', 'structure' => '10',
            'min_height' => [ 'unit' => 'vh', 'size' => 60 ],
            'background_background' => 'classic', 'background_color' => '#111111',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'theme-post-featured-image',
                    'settings' => [
                        'image_size' => 'full',
                        'border_radius' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
                    ], 'elements' => [] ],
              ]],
        ],
    ],
    // Title + meta
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20',
            'padding' => [ 'unit' => 'px', 'top' => '60', 'right' => '40', 'bottom' => '60', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 66 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => '', 'header_size' => 'h1',
                        'title_color' => '#000000',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 48 ],
                        '__dynamic__' => [ 'title' => '[elementor-tag id="" name="post-title"]' ],
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'theme-post-content',
                    'settings' => [
                        'text_color' => '#333333',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
                        'typography_font_weight' => '400',
                    ], 'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-folder', 'library' => 'fa-solid' ],
                        'title_text' => 'Categorie', 'position' => 'left', 'primary_color' => '#FF6B35',
                        'description_text' => '',
                        '__dynamic__' => [ 'description_text' => '[elementor-tag id="" name="post-custom-field" settings="%7B%22key%22%3A%22_portfolio_categorie%22%7D"]' ],
                        'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Lato', 'title_typography_font_weight' => '700',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-camera', 'library' => 'fa-solid' ],
                        'title_text' => 'Camera', 'position' => 'left', 'primary_color' => '#FF6B35',
                        'description_text' => '',
                        '__dynamic__' => [ 'description_text' => '[elementor-tag id="" name="post-custom-field" settings="%7B%22key%22%3A%22_portfolio_camera%22%7D"]' ],
                        'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Lato', 'title_typography_font_weight' => '700',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid' ],
                        'title_text' => 'Locatie', 'position' => 'left', 'primary_color' => '#FF6B35',
                        'description_text' => '',
                        '__dynamic__' => [ 'description_text' => '[elementor-tag id="" name="post-custom-field" settings="%7B%22key%22%3A%22_portfolio_locatie%22%7D"]' ],
                        'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Lato', 'title_typography_font_weight' => '700',
                    ], 'elements' => [] ],
              ]],
        ],
    ],
];
$single_id = create_template( 'Single Portfolio', 'single', [ 'include/singular/portfolio_item' ], $single_data );
echo "2c. Single Portfolio template created (ID: $single_id).\n";

// Archive Portfolio Template
$archive_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '40', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                  'settings' => [
                      'title' => 'Portfolio', 'header_size' => 'h1', 'align' => 'center',
                      'title_color' => '#000000',
                      'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                      'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 48 ],
                  ], 'elements' => [],
              ]]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'posts',
                  'settings' => [
                      'posts_post_type' => 'portfolio_item',
                      'posts_per_page' => 9,
                      'columns' => '3',
                      'thumbnail' => 'yes',
                      'thumbnail_size_size' => 'medium_large',
                      'title' => 'yes',
                      'excerpt' => 'no',
                      'meta_data' => [ 'none' ],
                      'read_more' => 'yes',
                      'read_more_text' => 'View project',
                      'pagination_type' => 'numbers',
                  ], 'elements' => [],
              ]]],
        ],
    ],
];
$archive_id = create_template( 'Archive Portfolio', 'archive', [ 'include/archive/portfolio_item' ], $archive_data );
echo "2d. Archive Portfolio template created (ID: $archive_id).\n";

// ─── 3. Pages ───

function create_or_update_page( $title, $slug, $elementor_data ) {
    $page = get_page_by_path( $slug );
    if ( $page ) {
        $post_id = $page->ID;
    } else {
        $post_id = wp_insert_post([
            'post_title'  => $title,
            'post_name'   => $slug,
            'post_status' => 'publish',
            'post_type'   => 'page',
        ]);
    }
    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
    update_post_meta( $post_id, '_elementor_page_settings', [ 'hide_title' => 'yes' ] );
    update_post_meta( $post_id, '_elementor_data', wp_json_encode( $elementor_data ) );
    delete_post_meta( $post_id, '_elementor_css' );
    return $post_id;
}

// ── Homepage ──

$home_data = [
    // Hero
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width', 'structure' => '20',
            'min_height' => [ 'unit' => 'vh', 'size' => 90 ],
            'background_background' => 'classic', 'background_color' => '#000000',
            'padding' => [ 'unit' => 'px', 'top' => '120', 'right' => '60', 'bottom' => '120', 'left' => '60' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Moments Worth Remembering', 'header_size' => 'h1',
                        'title_color' => '#FFFFFF',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 56 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.1 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p>Professional photography capturing the beauty in everyday life. From portraits to landscapes, every image tells a story.</p>',
                        'text_color' => 'rgba(255,255,255,0.7)',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Lato',
                        'typography_font_weight' => '400', 'typography_font_size' => [ 'unit' => 'px', 'size' => 18 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
                    'settings' => [ 'text' => 'Bekijk portfolio',
                        'link' => [ 'url' => '/portfolio/', 'is_external' => false, 'nofollow' => false ],
                        'background_color' => '#FF6B35', 'button_text_color' => '#FFFFFF',
                        'border_radius' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
                        'typography_typography' => 'custom', 'typography_font_family' => 'Lato', 'typography_font_weight' => '700',
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 2 ] ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [ 'image' => [ 'url' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=600', 'id' => '' ],
                        'image_size' => 'full' ],
                    'elements' => [] ],
              ]],
        ],
    ],
    // Portfolio grid section
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '40', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                  'settings' => [ 'title' => 'Recent Work', 'header_size' => 'h2', 'align' => 'center',
                      'title_color' => '#000000',
                      'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                      'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                  'elements' => [],
              ]]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'posts',
                  'settings' => [
                      'posts_post_type' => 'portfolio_item',
                      'posts_per_page' => 6,
                      'columns' => '3',
                      'thumbnail' => 'yes',
                      'thumbnail_size_size' => 'medium_large',
                      'title' => 'yes',
                      'excerpt' => 'no',
                      'meta_data' => [ 'none' ],
                      'read_more' => 'yes',
                      'read_more_text' => 'View project',
                      'pagination_type' => 'none',
                  ], 'elements' => [],
              ]]],
        ],
    ],
    // About section
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20',
            'background_background' => 'classic', 'background_color' => '#F5F5F5',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '60', 'bottom' => '80', 'left' => '60' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [ 'image' => [ 'url' => 'https://images.unsplash.com/photo-1554048612-b6a482bc67e5?w=500', 'id' => '' ],
                        'image_size' => 'full' ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Over de fotograaf', 'header_size' => 'h2',
                        'title_color' => '#000000',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Playfair Display',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p>Met meer dan 10 jaar ervaring in professionele fotografie, leg ik de mooiste momenten vast. Mijn stijl combineert natuurlijk licht met een oog voor detail.</p><p>Of het nu gaat om portretfotografie, evenementen, of commercieel werk — elk project benader ik met passie en precisie.</p>',
                        'text_color' => '#333333',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Lato', 'typography_font_weight' => '400' ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
                    'settings' => [ 'text' => 'Neem contact op',
                        'link' => [ 'url' => '#contact', 'is_external' => false, 'nofollow' => false ],
                        'background_color' => '#FF6B35', 'button_text_color' => '#FFFFFF',
                        'border_radius' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
                        'typography_typography' => 'custom', 'typography_font_family' => 'Lato', 'typography_font_weight' => '700' ],
                    'elements' => [] ],
              ]],
        ],
    ],
];

$home_id = create_or_update_page( 'Home', 'home', $home_data );
echo "3a. Home page created (ID: $home_id).\n";

// ─── 4. WordPress Settings ───

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
flush_rewrite_rules();
echo "4. WordPress settings configured.\n";

// ─── 5. Clear CSS cache ───

delete_post_meta_by_key( '_elementor_css' );
echo "5. Elementor CSS cache cleared.\n";

update_option( 'fotograaf_elementor_setup_done', true );
echo "\nDone! Created:\n";
echo "- Header template (ID: $header_id)\n";
echo "- Footer template (ID: $footer_id)\n";
echo "- Single Portfolio template (ID: $single_id)\n";
echo "- Archive Portfolio template (ID: $archive_id)\n";
echo "- Home (ID: $home_id)\n";
echo "\nOpen any page in Elementor editor to start customizing.\n";
