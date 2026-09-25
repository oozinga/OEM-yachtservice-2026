<?php
/**
 * Tandarts Praktijk — Elementor Pro Setup Script
 * Run once: wp eval-file wp-content/themes/tandarts/setup-elementor.php --allow-root
 */

$marker = get_option( 'tandarts_elementor_setup_done' );
if ( $marker ) {
    echo "Setup already completed. Delete option 'tandarts_elementor_setup_done' to re-run.\n";
    return;
}

function eid() {
    return substr( md5( uniqid( mt_rand(), true ) ), 0, 7 );
}

$theme_url = get_stylesheet_directory_uri();

// ─── 1. Global Kit ───

$kit_id   = get_option( 'elementor_active_kit' );
$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
if ( ! is_array( $settings ) ) $settings = [];

$settings['system_colors'] = [
    [ '_id' => 'primary',   'title' => 'Primary (donkerblauw)', 'color' => '#1A3A5C' ],
    [ '_id' => 'secondary', 'title' => 'Accent (mintgroen)',    'color' => '#4ECDC4' ],
    [ '_id' => 'text',      'title' => 'Body text',             'color' => '#4A4A4A' ],
    [ '_id' => 'accent',    'title' => 'Light BG',              'color' => '#F4F9F8' ],
];

$settings['system_typography'] = [
    [ '_id' => 'primary',   'title' => 'Heading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
      'typography_font_weight' => '700' ],
    [ '_id' => 'secondary', 'title' => 'Subheading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
      'typography_font_weight' => '600' ],
    [ '_id' => 'text',      'title' => 'Body',
      'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
      'typography_font_weight' => '400',
      'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
      'typography_line_height' => [ 'unit' => 'em', 'size' => 1.7 ] ],
    [ '_id' => 'accent',    'title' => 'Caption',
      'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
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
        'background_background' => 'classic', 'background_color' => '#FFFFFF',
        'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '40', 'bottom' => '16', 'left' => '40' ],
        'border_border' => 'solid',
        'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0' ],
        'border_color' => '#E8E8E8',
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
              'settings' => [
                  'title' => 'Tandarts Praktijk', 'header_size' => 'h3',
                  'title_color' => '#1A3A5C',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
                  'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 22 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'nav-menu',
              'settings' => [
                  'menu' => '', 'layout' => 'horizontal', 'align_items' => 'center',
                  'pointer' => 'none',
                  'text_color' => '#1A3A5C',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
                  'typography_font_weight' => '600', 'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
              'settings' => [
                  'text' => 'Afspraak maken', 'align' => 'right',
                  'link' => [ 'url' => '/contact/', 'is_external' => false, 'nofollow' => false ],
                  'background_color' => '#4ECDC4', 'button_text_color' => '#FFFFFF',
                  'border_radius' => [ 'unit' => 'px', 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6' ],
                  'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
                  'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
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
        'background_background' => 'classic', 'background_color' => '#1A3A5C',
        'padding' => [ 'unit' => 'px', 'top' => '60', 'right' => '40', 'bottom' => '60', 'left' => '40' ],
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                'settings' => [ 'title' => 'Tandarts Praktijk', 'header_size' => 'h4',
                    'title_color' => '#FFFFFF', 'typography_typography' => 'custom',
                    'typography_font_family' => 'Inter', 'typography_font_weight' => '700' ],
                'elements' => [] ],
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                'settings' => [ 'editor' => '<p>Uw lach in goede handen.</p>', 'text_color' => 'rgba(255,255,255,0.7)',
                    'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '400' ],
                'elements' => [] ],
          ]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#4ECDC4;">Navigatie</strong></p><p><a href="/" style="color:#fff;">Home</a></p><p><a href="/over-ons/" style="color:#fff;">Over ons</a></p><p><a href="/behandelingen/" style="color:#fff;">Behandelingen</a></p><p><a href="/contact/" style="color:#fff;">Contact</a></p>' ],
              'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#4ECDC4;">Contact</strong></p><p style="color:#fff;">Keizersgracht 123<br>1015 AA Amsterdam</p><p style="color:#fff;">020 123 4567<br>info@tandartspraktijk.nl</p>' ],
              'elements' => [],
          ]]],
    ],
]];
$footer_id = create_template( 'Footer', 'footer', [ 'include/general' ], $footer_data );
echo "2b. Footer template created (ID: $footer_id).\n";

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
    // Hero section
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width', 'structure' => '20',
            'min_height' => [ 'unit' => 'vh', 'size' => 80 ],
            'background_background' => 'classic', 'background_color' => '#1A3A5C',
            'padding' => [ 'unit' => 'px', 'top' => '100', 'right' => '60', 'bottom' => '100', 'left' => '60' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Uw lach, onze zorg', 'header_size' => 'h1',
                        'title_color' => '#FFFFFF',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 52 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.1 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p>Wij bieden moderne tandheelkunde in een ontspannen sfeer. Van controle tot cosmetische behandelingen — uw glimlach is bij ons in goede handen.</p>',
                        'text_color' => 'rgba(255,255,255,0.85)',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Inter',
                        'typography_font_weight' => '400', 'typography_font_size' => [ 'unit' => 'px', 'size' => 18 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
                    'settings' => [ 'text' => 'Maak een afspraak',
                        'link' => [ 'url' => '/contact/', 'is_external' => false, 'nofollow' => false ],
                        'background_color' => '#4ECDC4', 'button_text_color' => '#FFFFFF',
                        'border_radius' => [ 'unit' => 'px', 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6' ],
                        'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '700' ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [ 'image' => [ 'url' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=600', 'id' => '' ],
                        'image_size' => 'full', 'border_radius' => [ 'unit' => 'px', 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12' ] ],
                    'elements' => [] ],
              ]],
        ],
    ],
    // Services section (3 cards)
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '30',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Onze diensten', 'header_size' => 'h2', 'align' => 'center',
                        'title_color' => '#1A3A5C',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ] ],
                    'elements' => [] ],
              ]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '30', 'gap' => 'extended',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '24', 'bottom' => '80', 'left' => '24' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-tooth', 'library' => 'fa-solid' ],
                      'title_text' => 'Algemene tandheelkunde',
                      'description_text' => 'Controles, vullingen en preventieve zorg voor een gezond gebit.',
                      'link' => [ 'url' => '/behandelingen/' ],
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                      'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Inter', 'title_typography_font_weight' => '700',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-smile-beam', 'library' => 'fa-solid' ],
                      'title_text' => 'Cosmetische behandelingen',
                      'description_text' => 'Whitening, veneers en facings voor een stralende glimlach.',
                      'link' => [ 'url' => '/behandelingen/' ],
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                      'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Inter', 'title_typography_font_weight' => '700',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-x-ray', 'library' => 'fa-solid' ],
                      'title_text' => 'Implantologie',
                      'description_text' => 'Duurzame implantaten als vervanging voor ontbrekende tanden.',
                      'link' => [ 'url' => '/behandelingen/' ],
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                      'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Inter', 'title_typography_font_weight' => '700',
                  ], 'elements' => [],
              ]]],
        ],
    ],
    // Testimonial section
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'background_background' => 'classic', 'background_color' => '#F4F9F8',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '60', 'bottom' => '80', 'left' => '60' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'testimonial',
                    'settings' => [
                        'testimonial_content' => 'Eindelijk een tandarts waar ik me op mijn gemak voel. Professioneel, vriendelijk en altijd op tijd. Ik raad deze praktijk iedereen aan!',
                        'testimonial_name' => 'Maria de Vries',
                        'testimonial_job' => 'Patiënt sinds 2020',
                        'testimonial_alignment' => 'center',
                        'content_content_color' => '#4A4A4A',
                        'name_text_color' => '#1A3A5C',
                    ], 'elements' => [] ],
              ]],
        ],
    ],
];

$home_id = create_or_update_page( 'Home', 'home', $home_data );
echo "3a. Home page created (ID: $home_id).\n";

// ── Over ons ──

$about_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Over ons', 'header_size' => 'h1',
                        'title_color' => '#1A3A5C', 'typography_typography' => 'custom',
                        'typography_font_family' => 'Inter', 'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p>Onze praktijk bestaat sinds 2005 en biedt hoogwaardige tandheelkundige zorg aan het hele gezin. Met een team van ervaren tandartsen en mondhygiënisten streven wij naar de beste resultaten in een ontspannen omgeving.</p><p>Wij investeren continu in de nieuwste apparatuur en technieken om u de best mogelijke zorg te bieden.</p>',
                        'text_color' => '#4A4A4A' ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [ 'image' => [ 'url' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=600', 'id' => '' ],
                        'border_radius' => [ 'unit' => 'px', 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '12' ] ],
                    'elements' => [] ],
              ]],
        ],
    ],
    // Team grid
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '30', 'gap' => 'extended',
            'background_background' => 'classic', 'background_color' => '#F4F9F8',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Ons team', 'header_size' => 'h2', 'align' => 'center',
                        'title_color' => '#1A3A5C' ], 'elements' => [] ],
              ]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '30', 'gap' => 'extended',
            'background_background' => 'classic', 'background_color' => '#F4F9F8',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ] ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-user-md', 'library' => 'fa-solid' ],
                      'title_text' => 'Dr. Jan Bakker', 'description_text' => 'Tandarts — Algemene tandheelkunde & implantologie',
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-user-md', 'library' => 'fa-solid' ],
                      'title_text' => 'Dr. Lisa de Jong', 'description_text' => 'Tandarts — Cosmetische tandheelkunde & orthodontie',
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                  'settings' => [
                      'selected_icon' => [ 'value' => 'fas fa-user-md', 'library' => 'fa-solid' ],
                      'title_text' => 'Sophie Vermeer', 'description_text' => 'Mondhygiënist — Preventie & parodontologie',
                      'position' => 'top', 'primary_color' => '#4ECDC4',
                  ], 'elements' => [],
              ]]],
        ],
    ],
];

$about_id = create_or_update_page( 'Over ons', 'over-ons', $about_data );
echo "3b. Over ons page created (ID: $about_id).\n";

// ── Behandelingen ──

$services = [
    [ 'icon' => 'fas fa-tooth',       'title' => 'Controle & reiniging',      'desc' => 'Halfjaarlijkse controle met professionele gebitsreiniging en advies.' ],
    [ 'icon' => 'fas fa-fill-drip',    'title' => 'Vullingen',                 'desc' => 'Tandkleurige composiet vullingen voor een natuurlijk resultaat.' ],
    [ 'icon' => 'fas fa-smile-beam',   'title' => 'Whitening',                 'desc' => 'Professioneel bleken voor een wittere, stralende glimlach.' ],
    [ 'icon' => 'fas fa-teeth',        'title' => 'Kronen & bruggen',          'desc' => 'Duurzaam herstel van beschadigde of ontbrekende tanden.' ],
    [ 'icon' => 'fas fa-x-ray',        'title' => 'Implantaten',               'desc' => 'Titanium implantaten als permanente vervanging voor ontbrekende elementen.' ],
    [ 'icon' => 'fas fa-child',        'title' => 'Kindertandheelkunde',       'desc' => 'Speelse en geduldige behandeling speciaal voor kinderen.' ],
];

$service_columns = [];
foreach ( $services as $s ) {
    $service_columns[] = [
        'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
        'elements' => [[
            'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
            'settings' => [
                'selected_icon' => [ 'value' => $s['icon'], 'library' => 'fa-solid' ],
                'title_text' => $s['title'], 'description_text' => $s['desc'],
                'position' => 'top', 'primary_color' => '#4ECDC4',
                'title_typography_typography' => 'custom', 'title_typography_font_family' => 'Inter', 'title_typography_font_weight' => '700',
            ], 'elements' => [],
        ]],
    ];
}

$behandelingen_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '10', 'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '40', 'left' => '40' ] ],
        'elements' => [[ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                  'settings' => [ 'title' => 'Behandelingen', 'header_size' => 'h1', 'align' => 'center',
                      'title_color' => '#1A3A5C', 'typography_typography' => 'custom',
                      'typography_font_family' => 'Inter', 'typography_font_weight' => '700',
                      'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                  'elements' => [] ],
                [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                  'settings' => [ 'editor' => '<p style="text-align:center;">Ontdek ons volledige aanbod aan tandheelkundige behandelingen.</p>',
                      'text_color' => '#4A4A4A' ],
                  'elements' => [] ],
            ],
        ]],
    ],
    // Row 1 (3 cards)
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '30', 'gap' => 'extended',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '24', 'left' => '40' ] ],
        'elements' => array_slice( $service_columns, 0, 3 ),
    ],
    // Row 2 (3 cards)
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '30', 'gap' => 'extended',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ] ],
        'elements' => array_slice( $service_columns, 3, 3 ),
    ],
];

$behandelingen_id = create_or_update_page( 'Behandelingen', 'behandelingen', $behandelingen_data );
echo "3c. Behandelingen page created (ID: $behandelingen_id).\n";

// ── Contact ──

$contact_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '10',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '40', 'left' => '40' ] ],
        'elements' => [[ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
            'elements' => [[
                'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                'settings' => [ 'title' => 'Contact', 'header_size' => 'h1', 'align' => 'center',
                    'title_color' => '#1A3A5C', 'typography_typography' => 'custom',
                    'typography_font_family' => 'Inter', 'typography_font_weight' => '700',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                'elements' => [],
            ]],
        ]],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [ 'structure' => '20', 'gap' => 'extended',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ] ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'form',
                  'settings' => [
                      'form_name' => 'Contact',
                      'form_fields' => [
                          [ 'custom_id' => 'name', 'field_type' => 'text', 'field_label' => 'Naam', 'placeholder' => 'Uw naam', 'required' => 'true', 'width' => '50' ],
                          [ 'custom_id' => 'phone', 'field_type' => 'tel', 'field_label' => 'Telefoon', 'placeholder' => '06-12345678', 'required' => '', 'width' => '50' ],
                          [ 'custom_id' => 'email', 'field_type' => 'email', 'field_label' => 'E-mail', 'placeholder' => 'uw@email.nl', 'required' => 'true', 'width' => '100' ],
                          [ 'custom_id' => 'message', 'field_type' => 'textarea', 'field_label' => 'Bericht', 'placeholder' => 'Uw vraag of opmerking', 'required' => '', 'rows' => 5, 'width' => '100' ],
                      ],
                      'button_text' => 'Versturen',
                      'button_background_color' => '#4ECDC4',
                      'button_color' => '#FFFFFF',
                      'email_to' => 'info@tandartspraktijk.nl',
                      'email_subject' => 'Nieuw contactformulier bericht',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid' ],
                        'title_text' => 'Adres', 'description_text' => 'Keizersgracht 123\n1015 AA Amsterdam',
                        'position' => 'left', 'primary_color' => '#4ECDC4',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-phone', 'library' => 'fa-solid' ],
                        'title_text' => 'Telefoon', 'description_text' => '020 123 4567',
                        'position' => 'left', 'primary_color' => '#4ECDC4',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-clock', 'library' => 'fa-solid' ],
                        'title_text' => 'Openingstijden', 'description_text' => 'Ma-Vr: 08:00 - 17:00\nZa: op afspraak',
                        'position' => 'left', 'primary_color' => '#4ECDC4',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                    'settings' => [
                        'html' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2436.0!2d4.887339!3d52.370216!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTLCsDIyJzEyLjgiTiA0wrA1MycxNC4wIkU!5e0!3m2!1snl!2snl!4v1" width="100%" height="300" style="border:0;border-radius:12px;" allowfullscreen loading="lazy"></iframe>',
                    ], 'elements' => [] ],
              ]],
        ],
    ],
];

$contact_id = create_or_update_page( 'Contact', 'contact', $contact_data );
echo "3d. Contact page created (ID: $contact_id).\n";

// ─── 4. WordPress Settings ───

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
flush_rewrite_rules();
echo "4. WordPress settings configured.\n";

// ─── 5. Clear CSS cache ───

delete_post_meta_by_key( '_elementor_css' );
echo "5. Elementor CSS cache cleared.\n";

update_option( 'tandarts_elementor_setup_done', true );
echo "\nDone! Created:\n";
echo "- Header template (ID: $header_id)\n";
echo "- Footer template (ID: $footer_id)\n";
echo "- Home (ID: $home_id)\n";
echo "- Over ons (ID: $about_id)\n";
echo "- Behandelingen (ID: $behandelingen_id)\n";
echo "- Contact (ID: $contact_id)\n";
echo "\nOpen any page in Elementor editor to start customizing.\n";
