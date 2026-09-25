<?php
/**
 * Restaurant Bistro — Elementor Pro Setup Script
 * Run once: wp eval-file wp-content/themes/restaurant/setup-elementor.php --allow-root
 */

$marker = get_option( 'restaurant_elementor_setup_done' );
if ( $marker ) {
    echo "Setup already completed. Delete option 'restaurant_elementor_setup_done' to re-run.\n";
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
    [ '_id' => 'primary',   'title' => 'Primary (donkergroen)', 'color' => '#2D5016' ],
    [ '_id' => 'secondary', 'title' => 'Secondary (groen)',     'color' => '#4A7C2E' ],
    [ '_id' => 'text',      'title' => 'Body text',              'color' => '#3D3D3D' ],
    [ '_id' => 'accent',    'title' => 'Light BG (creme)',       'color' => '#FFF8E7' ],
];

$settings['system_typography'] = [
    [ '_id' => 'primary',   'title' => 'Heading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
      'typography_font_weight' => '700' ],
    [ '_id' => 'secondary', 'title' => 'Subheading',
      'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
      'typography_font_weight' => '600' ],
    [ '_id' => 'text',      'title' => 'Body',
      'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans',
      'typography_font_weight' => '400',
      'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
      'typography_line_height' => [ 'unit' => 'em', 'size' => 1.7 ] ],
    [ '_id' => 'accent',    'title' => 'Caption',
      'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans',
      'typography_font_weight' => '400' ],
];

$settings['container_width'] = [ 'unit' => 'px', 'size' => 1100 ];
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
        'background_background' => 'classic', 'background_color' => '#2D5016',
        'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '40', 'bottom' => '16', 'left' => '40' ],
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
              'settings' => [
                  'title' => 'Bistro Verde', 'header_size' => 'h3',
                  'title_color' => '#FFF8E7',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                  'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 26 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'nav-menu',
              'settings' => [
                  'menu' => '', 'layout' => 'horizontal', 'align_items' => 'center',
                  'pointer' => 'none',
                  'text_color' => '#FFF8E7',
                  'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans',
                  'typography_font_weight' => '600', 'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
              ], 'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
              'settings' => [
                  'text' => 'Reserveren', 'align' => 'right',
                  'link' => [ 'url' => '#contact', 'is_external' => false, 'nofollow' => false ],
                  'background_color' => '#FFF8E7', 'button_text_color' => '#2D5016',
                  'border_radius' => [ 'unit' => 'px', 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4' ],
                  'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans',
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
        'background_background' => 'classic', 'background_color' => '#2D5016',
        'padding' => [ 'unit' => 'px', 'top' => '50', 'right' => '40', 'bottom' => '50', 'left' => '40' ],
    ],
    'elements' => [
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                'settings' => [ 'title' => 'Bistro Verde', 'header_size' => 'h4',
                    'title_color' => '#FFF8E7', 'typography_typography' => 'custom',
                    'typography_font_family' => 'Cormorant Garamond', 'typography_font_weight' => '700' ],
                'elements' => [] ],
              [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                'settings' => [ 'editor' => '<p>Eerlijk eten, met liefde bereid.</p>',
                    'text_color' => 'rgba(255,248,231,0.7)',
                    'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '400' ],
                'elements' => [] ],
          ]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#FFF8E7;">Openingstijden</strong></p><p style="color:rgba(255,248,231,0.8);">Di - Za: 17:00 - 23:00<br>Zo: 12:00 - 21:00<br>Ma: gesloten</p>' ],
              'elements' => [],
          ]]],
        [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 33 ],
          'elements' => [[
              'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
              'settings' => [ 'editor' => '<p><strong style="color:#FFF8E7;">Contact</strong></p><p style="color:rgba(255,248,231,0.8);">Herengracht 456<br>1017 CA Amsterdam</p><p style="color:rgba(255,248,231,0.8);">020 987 6543<br>info@bistroverde.nl</p>' ],
              'elements' => [],
          ]]],
    ],
]];
$footer_id = create_template( 'Footer', 'footer', [ 'include/general' ], $footer_data );
echo "2b. Footer template created (ID: $footer_id).\n";

// ─── 3. One-Page Content ───

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

$home_data = [
    // ── Hero section with background image ──
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width', 'structure' => '10',
            'min_height' => [ 'unit' => 'vh', 'size' => 85 ],
            'background_background' => 'classic',
            'background_image' => [ 'url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1400', 'id' => '' ],
            'background_position' => 'center center',
            'background_size' => 'cover',
            'background_overlay_background' => 'classic',
            'background_overlay_color' => 'rgba(45,80,22,0.65)',
            'padding' => [ 'unit' => 'px', 'top' => '140', 'right' => '40', 'bottom' => '140', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Bistro Verde', 'header_size' => 'h1', 'align' => 'center',
                        'title_color' => '#FFF8E7',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 72 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.1 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p style="text-align:center;">Eerlijk eten met verse, seizoensgebonden ingrediënten in het hart van Amsterdam.</p>',
                        'text_color' => 'rgba(255,248,231,0.9)',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans',
                        'typography_font_weight' => '400', 'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
                    'settings' => [ 'text' => 'Bekijk de menukaart', 'align' => 'center',
                        'link' => [ 'url' => '#menu', 'is_external' => false, 'nofollow' => false ],
                        'background_color' => '#FFF8E7', 'button_text_color' => '#2D5016',
                        'border_radius' => [ 'unit' => 'px', 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4' ],
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '700' ],
                    'elements' => [] ],
              ]],
        ],
    ],

    // ── Menu section (2 columns) ──
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'background_background' => 'classic', 'background_color' => '#FFF8E7',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '20', 'left' => '40' ],
            'css_id' => 'menu',
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Menukaart', 'header_size' => 'h2', 'align' => 'center',
                        'title_color' => '#2D5016',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p style="text-align:center;">Seizoensgebonden gerechten met lokale ingrediënten.</p>',
                        'text_color' => '#3D3D3D' ],
                    'elements' => [] ],
              ]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20', 'gap' => 'extended',
            'background_background' => 'classic', 'background_color' => '#FFF8E7',
            'padding' => [ 'unit' => 'px', 'top' => '20', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Voorgerechten', 'header_size' => 'h3',
                        'title_color' => '#2D5016',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p><strong>Seizoenssalade</strong> <span style="float:right;">€9,50</span><br><em>Geitenkaas, walnoot, honing-mosterd dressing</em></p><hr><p><strong>Pompoensoep</strong> <span style="float:right;">€8,00</span><br><em>Geroosterde pompoen, kokosmelk, pompoenpitten</em></p><hr><p><strong>Carpaccio</strong> <span style="float:right;">€12,50</span><br><em>Rundercarpaccio, truffel mayo, Parmezaan, rucola</em></p><hr><p><strong>Bruschetta</strong> <span style="float:right;">€7,50</span><br><em>Geroosterd brood, tomaat, basilicum, knoflookolie</em></p>',
                        'text_color' => '#3D3D3D',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '400' ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Hoofdgerechten', 'header_size' => 'h3',
                        'title_color' => '#2D5016',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p><strong>Ossobuco</strong> <span style="float:right;">€24,50</span><br><em>Langzaam gegaard kalfsvlees, gremolata, risotto Milanese</em></p><hr><p><strong>Zeebaars</strong> <span style="float:right;">€22,00</span><br><em>Pan-gebakken zeebaars, fijne groenten, saffraan beurre blanc</em></p><hr><p><strong>Risotto Funghi</strong> <span style="float:right;">€18,50</span><br><em>Arborio rijst, wilde paddenstoelen, truffelolie, Parmezaan</em></p><hr><p><strong>Entrecote</strong> <span style="float:right;">€26,00</span><br><em>Black Angus, kruidenboter, hand-cut frites, salade</em></p>',
                        'text_color' => '#3D3D3D',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '400' ],
                    'elements' => [] ],
              ]],
        ],
    ],

    // ── Over ons section ──
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '60', 'bottom' => '80', 'left' => '60' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [ 'image' => [ 'url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=600', 'id' => '' ],
                        'image_size' => 'full',
                        'border_radius' => [ 'unit' => 'px', 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8' ] ],
                    'elements' => [] ],
              ]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [ 'title' => 'Ons verhaal', 'header_size' => 'h2',
                        'title_color' => '#2D5016',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                        'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ] ],
                    'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [ 'editor' => '<p>Sinds 2018 serveren wij bij Bistro Verde seizoensgebonden gerechten bereid met de beste lokale ingrediënten. Onze chef heeft jarenlange ervaring in de Italiaanse en Franse keuken.</p><p>Wij geloven dat goed eten begint bij goede producten. Daarom werken wij samen met lokale boeren en leveranciers die dezelfde passie voor kwaliteit delen.</p>',
                        'text_color' => '#3D3D3D',
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '400' ],
                    'elements' => [] ],
              ]],
        ],
    ],

    // ── Contact section ──
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'background_background' => 'classic', 'background_color' => '#FFF8E7',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '40', 'bottom' => '20', 'left' => '40' ],
            'css_id' => 'contact',
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 100 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                  'settings' => [ 'title' => 'Reserveren', 'header_size' => 'h2', 'align' => 'center',
                      'title_color' => '#2D5016',
                      'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond',
                      'typography_font_weight' => '700', 'typography_font_size' => [ 'unit' => 'px', 'size' => 42 ] ],
                  'elements' => [],
              ]]],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'structure' => '20', 'gap' => 'extended',
            'background_background' => 'classic', 'background_color' => '#FFF8E7',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '80', 'left' => '40' ],
        ],
        'elements' => [
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [[
                  'id' => eid(), 'elType' => 'widget', 'widgetType' => 'form',
                  'settings' => [
                      'form_name' => 'Reservering',
                      'form_fields' => [
                          [ 'custom_id' => 'name', 'field_type' => 'text', 'field_label' => 'Naam', 'placeholder' => 'Uw naam', 'required' => 'true', 'width' => '50' ],
                          [ 'custom_id' => 'phone', 'field_type' => 'tel', 'field_label' => 'Telefoon', 'placeholder' => '06-12345678', 'required' => 'true', 'width' => '50' ],
                          [ 'custom_id' => 'email', 'field_type' => 'email', 'field_label' => 'E-mail', 'placeholder' => 'uw@email.nl', 'required' => 'true', 'width' => '100' ],
                          [ 'custom_id' => 'date', 'field_type' => 'date', 'field_label' => 'Datum', 'required' => 'true', 'width' => '50' ],
                          [ 'custom_id' => 'guests', 'field_type' => 'number', 'field_label' => 'Aantal gasten', 'placeholder' => '2', 'required' => 'true', 'width' => '50' ],
                          [ 'custom_id' => 'message', 'field_type' => 'textarea', 'field_label' => 'Opmerkingen', 'placeholder' => 'Allergieën, dieetwensen, speciale gelegenheid...', 'required' => '', 'rows' => 4, 'width' => '100' ],
                      ],
                      'button_text' => 'Reserveren',
                      'button_background_color' => '#2D5016',
                      'button_color' => '#FFFFFF',
                      'email_to' => 'info@bistroverde.nl',
                      'email_subject' => 'Nieuwe reservering',
                  ], 'elements' => [],
              ]]],
            [ 'id' => eid(), 'elType' => 'column', 'settings' => [ '_column_size' => 50 ],
              'elements' => [
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-list',
                    'settings' => [
                        'icon_list' => [
                            [ 'text' => 'Di - Za: 17:00 - 23:00', 'selected_icon' => [ 'value' => 'fas fa-clock', 'library' => 'fa-solid' ], 'link' => [ 'url' => '' ] ],
                            [ 'text' => 'Zo: 12:00 - 21:00',      'selected_icon' => [ 'value' => 'fas fa-clock', 'library' => 'fa-solid' ], 'link' => [ 'url' => '' ] ],
                            [ 'text' => 'Ma: gesloten',           'selected_icon' => [ 'value' => 'fas fa-times-circle', 'library' => 'fa-solid' ], 'link' => [ 'url' => '' ] ],
                        ],
                        'icon_color' => '#2D5016',
                        'text_color' => '#3D3D3D',
                        'text_indent' => [ 'unit' => 'px', 'size' => 8 ],
                        'typography_typography' => 'custom', 'typography_font_family' => 'Open Sans', 'typography_font_weight' => '400',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-map-marker-alt', 'library' => 'fa-solid' ],
                        'title_text' => 'Adres', 'description_text' => 'Herengracht 456, 1017 CA Amsterdam',
                        'position' => 'left', 'primary_color' => '#2D5016',
                    ], 'elements' => [] ],
                  [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'icon-box',
                    'settings' => [
                        'selected_icon' => [ 'value' => 'fas fa-phone', 'library' => 'fa-solid' ],
                        'title_text' => 'Telefoon', 'description_text' => '020 987 6543',
                        'position' => 'left', 'primary_color' => '#2D5016',
                    ], 'elements' => [] ],
              ]],
        ],
    ],
];

$home_id = create_or_update_page( 'Home', 'home', $home_data );
echo "3. Home page created (ID: $home_id).\n";

// ─── 4. WordPress Settings ───

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
flush_rewrite_rules();
echo "4. WordPress settings configured.\n";

// ─── 5. Clear CSS cache ───

delete_post_meta_by_key( '_elementor_css' );
echo "5. Elementor CSS cache cleared.\n";

update_option( 'restaurant_elementor_setup_done', true );
echo "\nDone! Created:\n";
echo "- Header template (ID: $header_id)\n";
echo "- Footer template (ID: $footer_id)\n";
echo "- Home (ID: $home_id)\n";
echo "\nOpen the page in Elementor editor to start customizing.\n";
