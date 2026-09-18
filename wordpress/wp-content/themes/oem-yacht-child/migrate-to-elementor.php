<?php
/**
 * Migration: Convert custom theme to Elementor Pro.
 *
 * Prerequisites:
 *   - Elementor Pro must be installed and activated
 *   - Current site content must exist (pages + oem_project CPT posts)
 *
 * Run: wp eval-file wp-content/themes/oem-yacht-child/migrate-to-elementor.php --allow-root
 */

// ─── Prerequisite Check ────────────────────────────────────────────────────────

if ( ! defined( 'ABSPATH' ) ) {
    echo "ERROR: Must be run within WordPress (wp eval-file).\n";
    exit(1);
}

if ( ! is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
    echo "ERROR: Elementor Pro is not active. Install and activate it first.\n";
    echo "  wp plugin install /path/to/elementor-pro.zip --activate --allow-root\n";
    exit(1);
}

echo "=== OEM Yacht Service: Elementor Pro Migration ===\n\n";

// ─── Helper Functions ──────────────────────────────────────────────────────────

function eid() {
    static $c = 0;
    return substr( md5( 'oem_el_' . (++$c) . microtime() ), 0, 7 );
}

function el_section( $elements, $settings = [] ) {
    return [
        'id'       => eid(),
        'elType'   => 'section',
        'settings' => $settings,
        'elements' => $elements,
    ];
}

function el_col( $size, $elements, $settings = [] ) {
    return [
        'id'       => eid(),
        'elType'   => 'column',
        'settings' => array_merge( ['_column_size' => $size, '_inline_size' => null], $settings ),
        'elements' => $elements,
    ];
}

function el_w( $type, $settings = [] ) {
    return [
        'id'         => eid(),
        'elType'     => 'widget',
        'widgetType' => $type,
        'settings'   => $settings,
    ];
}

function el_heading( $text, $tag = 'h2', $extra = [] ) {
    return el_w( 'heading', array_merge( [
        'title'       => $text,
        'header_size' => $tag,
        'title_color' => '#1B1B1F',
        'typography_typography'   => 'custom',
        'typography_font_family'  => 'Acumin Pro',
        'typography_font_weight'  => '700',
    ], $extra ) );
}

function el_text( $html, $extra = [] ) {
    return el_w( 'text-editor', array_merge( [
        'editor'     => $html,
        'text_color' => '#4D4D54',
        'typography_typography'  => 'custom',
        'typography_font_family' => 'Acumin Pro',
        'typography_font_weight' => '400',
        'typography_font_size'   => ['unit' => 'px', 'size' => 17, 'sizes' => []],
        'typography_line_height' => ['unit' => 'em', 'size' => 1.65, 'sizes' => []],
    ], $extra ) );
}

function el_eyebrow( $text, $extra = [] ) {
    return el_w( 'heading', array_merge( [
        'title'       => $text,
        'header_size' => 'span',
        'title_color' => '#7A7A82',
        'typography_typography'      => 'custom',
        'typography_font_family'     => 'Acumin Pro',
        'typography_font_weight'     => '300',
        'typography_font_size'       => ['unit' => 'px', 'size' => 13, 'sizes' => []],
        'typography_letter_spacing'  => ['unit' => 'px', 'size' => 3, 'sizes' => []],
        'typography_text_transform'  => 'uppercase',
        '_css_classes' => 'oem-eyebrow',
    ], $extra ) );
}

function el_button( $text, $url, $extra = [] ) {
    return el_w( 'button', array_merge( [
        'text'              => $text,
        'link'              => ['url' => $url, 'is_external' => '', 'nofollow' => ''],
        'background_color'  => '#E40034',
        'button_text_color' => '#FFFFFF',
        'border_radius'     => ['unit' => 'px', 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6', 'isLinked' => true],
        'typography_typography'  => 'custom',
        'typography_font_family' => 'Acumin Pro',
        'typography_font_weight' => '700',
        'selected_icon'     => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid'],
        'icon_align'        => 'right',
    ], $extra ) );
}

function el_button_outline( $text, $url, $extra = [] ) {
    return el_button( $text, $url, array_merge( [
        'button_type'       => '',
        'background_color'  => 'transparent',
        'button_text_color' => '#FFFFFF',
        'border_border'     => 'solid',
        'border_width'      => ['unit' => 'px', 'top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'isLinked' => true],
        'border_color'      => 'rgba(255,255,255,.35)',
        'selected_icon'     => ['value' => '', 'library' => ''],
    ], $extra ) );
}

function el_icon_box( $icon, $title, $desc, $extra = [] ) {
    return el_w( 'icon-box', array_merge( [
        'selected_icon'   => ['value' => 'fas ' . $icon, 'library' => 'fa-solid'],
        'title_text'      => $title,
        'description_text'=> $desc,
        'icon_color'      => '#E40034',
        'title_color'     => '#1B1B1F',
        'description_color'=> '#4D4D54',
        'title_typography_typography'  => 'custom',
        'title_typography_font_family' => 'Acumin Pro',
        'title_typography_font_weight' => '700',
        'title_typography_font_size'   => ['unit' => 'px', 'size' => 20, 'sizes' => []],
        'description_typography_typography'  => 'custom',
        'description_typography_font_family' => 'Acumin Pro',
        'description_typography_font_size'   => ['unit' => 'px', 'size' => 16, 'sizes' => []],
        'description_typography_line_height' => ['unit' => 'em', 'size' => 1.55, 'sizes' => []],
    ], $extra ) );
}

function el_image_placeholder( $height = '320px' ) {
    return el_w( 'html', [
        'html' => '<div style="background:#ECECEE;border-radius:8px;height:' . $height . ';display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="font-size:48px;color:#C9C9CD;"></i></div>',
    ] );
}

function el_spacer( $size = 40 ) {
    return el_w( 'spacer', ['space' => ['unit' => 'px', 'size' => $size]] );
}

function el_shortcode( $code ) {
    return el_w( 'shortcode', ['shortcode' => $code] );
}

function pad( $top = 80, $bottom = 80 ) {
    return ['unit' => 'px', 'top' => (string) $top, 'right' => '0', 'bottom' => (string) $bottom, 'left' => '0', 'isLinked' => false];
}

function set_elementor_data( $post_id, $data ) {
    update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $post_id, '_elementor_version', '4.2.4' );
    update_post_meta( $post_id, '_elementor_page_settings', [
        'hide_title' => 'yes',
    ] );
    wp_update_post( ['ID' => $post_id, 'post_content' => ''] );
}

// ─── 1. Update Elementor Global Kit ────────────────────────────────────────────

echo "1. Updating Elementor Global Kit...\n";

$kit_id = (int) get_option( 'elementor_active_kit' );
if ( $kit_id ) {
    $kit_settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
    if ( ! is_array( $kit_settings ) ) $kit_settings = [];

    $kit_settings['system_colors'] = [
        ['_id' => 'primary',   'title' => 'OEM Red',      'color' => '#E40034'],
        ['_id' => 'secondary', 'title' => 'OEM Ink',      'color' => '#1B1B1F'],
        ['_id' => 'text',      'title' => 'OEM Charcoal', 'color' => '#4D4D54'],
        ['_id' => 'accent',    'title' => 'OEM Paper',    'color' => '#F6F6F7'],
    ];
    $kit_settings['custom_colors'] = [
        ['_id' => 'oem_red_deep',    'title' => 'Red Deep',      'color' => '#B5002A'],
        ['_id' => 'oem_charcoal_70', 'title' => 'Charcoal 70',   'color' => '#7A7A82'],
        ['_id' => 'oem_charcoal_50', 'title' => 'Charcoal 50',   'color' => '#A4A4AA'],
        ['_id' => 'oem_charcoal_30', 'title' => 'Charcoal 30',   'color' => '#C9C9CD'],
        ['_id' => 'oem_charcoal_10', 'title' => 'Charcoal 10',   'color' => '#ECECEE'],
    ];
    $kit_settings['system_typography'] = [
        [
            '_id' => 'primary',
            'title' => 'Primary',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Acumin Pro',
            'typography_font_weight' => '400',
        ],
        [
            '_id' => 'secondary',
            'title' => 'Secondary',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Acumin Pro',
            'typography_font_weight' => '700',
        ],
        [
            '_id' => 'text',
            'title' => 'Text',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Acumin Pro',
            'typography_font_weight' => '400',
            'typography_font_size' => ['unit' => 'px', 'size' => 17],
            'typography_line_height' => ['unit' => 'em', 'size' => 1.65],
        ],
        [
            '_id' => 'accent',
            'title' => 'Subheading',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Acumin Pro',
            'typography_font_weight' => '300',
        ],
    ];
    $kit_settings['container_width'] = ['unit' => 'px', 'size' => 1240, 'sizes' => []];
    $kit_settings['space_between_widgets'] = ['unit' => 'px', 'size' => 0, 'sizes' => []];
    $kit_settings['viewport_md'] = 768;
    $kit_settings['viewport_lg'] = 1025;
    $kit_settings['page_title_selector'] = 'h1.entry-title';

    update_post_meta( $kit_id, '_elementor_page_settings', $kit_settings );
    echo "  Kit updated (ID $kit_id): colors, fonts, container 1240px.\n";
} else {
    echo "  WARNING: No active Elementor kit found.\n";
}

// ─── 2. Theme Builder Templates ────────────────────────────────────────────────

echo "\n2. Creating Theme Builder templates...\n";

function create_theme_template( $title, $type, $conditions, $data ) {
    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_status'  => 'publish',
        'post_type'    => 'elementor_library',
    ]);
    if ( is_wp_error( $post_id ) ) {
        echo "  ERROR creating template '$title': " . $post_id->get_error_message() . "\n";
        return 0;
    }
    update_post_meta( $post_id, '_elementor_template_type', $type );
    update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
    update_post_meta( $post_id, '_elementor_version', '4.2.4' );
    update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
    update_post_meta( $post_id, '_elementor_conditions', $conditions );

    $saved = get_option( 'elementor_pro_theme_builder_conditions', [] );
    $saved[ $type ][ $post_id ] = $conditions;
    update_option( 'elementor_pro_theme_builder_conditions', $saved );

    echo "  Created: $title ($type, ID $post_id)\n";
    return $post_id;
}

// --- HEADER ---
$logo_url = get_stylesheet_directory_uri() . '/assets/logo-red.svg';

$header_data = [
    el_section(
        [el_col( 100, [
            el_w( 'html', [
                'html' => '<div style="display:flex;align-items:center;justify-content:space-between;max-width:1240px;margin:0 auto;padding:0 24px;height:72px;">
  <a href="/" style="display:block;line-height:0;"><img src="' . esc_url( $logo_url ) . '" alt="OEM Yacht Service" style="height:36px;"></a>
  <nav id="oem-el-nav" style="flex:1;display:flex;justify-content:center;">[elementor-template id="nav-placeholder"]</nav>
  <a href="/contact/" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#E40034;color:#fff;border-radius:6px;font:700 15px/1 \'Acumin Pro\',sans-serif;text-decoration:none;">Contact</a>
</div>',
            ] ),
        ] )],
        [
            'background_background' => 'classic',
            'background_color' => 'rgba(255,255,255,.92)',
            'padding' => pad(0, 0),
            'margin' => pad(0, 0),
            'css_classes' => 'oem-header-section',
            'z_index' => '1000',
            '_element_custom_width' => ['unit' => '%', 'size' => 100],
        ]
    ),
];

create_theme_template( 'OEM Header', 'header', ['include/general'], $header_data );

// Note: The header uses an HTML widget as a starting point.
// After migration, replace the HTML widget with proper Elementor Pro widgets:
//   - Site Logo widget
//   - Nav Menu widget (location: primary)
//   - Button widget (Contact CTA)
// This ensures full visual editing capability.

// --- FOOTER ---
$logo_white = get_stylesheet_directory_uri() . '/assets/logo-white.svg';

$footer_html = '<div style="max-width:1240px;margin:0 auto;padding:64px 24px 32px;">
  <div style="display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr 1fr;gap:32px;">
    <div>
      <a href="/"><img src="' . esc_url( $logo_white ) . '" alt="OEM Yacht Service" style="height:28px;margin-bottom:16px;display:block;"></a>
      <p style="font:300 15px/1.6 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.6);margin:0;">Independent yacht support, in combination with the original equipment manufacturers\' knowledge.</p>
    </div>
    <div>
      <h5 style="font:700 14px/1.3 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:2px;margin:0 0 16px;">What we do</h5>
      <a href="/what-we-do/electrical-control-systems/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Electrical control systems</a>
      <a href="/what-we-do/hydraulic/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Hydraulic systems</a>
      <a href="/what-we-do/mechanic/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Mechanic</a>
      <a href="/what-we-do/tender-cranes/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Tender Cranes</a>
      <a href="/what-we-do/boarding/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Boarding</a>
      <a href="/what-we-do/hull-doors/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Hull Doors</a>
    </div>
    <div>
      <h5 style="font:700 14px/1.3 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:2px;margin:0 0 16px;">Projects</h5>
      <a href="/projects/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">All projects</a>
      <a href="/oem-connect/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">OEM Connect</a>
      <a href="/careers/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Careers</a>
    </div>
    <div>
      <h5 style="font:700 14px/1.3 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:2px;margin:0 0 16px;">About</h5>
      <a href="/team/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Team</a>
      <a href="/team-sub/" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">Team Sub</a>
    </div>
    <div>
      <h5 style="font:700 14px/1.3 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:2px;margin:0 0 16px;">Contact</h5>
      <a href="mailto:info@oemyachtservice.com" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">info@oemyachtservice.com</a>
      <a href="tel:+31611004005" style="display:block;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.75);text-decoration:none;padding:4px 0;">+31 (0) 6 1100 4005</a>
      <p style="font:400 15px/1.6 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.5);margin:8px 0 0;">Industriepark 10<br>8701 PN Bolsward</p>
    </div>
  </div>
  <div style="display:flex;justify-content:space-between;align-items:center;padding-top:32px;margin-top:32px;border-top:1px solid rgba(255,255,255,.1);font:400 14px/1.4 \'Acumin Pro\',sans-serif;">
    <div style="display:flex;gap:24px;">
      <a href="/contact/" style="color:rgba(255,255,255,.45);text-decoration:none;">Terms &amp; conditions</a>
      <a href="/contact/" style="color:rgba(255,255,255,.45);text-decoration:none;">Privacy</a>
    </div>
    <span style="color:rgba(255,255,255,.35);">&copy; 2026 OEM Yacht Service B.V.</span>
  </div>
</div>';

$footer_data = [
    el_section(
        [el_col( 100, [ el_w( 'html', ['html' => $footer_html] ) ] )],
        [
            'background_background' => 'classic',
            'background_color' => '#1B1B1F',
            'padding' => pad(0, 0),
        ]
    ),
];

create_theme_template( 'OEM Footer', 'footer', ['include/general'], $footer_data );

// --- SINGLE OEM_PROJECT ---
$single_data = [
    // Breadcrumb + Tag + Title + Vessel
    el_section(
        [el_col( 100, [
            el_w( 'text-editor', [
                'editor' => '<p style="font:400 14px/1.4 \'Acumin Pro\',sans-serif;"><a href="/projects/" style="color:#E40034;text-decoration:none;">Projects</a> / <span style="color:#7A7A82;">{post_title_placeholder}</span></p>',
                '__dynamic__' => [],
            ] ),
            el_w( 'shortcode', ['shortcode' => '[oem_tag_badge]'] ),
            el_w( 'theme-post-title', [
                'header_size' => 'h1',
                'title_color' => '#1B1B1F',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Acumin Pro',
                'typography_font_weight' => '700',
                'typography_font_size' => ['unit' => 'px', 'size' => 42, 'sizes' => []],
                'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
            ] ),
            el_w( 'html', [
                'html' => '<?php $v = get_post_meta(get_the_ID(),"_oem_vessel",true); if($v): ?><p style="font:400 17px/1.65 \'Acumin Pro\',sans-serif;color:#7A7A82;margin:8px 0 0;"><?php echo esc_html($v); ?></p><?php endif; ?>',
            ] ),
        ] )],
        ['padding' => ['unit' => 'px', 'top' => '72', 'right' => '0', 'bottom' => '32', 'left' => '0', 'isLinked' => false]]
    ),

    // Featured Image
    el_section(
        [el_col( 100, [
            el_w( 'theme-post-featured-image', [
                'image_size' => 'full',
                'image_custom_dimension' => ['width' => '', 'height' => '440'],
                'image_fit' => 'cover',
                'border_radius' => ['unit' => 'px', 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'isLinked' => true],
            ] ),
        ] )],
        ['padding' => pad(0, 48)]
    ),

    // Content: 2-column with body/delivered + fact sheet
    el_section(
        [
            el_col( 60, [
                el_heading( 'The job', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.2, 'sizes' => []],
                ] ),
                el_w( 'theme-post-content', [
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Acumin Pro',
                    'typography_font_size' => ['unit' => 'px', 'size' => 17, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.65, 'sizes' => []],
                    'text_color' => '#4D4D54',
                ] ),
                el_spacer( 32 ),
                el_heading( 'Delivered', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
                ] ),
                el_shortcode( '[oem_delivered]' ),
            ] ),
            el_col( 40, [
                el_w( 'html', [
                    'html' => '<div style="background:#F6F6F7;border-radius:8px;padding:28px;border-top:4px solid #E40034;">
<h3 style="font:700 20px/1.3 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 20px;">Fact sheet</h3>
[oem_facts]
<div style="margin-top:24px;">
<a href="/contact/" style="display:inline-flex;align-items:center;gap:8px;padding:14px 24px;background:#E40034;color:#fff;border-radius:6px;font:700 15px/1 \'Acumin Pro\',sans-serif;text-decoration:none;width:100%;justify-content:center;">Discuss a similar scope <i class="fas fa-arrow-right"></i></a>
</div></div>',
                ] ),
            ] ),
        ],
        [
            'structure' => '22',
            'padding' => pad(0, 80),
            'gap' => ['unit' => 'px', 'size' => 48],
        ]
    ),
];

create_theme_template( 'OEM Single Project', 'single', ['include/singular/oem_project'], $single_data );

// --- ARCHIVE OEM_PROJECT ---
$archive_data = [
    // Header
    el_section(
        [el_col( 100, [
            el_eyebrow( 'Projects' ),
            el_heading( 'Selected work.', 'h1', [
                'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
            ] ),
            el_text( '<p>Eight recent projects that illustrate our range — from single-system service calls to multi-discipline refits and fleet-wide digital rollouts.</p>', [
                'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                'typography_line_height' => ['unit' => 'em', 'size' => 1.55, 'sizes' => []],
                'text_color' => '#7A7A82',
            ] ),
        ] )],
        [
            'background_background' => 'classic',
            'background_color' => '#F6F6F7',
            'padding' => pad(80, 80),
        ]
    ),

    // Posts Grid
    el_section(
        [el_col( 100, [
            el_w( 'posts', [
                'posts_post_type' => 'oem_project',
                'posts_per_page'  => 12,
                'columns'         => '3',
                'row_gap'         => ['unit' => 'px', 'size' => 32],
                'column_gap'      => ['unit' => 'px', 'size' => 32],
                'thumbnail'       => 'yes',
                'thumbnail_size'  => 'medium_large',
                'thumbnail_ratio' => '3:2',
                'show_title'      => 'yes',
                'title_tag'       => 'h3',
                'show_excerpt'    => 'no',
                'show_read_more'  => 'no',
                'show_date'       => 'no',
                'orderby'         => 'menu_order',
                'order'           => 'asc',
            ] ),
        ] )],
        ['padding' => pad(64, 80)]
    ),
];

create_theme_template( 'OEM Archive Projects', 'archive', ['include/archive/oem_project'], $archive_data );

echo "  Theme Builder templates created.\n";

// ─── 3. Convert Pages to Elementor ────────────────────────────────────────────

echo "\n3. Converting pages to Elementor...\n";

// Load service data from existing functions for content
require_once get_stylesheet_directory() . '/functions.php';
$services  = oem_get_services();
$team_data = oem_get_team();
$vacancies = oem_get_vacancies();
$locations = oem_get_locations();

// --- HOME ---
$home_id = (int) get_option( 'page_on_front' );
if ( $home_id ) {
    // Service cards for What We Do section
    $svc_widgets = [];
    foreach ( $services as $s ) {
        $svc_widgets[] = el_icon_box( $s['icon'], $s['title'], $s['short'], [
            'link' => ['url' => '/what-we-do/' . $s['slug'] . '/', 'is_external' => '', 'nofollow' => ''],
        ] );
    }

    $home_data = [
        // HERO
        el_section(
            [el_col( 100, [
                el_eyebrow( 'OEM Yacht Service', ['title_color' => 'rgba(255,255,255,.6)'] ),
                el_heading( 'Independent yacht support. OEM-grade knowledge.', 'h1', [
                    'title_color' => '#FFFFFF',
                    'typography_font_size' => ['unit' => 'px', 'size' => 52, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.08, 'sizes' => []],
                ] ),
                el_text( '<p style="max-width:560px;color:rgba(255,255,255,.82);">Service, refit and control technology for superyachts, delivered from Bolsward, Barcelona and Fort Lauderdale.</p>', [
                    'text_color' => 'rgba(255,255,255,.82)',
                    'typography_font_size' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
                    'typography_font_weight' => '300',
                ] ),
                el_spacer( 24 ),
                el_w( 'html', [
                    'html' => '<div style="display:flex;gap:12px;flex-wrap:wrap;">
<a href="/contact/" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:#E40034;color:#fff;border-radius:6px;font:700 16px/1 \'Acumin Pro\',sans-serif;text-decoration:none;">Request a survey <i class="fas fa-arrow-right"></i></a>
<a href="/oem-connect/" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.35);border-radius:6px;font:700 16px/1 \'Acumin Pro\',sans-serif;text-decoration:none;">OEM Connect</a>
</div>',
                ] ),
            ] )],
            [
                'background_background' => 'video',
                'background_video_link' => get_stylesheet_directory_uri() . '/assets/hero-bg.mov',
                'background_video_fallback' => [],
                'background_overlay_background' => 'classic',
                'background_overlay_color' => 'rgba(27,27,31,0.65)',
                'padding' => ['unit' => 'px', 'top' => '140', 'right' => '0', 'bottom' => '140', 'left' => '0', 'isLinked' => false],
            ]
        ),

        // PILLARS
        el_section(
            [
                el_col( 33, [
                    el_icon_box( 'fa-anchor', 'Only Service & Refit', 'No new-build distractions. Every engineer, every tool and every hour is dedicated to keeping existing yachts running.' ),
                ] ),
                el_col( 33, [
                    el_icon_box( 'fa-layer-group', 'One Stop Shop', 'Hydraulics, electrics, mechanics, cranes, boarding and hull doors — one team, one contract, one responsibility.' ),
                ] ),
                el_col( 33, [
                    el_icon_box( 'fa-signal', 'OEM Connect', 'Live diagnostics and remote support via our secure platform — reducing downtime and unplanned yard visits.' ),
                ] ),
            ],
            [
                'structure' => '30',
                'padding' => pad(64, 64),
                'border_border' => 'solid',
                'border_width' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false],
                'border_color' => '#ECECEE',
            ]
        ),

        // WHAT WE DO
        el_section(
            [el_col( 100, [
                el_eyebrow( '01 — What we do' ),
                el_heading( 'Six disciplines, one service organisation.', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 36, 'sizes' => []],
                ] ),
                el_spacer( 32 ),
                el_w( 'html', [
                    'html' => oem_build_service_cards_html( $services ),
                ] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
            ]
        ),

        // PROJECTS
        el_section(
            [el_col( 100, [
                el_w( 'html', [
                    'html' => '<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:8px;flex-wrap:wrap;gap:16px;"><div>',
                ] ),
                el_eyebrow( '02 — Projects' ),
                el_heading( 'Selected work.', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 36, 'sizes' => []],
                ] ),
                el_w( 'html', [
                    'html' => '</div><a href="/projects/" style="font:700 15px/1 \'Acumin Pro\',sans-serif;color:#E40034;text-decoration:none;">All projects <i class="fas fa-arrow-right"></i></a></div>',
                ] ),
                el_spacer( 24 ),
                el_w( 'posts', [
                    'posts_post_type' => 'oem_project',
                    'posts_per_page'  => 4,
                    'columns'         => '4',
                    'thumbnail'       => 'yes',
                    'thumbnail_size'  => 'medium_large',
                    'show_title'      => 'yes',
                    'title_tag'       => 'h3',
                    'show_excerpt'    => 'no',
                    'show_read_more'  => 'no',
                    'show_date'       => 'no',
                    'orderby'         => 'menu_order',
                    'order'           => 'asc',
                ] ),
            ] )],
            ['padding' => pad(80, 80)]
        ),

        // OEM CONNECT PROMO
        el_section(
            [
                el_col( 60, [
                    el_eyebrow( 'OEM Connect', ['title_color' => '#E40034'] ),
                    el_heading( 'Remote diagnostics. Predictive maintenance.', 'h2', [
                        'title_color' => '#FFFFFF',
                        'typography_font_size' => ['unit' => 'px', 'size' => 36, 'sizes' => []],
                    ] ),
                    el_text( '<p style="max-width:480px;">Our secure platform connects your on-board equipment to our engineering team — live sensor data, remote PLC access and a complete digital service history per serial number.</p>', [
                        'text_color' => 'rgba(255,255,255,.82)',
                        'typography_font_weight' => '300',
                        'typography_font_size' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
                    ] ),
                    el_spacer( 16 ),
                    el_button( 'Explore OEM Connect', '/oem-connect/' ),
                ] ),
                el_col( 40, [
                    el_w( 'html', [
                        'html' => '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:24px;">
<div style="text-align:center;"><p style="font:700 26px/1.2 \'Acumin Pro\',sans-serif;color:#fff;margin:0;">3 bases</p><p style="font:300 14px/1.45 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.55);margin:4px 0 0;">Bolsward &middot; Barcelona &middot; Fort Lauderdale</p></div>
<div style="text-align:center;"><p style="font:700 26px/1.2 \'Acumin Pro\',sans-serif;color:#fff;margin:0;">&lt; 1 day</p><p style="font:300 14px/1.45 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.55);margin:4px 0 0;">Average remote-response time</p></div>
<div style="text-align:center;"><p style="font:700 26px/1.2 \'Acumin Pro\',sans-serif;color:#fff;margin:0;">Per serial</p><p style="font:300 14px/1.45 \'Acumin Pro\',sans-serif;color:rgba(255,255,255,.55);margin:4px 0 0;">Full service history by equipment serial</p></div>
</div>',
                    ] ),
                ] ),
            ],
            [
                'structure' => '22',
                'background_background' => 'classic',
                'background_color' => '#1B1B1F',
                'padding' => pad(80, 80),
                'gap' => ['unit' => 'px', 'size' => 48],
            ]
        ),
    ];

    set_elementor_data( $home_id, $home_data );
    echo "  Home page converted.\n";
}

// Helper: build service cards HTML for use in HTML widget
function oem_build_service_cards_html( $services ) {
    $html = '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">';
    foreach ( $services as $s ) {
        $html .= '<a href="/what-we-do/' . esc_attr( $s['slug'] ) . '/" style="display:block;padding:32px;background:#fff;border-radius:8px;text-decoration:none;border:1px solid #ECECEE;transition:border-color .2s;">
<div style="width:48px;height:48px;border-radius:50%;background:rgba(228,0,52,.08);display:flex;align-items:center;justify-content:center;margin-bottom:16px;"><i class="fas ' . esc_attr( $s['icon'] ) . '" style="font-size:20px;color:#E40034;"></i></div>
<h3 style="font:700 18px/1.25 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 8px;">' . esc_html( $s['title'] ) . '</h3>
<p style="font:400 15px/1.55 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0 0 16px;">' . esc_html( $s['short'] ) . '</p>
<span style="font:700 14px/1 \'Acumin Pro\',sans-serif;color:#E40034;">Read more <i class="fas fa-arrow-right"></i></span>
</a>';
    }
    $html .= '</div>';
    return $html;
}

// --- WHAT WE DO INDEX ---
$wwd_page = get_page_by_path( 'what-we-do' );
if ( $wwd_page ) {
    $wwd_data = [
        el_section(
            [el_col( 100, [
                el_eyebrow( 'What we do' ),
                el_heading( 'Six disciplines, one service organisation.', 'h1', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                ] ),
                el_text( '<p>Hydraulics, electrics, mechanics, cranes, boarding equipment and hull doors — maintained, repaired and upgraded by OEM-trained engineers from three bases worldwide.</p>', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                    'text_color' => '#7A7A82',
                ] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
            ]
        ),
        el_section(
            [el_col( 100, [
                el_w( 'html', ['html' => oem_build_service_cards_html( $services )] ),
            ] )],
            ['padding' => pad(64, 80)]
        ),
    ];
    set_elementor_data( $wwd_page->ID, $wwd_data );
    echo "  What We Do index converted.\n";
}

// --- SERVICE DETAIL PAGES ---
foreach ( $services as $idx => $s ) {
    $page = get_page_by_path( $s['slug'], OBJECT, 'page' );
    if ( ! $page ) {
        $children = get_pages(['child_of' => $wwd_page->ID ?? 0, 'post_type' => 'page']);
        foreach ( $children as $child ) {
            if ( $child->post_name === $s['slug'] ) { $page = $child; break; }
        }
    }
    if ( ! $page ) { echo "  WARNING: Service page '{$s['slug']}' not found.\n"; continue; }

    $scope_html = '<ul style="list-style:none;padding:0;margin:0;">';
    foreach ( $s['scope'] as $item ) {
        $scope_html .= '<li style="padding:8px 0 8px 20px;border-bottom:1px solid #ECECEE;position:relative;font:400 16px/1.6 \'Acumin Pro\',sans-serif;color:#4D4D54;"><span style="position:absolute;left:0;color:#E40034;">&#x2022;</span>' . esc_html( $item ) . '</li>';
    }
    $scope_html .= '</ul>';

    $specs_html = '<table style="width:100%;border-collapse:collapse;">';
    foreach ( $s['specs'] as $spec ) {
        $specs_html .= '<tr><td style="padding:10px 16px 10px 0;border-bottom:1px solid #ECECEE;font:700 15px/1.4 \'Acumin Pro\',sans-serif;color:#1B1B1F;white-space:nowrap;">' . esc_html( $spec['k'] ) . '</td><td style="padding:10px 0;border-bottom:1px solid #ECECEE;font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:#4D4D54;">' . esc_html( $spec['v'] ) . '</td></tr>';
    }
    $specs_html .= '</table>';

    $related = array_values( array_filter( $services, fn($x) => $x['slug'] !== $s['slug'] ) );
    $related = array_slice( $related, 0, 3 );
    $related_html = '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">';
    foreach ( $related as $r ) {
        $related_html .= '<a href="/what-we-do/' . esc_attr( $r['slug'] ) . '/" style="display:block;padding:32px;background:#fff;border-radius:8px;text-decoration:none;border:1px solid #ECECEE;">
<div style="width:48px;height:48px;border-radius:50%;background:rgba(228,0,52,.08);display:flex;align-items:center;justify-content:center;margin-bottom:16px;"><i class="fas ' . esc_attr( $r['icon'] ) . '" style="font-size:20px;color:#E40034;"></i></div>
<h3 style="font:700 18px/1.25 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 8px;">' . esc_html( $r['title'] ) . '</h3>
<p style="font:400 15px/1.55 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0 0 16px;">' . esc_html( $r['short'] ) . '</p>
<span style="font:700 14px/1 \'Acumin Pro\',sans-serif;color:#E40034;">Read more <i class="fas fa-arrow-right"></i></span></a>';
    }
    $related_html .= '</div>';

    $svc_data = [
        // Header
        el_section(
            [
                el_col( 50, [
                    el_w( 'text-editor', [
                        'editor' => '<p style="font:400 14px/1.4 \'Acumin Pro\',sans-serif;"><a href="/what-we-do/" style="color:#E40034;text-decoration:none;">What we do</a> / <span style="color:#7A7A82;">' . esc_html( $s['title'] ) . '</span></p>',
                    ] ),
                    el_eyebrow( $s['eyebrow'] ),
                    el_heading( $s['headline'], 'h1', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 42, 'sizes' => []],
                        'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                    ] ),
                    el_text( '<p>' . esc_html( $s['intro'] ) . '</p>', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
                    ] ),
                ] ),
                el_col( 50, [
                    el_image_placeholder( '320px' ),
                ] ),
            ],
            [
                'structure' => '20',
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
                'gap' => ['unit' => 'px', 'size' => 48],
            ]
        ),

        // Scope + Specs
        el_section(
            [
                el_col( 50, [
                    el_heading( 'Scope of work', 'h2', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
                    ] ),
                    el_spacer( 16 ),
                    el_w( 'html', ['html' => $scope_html] ),
                ] ),
                el_col( 50, [
                    el_heading( 'Specification', 'h2', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
                    ] ),
                    el_spacer( 16 ),
                    el_w( 'html', ['html' => $specs_html] ),
                ] ),
            ],
            [
                'structure' => '20',
                'padding' => pad(64, 64),
                'gap' => ['unit' => 'px', 'size' => 48],
            ]
        ),

        // Related
        el_section(
            [el_col( 100, [
                el_heading( 'Also in What we do', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 30, 'sizes' => []],
                ] ),
                el_spacer( 24 ),
                el_w( 'html', ['html' => $related_html] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(64, 64),
            ]
        ),
    ];

    set_elementor_data( $page->ID, $svc_data );
    echo "  Service page '{$s['title']}' converted.\n";
}

// --- OEM CONNECT ---
$connect_page = get_page_by_path( 'oem-connect' );
if ( $connect_page ) {
    $features = [
        ['icon' => 'fa-signal',         'title' => 'Live diagnostics',     'desc' => 'Real-time sensor data streamed from on-board PLCs to our engineering dashboard — pressure, temperature, position, runtime hours.'],
        ['icon' => 'fa-microchip',      'title' => 'Remote logic updates', 'desc' => 'We push PLC software changes over a secure tunnel — no on-board visit required for parameter tweaks, sequence adjustments or firmware patches.'],
        ['icon' => 'fa-clipboard-list', 'title' => 'Service history',      'desc' => 'Every intervention, every spare part and every test result logged against the equipment serial number — accessible to crew and management.'],
        ['icon' => 'fa-wrench',         'title' => 'Direct line',          'desc' => 'One tap connects the crew to the engineer who last worked on that system — no call centres, no ticket queues.'],
    ];

    $feat_widgets = [];
    foreach ( $features as $f ) {
        $feat_widgets[] = el_icon_box( $f['icon'], $f['title'], $f['desc'] );
    }

    $steps = [
        ['n' => '01', 'title' => 'Crew reports',  'desc' => 'The crew taps a single button on the bridge panel or the OEM Connect app to flag a system.'],
        ['n' => '02', 'title' => 'We read out',   'desc' => "Our engineer opens a secure tunnel to the vessel's PLC and reads live diagnostics within minutes."],
        ['n' => '03', 'title' => 'Fix or plan',   'desc' => "If it's a parameter or logic issue, we push a fix remotely. If hardware is needed, we dispatch a team with the right parts."],
        ['n' => '04', 'title' => 'Logged',         'desc' => "Every action — remote or on-board — is logged against the equipment serial in the vessel's digital service history."],
    ];

    $steps_html = '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;">';
    foreach ( $steps as $st ) {
        $steps_html .= '<div style="border-top:3px solid #E40034;padding-top:20px;">
<div style="font:700 28px/1 \'Acumin Pro\',sans-serif;color:#E40034;margin-bottom:12px;">' . $st['n'] . '</div>
<h3 style="font:700 18px/1.25 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 8px;">' . esc_html( $st['title'] ) . '</h3>
<p style="font:400 15px/1.55 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0;">' . esc_html( $st['desc'] ) . '</p></div>';
    }
    $steps_html .= '</div>';

    $connect_data = [
        // Dark Header
        el_section(
            [el_col( 100, [
                el_eyebrow( 'OEM Connect', ['title_color' => '#E40034'] ),
                el_heading( 'Remote diagnostics. Predictive maintenance. One platform.', 'h1', [
                    'title_color' => '#FFFFFF',
                    'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                ] ),
                el_text( '<p>Our secure platform connects your on-board equipment to our engineering team — live sensor data, remote PLC access and a complete digital service history per serial number.</p>', [
                    'text_color' => 'rgba(255,255,255,.82)',
                    'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                ] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#1B1B1F',
                'padding' => pad(80, 80),
            ]
        ),

        // Features (2x2 grid using icon-box widgets in HTML grid)
        el_section(
            [el_col( 100, [
                el_w( 'html', [
                    'html' => '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:32px;">' .
                        implode( '', array_map( function($f) {
                            return '<div style="padding:24px 0;">
<div style="width:48px;height:48px;border-radius:50%;background:rgba(228,0,52,.08);display:flex;align-items:center;justify-content:center;margin-bottom:16px;"><i class="fas ' . esc_attr( $f['icon'] ) . '" style="font-size:20px;color:#E40034;"></i></div>
<h3 style="font:700 18px/1.25 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 8px;">' . esc_html( $f['title'] ) . '</h3>
<p style="font:400 15px/1.55 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0;">' . esc_html( $f['desc'] ) . '</p></div>';
                        }, $features ) ) .
                    '</div>',
                ] ),
            ] )],
            ['padding' => pad(64, 64)]
        ),

        // Steps
        el_section(
            [el_col( 100, [
                el_heading( 'How a service call runs', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 36, 'sizes' => []],
                ] ),
                el_spacer( 32 ),
                el_w( 'html', ['html' => $steps_html] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(64, 64),
            ]
        ),
    ];

    set_elementor_data( $connect_page->ID, $connect_data );
    echo "  OEM Connect converted.\n";
}

// --- TEAM ---
function build_team_page_data( $group, $active_tab ) {
    $other_tab = $active_tab === 'Team' ? 'Team Sub' : 'Team';
    $other_url = $active_tab === 'Team' ? '/team-sub/' : '/team/';

    $people_html = '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;">';
    foreach ( $group['people'] as $person ) {
        $people_html .= '<div style="text-align:center;">
<div style="width:120px;height:120px;border-radius:50%;background:#ECECEE;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-user" style="font-size:32px;color:#C9C9CD;"></i></div>
<h3 style="font:700 17px/1.3 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 4px;">' . esc_html( $person['name'] ) . '</h3>
<p style="font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0;">' . esc_html( $person['role'] ) . '</p>
<p style="font:400 14px/1.4 \'Acumin Pro\',sans-serif;color:#A4A4AA;margin:4px 0 0;">' . esc_html( $person['base'] ) . '</p></div>';
    }
    $people_html .= '</div>';

    $tabs_html = '<div style="display:flex;gap:8px;margin-top:24px;">';
    if ( $active_tab === 'Team' ) {
        $tabs_html .= '<span style="padding:8px 20px;background:#1B1B1F;color:#fff;border-radius:20px;font:700 14px/1 \'Acumin Pro\',sans-serif;">Team</span>';
        $tabs_html .= '<a href="/team-sub/" style="padding:8px 20px;background:#ECECEE;color:#4D4D54;border-radius:20px;font:700 14px/1 \'Acumin Pro\',sans-serif;text-decoration:none;">Team Sub</a>';
    } else {
        $tabs_html .= '<a href="/team/" style="padding:8px 20px;background:#ECECEE;color:#4D4D54;border-radius:20px;font:700 14px/1 \'Acumin Pro\',sans-serif;text-decoration:none;">Team</a>';
        $tabs_html .= '<span style="padding:8px 20px;background:#1B1B1F;color:#fff;border-radius:20px;font:700 14px/1 \'Acumin Pro\',sans-serif;">Team Sub</span>';
    }
    $tabs_html .= '</div>';

    return [
        el_section(
            [el_col( 100, [
                el_eyebrow( $group['eyebrow'] ),
                el_heading( $group['headline'], 'h1', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                ] ),
                el_text( '<p>' . esc_html( $group['intro'] ) . '</p>', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                    'text_color' => '#7A7A82',
                ] ),
                el_w( 'html', ['html' => $tabs_html] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
            ]
        ),
        el_section(
            [el_col( 100, [
                el_w( 'html', ['html' => $people_html] ),
            ] )],
            ['padding' => pad(64, 80)]
        ),
    ];
}

$team_page = get_page_by_path( 'team' );
if ( $team_page ) {
    set_elementor_data( $team_page->ID, build_team_page_data( $team_data['team'], 'Team' ) );
    echo "  Team page converted.\n";
}

$sub_page = get_page_by_path( 'team-sub' );
if ( $sub_page ) {
    set_elementor_data( $sub_page->ID, build_team_page_data( $team_data['sub'], 'Team Sub' ) );
    echo "  Team Sub page converted.\n";
}

// --- CAREERS ---
$careers_page = get_page_by_path( 'careers' );
if ( $careers_page ) {
    $vac_html = '';
    foreach ( $vacancies as $v ) {
        $vac_html .= '<a href="/contact/" style="display:grid;grid-template-columns:1.5fr 1fr 0.8fr auto;gap:16px;align-items:center;padding:20px 0;border-bottom:1px solid #ECECEE;text-decoration:none;">
<span style="font:700 17px/1.3 \'Acumin Pro\',sans-serif;color:#1B1B1F;">' . esc_html( $v['title'] ) . '</span>
<span style="font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:#7A7A82;">' . esc_html( $v['location'] ) . '</span>
<span style="font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:#7A7A82;">' . esc_html( $v['type'] ) . '</span>
<span style="color:#E40034;"><i class="fas fa-arrow-right"></i></span></a>';
    }

    $careers_data = [
        el_section(
            [
                el_col( 50, [
                    el_eyebrow( 'Careers' ),
                    el_heading( 'Build superyacht systems. See the world.', 'h1', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                        'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                    ] ),
                    el_text( '<p>We are always looking for experienced marine engineers and technicians who want to work on the most complex yacht systems afloat — from our workshop in Bolsward or on board worldwide.</p>', [
                        'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                        'text_color' => '#7A7A82',
                    ] ),
                ] ),
                el_col( 50, [
                    el_image_placeholder( '320px' ),
                ] ),
            ],
            [
                'structure' => '20',
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
                'gap' => ['unit' => 'px', 'size' => 48],
            ]
        ),
        el_section(
            [el_col( 100, [
                el_heading( 'Open positions', 'h2', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
                ] ),
                el_spacer( 16 ),
                el_w( 'html', ['html' => $vac_html] ),
            ] )],
            ['padding' => pad(64, 64)]
        ),
        el_section(
            [el_col( 100, [
                el_w( 'html', [
                    'html' => '<div style="background:#F6F6F7;border-radius:8px;padding:40px 48px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:24px;">
<div><h3 style="font:700 22px/1.2 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 4px;">Don\'t see your role?</h3><p style="font:400 16px/1.5 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0;">Send an open application — we\'re always interested in experienced marine engineers.</p></div>
<a href="mailto:info@oemyachtservice.com" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;background:#E40034;color:#fff;border-radius:6px;font:700 16px/1 \'Acumin Pro\',sans-serif;text-decoration:none;white-space:nowrap;">Open application <i class="fas fa-arrow-right"></i></a></div>',
                ] ),
            ] )],
            ['padding' => pad(0, 80)]
        ),
    ];

    set_elementor_data( $careers_page->ID, $careers_data );
    echo "  Careers page converted.\n";
}

// --- CONTACT ---
$contact_page = get_page_by_path( 'contact' );
if ( $contact_page ) {
    $loc_html = '';
    foreach ( $locations as $loc ) {
        $loc_html .= '<div style="margin-bottom:32px;">
<h3 style="font:700 18px/1.25 \'Acumin Pro\',sans-serif;color:#1B1B1F;margin:0 0 4px;">' . esc_html( $loc['city'] ) . '</h3>
<p style="font:400 14px/1.4 \'Acumin Pro\',sans-serif;color:#E40034;margin:0 0 8px;">' . esc_html( $loc['role'] ) . '</p>
<p style="font:400 15px/1.55 \'Acumin Pro\',sans-serif;color:#4D4D54;margin:0 0 4px;">' . esc_html( $loc['address'] ) . '</p>
<a href="' . esc_attr( $loc['tel_href'] ) . '" style="font:400 15px/1.4 \'Acumin Pro\',sans-serif;color:#E40034;text-decoration:none;">' . esc_html( $loc['tel'] ) . '</a></div>';
    }

    $discipline_options = '';
    foreach ( $services as $s ) {
        $discipline_options .= $s['title'] . "\n";
    }

    $contact_data = [
        el_section(
            [el_col( 100, [
                el_eyebrow( 'Contact' ),
                el_heading( 'Request a survey or get in touch.', 'h1', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
                    'typography_line_height' => ['unit' => 'em', 'size' => 1.1, 'sizes' => []],
                ] ),
                el_text( '<p>Tell us about your vessel and the scope of work — we will come back to you within one working day.</p>', [
                    'typography_font_size' => ['unit' => 'px', 'size' => 19, 'sizes' => []],
                    'text_color' => '#7A7A82',
                ] ),
            ] )],
            [
                'background_background' => 'classic',
                'background_color' => '#F6F6F7',
                'padding' => pad(80, 80),
            ]
        ),
        el_section(
            [
                el_col( 60, [
                    el_w( 'form', [
                        'form_name' => 'Contact Form',
                        'form_fields' => [
                            [
                                'custom_id' => 'name',
                                'field_type' => 'text',
                                'field_label' => 'Name',
                                'placeholder' => '',
                                'required' => 'true',
                                'width' => '50',
                                '_id' => eid(),
                            ],
                            [
                                'custom_id' => 'email',
                                'field_type' => 'email',
                                'field_label' => 'Email',
                                'placeholder' => '',
                                'required' => 'true',
                                'width' => '50',
                                '_id' => eid(),
                            ],
                            [
                                'custom_id' => 'vessel',
                                'field_type' => 'text',
                                'field_label' => 'Vessel',
                                'placeholder' => 'Yard, length',
                                'required' => '',
                                'width' => '50',
                                '_id' => eid(),
                            ],
                            [
                                'custom_id' => 'discipline',
                                'field_type' => 'select',
                                'field_label' => 'Discipline',
                                'field_options' => trim( $discipline_options ),
                                'required' => '',
                                'width' => '50',
                                '_id' => eid(),
                            ],
                            [
                                'custom_id' => 'scope',
                                'field_type' => 'textarea',
                                'field_label' => 'Scope',
                                'placeholder' => 'Describe the work you need…',
                                'required' => '',
                                'width' => '100',
                                'rows' => '5',
                                '_id' => eid(),
                            ],
                        ],
                        'button_text' => 'Request a survey',
                        'button_size' => 'md',
                        'button_background_color' => '#E40034',
                        'button_color' => '#FFFFFF',
                        'email_to' => 'info@oemyachtservice.com',
                        'email_subject' => 'Survey request from {{name}}',
                        'selected_icon' => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid'],
                        'button_icon_align' => 'right',
                    ] ),
                ] ),
                el_col( 40, [
                    el_w( 'html', ['html' => $loc_html] ),
                ] ),
            ],
            [
                'structure' => '22',
                'padding' => pad(64, 80),
                'gap' => ['unit' => 'px', 'size' => 48],
            ]
        ),
    ];

    set_elementor_data( $contact_page->ID, $contact_data );
    echo "  Contact page converted.\n";
}

// ─── 4. Swap functions.php ─────────────────────────────────────────────────────

echo "\n4. Swapping functions.php...\n";

$theme_dir = get_stylesheet_directory();
$old_functions = $theme_dir . '/functions.php';
$new_functions = $theme_dir . '/functions-elementor.php';
$backup = $theme_dir . '/functions-backup.php';

if ( file_exists( $new_functions ) ) {
    if ( file_exists( $old_functions ) ) {
        rename( $old_functions, $backup );
        echo "  Backed up old functions.php → functions-backup.php\n";
    }
    rename( $new_functions, $old_functions );
    echo "  Installed new functions.php (Elementor Pro version)\n";
} else {
    echo "  WARNING: functions-elementor.php not found. Manual swap needed.\n";
}

// ─── 5. Strip style.css ───────────────────────────────────────────────────────

echo "\n5. Stripping style.css...\n";

$style_content = "/*
Theme Name: OEM Yacht Service
Theme URI: https://oemyachtservice.com
Description: Elementor Pro child theme for OEM Yacht Service
Author: eFabriek
Author URI: https://efabriek.nl
Template: hello-elementor
Version: 2.0.0
Text Domain: oem-yacht
*/

/* Minimal eyebrow styling — used by Elementor heading widgets with class 'oem-eyebrow' */
.oem-eyebrow {
  display: inline-block;
}
.oem-eyebrow::before {
  content: '';
  display: inline-block;
  width: 24px;
  height: 2px;
  background: #E40034;
  vertical-align: middle;
  margin-right: 10px;
}
";

file_put_contents( $theme_dir . '/style.css', $style_content );
echo "  style.css stripped to theme header + eyebrow pseudo-element.\n";

// ─── 6. Remove old template files ─────────────────────────────────────────────

echo "\n6. Removing old custom template files...\n";

$files_to_remove = [
    'header.php',
    'footer.php',
    'page.php',
    'single-oem_project.php',
    'archive-oem_project.php',
    'create-pages.php',
    'migrate-projects-cpt.php',
];

foreach ( $files_to_remove as $file ) {
    $path = $theme_dir . '/' . $file;
    if ( file_exists( $path ) ) {
        unlink( $path );
        echo "  Removed: $file\n";
    }
}

// Keep migrate-to-elementor.php and functions-backup.php for reference

// ─── 7. Deactivate Contact Form 7 ────────────────────────────────────────────

echo "\n7. Deactivating Contact Form 7...\n";

if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
    deactivate_plugins( 'contact-form-7/wp-contact-form-7.php' );
    echo "  Contact Form 7 deactivated (Elementor Form widget replaces it).\n";
} else {
    echo "  Contact Form 7 was not active.\n";
}

// ─── 8. Flush rewrite rules ──────────────────────────────────────────────────

flush_rewrite_rules();

echo "\n=== Migration complete! ===\n";
echo "Next steps:\n";
echo "  1. Set up Adobe Fonts for Acumin Pro:\n";
echo "     a. Go to https://fonts.adobe.com and create a Web Project\n";
echo "     b. Add Acumin Pro with weights: Light (300), Regular (400), Bold (700)\n";
echo "     c. Copy the Project ID (e.g. 'abc1def')\n";
echo "     d. Add to wp-config.php: define( 'OEM_TYPEKIT_ID', 'your-project-id' );\n";
echo "     e. Or: Elementor → Settings → Integrations → Adobe Fonts → enter Project ID\n";
echo "  2. Open any page in Elementor editor to verify visual editing works\n";
echo "  3. Replace the HTML header template with proper Elementor Pro widgets:\n";
echo "     - Site Logo widget\n";
echo "     - Nav Menu widget (location: primary)\n";
echo "     - Button widget (Contact CTA)\n";
echo "  4. Same for the footer — replace HTML widget with Elementor widgets\n";
echo "  5. Check responsive preview for all pages\n";
echo "  6. Verify the contact form sends emails\n";
echo "  7. Delete functions-backup.php when satisfied\n";
