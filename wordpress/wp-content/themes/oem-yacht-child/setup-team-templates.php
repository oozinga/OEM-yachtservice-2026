<?php
/**
 * OEM Yacht Service — Team Templates Setup Script
 *
 * Creates Elementor Theme Builder templates for the oem_team_member CPT:
 *   - Archive template (/team/) with hero + department grids
 *   - Single template (/team/[slug]/) with hero, image, content, fact sheet
 *
 * Run once:
 *   php -r "require_once 'wp-load.php'; include 'wp-content/themes/oem-yacht-child/setup-team-templates.php';"
 * Or via WP-CLI:
 *   wp eval-file wp-content/themes/oem-yacht-child/setup-team-templates.php --allow-root
 */

$marker = get_option( 'oem_team_templates_setup_done' );
if ( $marker ) {
    echo "Team templates already created. Delete option 'oem_team_templates_setup_done' to re-run.\n";
    return;
}

function eid() {
    return substr( md5( uniqid( mt_rand(), true ) ), 0, 7 );
}

function oem_create_template( $title, $type, $conditions, $data ) {
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

// ─── Archive Template (/team/) ───

$archive_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '64', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#F6F6F7',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0' ],
            'border_color' => '#ECECEE',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'ABOUT — TEAM',
                        'header_size' => 'div',
                        'css_classes' => 'oem-eyebrow',
                        'title_color' => '#7A7A82',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 12 ],
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 1.7 ],
                        'typography_text_transform' => 'uppercase',
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Our Team',
                        'header_size' => 'h1',
                        'title_color' => '#1B1B1F',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 52 ],
                        'typography_font_size_tablet' => [ 'unit' => 'px', 'size' => 40 ],
                        'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 34 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.06 ],
                        'typography_letter_spacing' => [ 'unit' => 'em', 'size' => -0.015 ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [
                        'editor' => '<p>OEM Yacht Service brings together naval engineers, service coordinators and field specialists — each with deep experience in superyacht systems.</p>',
                        'text_color' => '#4D4D54',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '300',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.5 ],
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Management & Engineering',
                        'header_size' => 'h2',
                        'title_color' => '#1B1B1F',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.2 ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [
                        'editor' => '<p>Management, engineering and project coordination, based at the workshop in Bolsward.</p>',
                        'text_color' => '#4D4D54',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '400',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.6 ],
                        '_padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '40', 'left' => '0' ],
                        '_border_border' => 'solid',
                        '_border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0' ],
                        '_border_color' => '#ECECEE',
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                    'settings' => [
                        'html' => '[oem_team_grid department="Management & Engineering"]',
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#F6F6F7',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Field Teams & Specialists',
                        'header_size' => 'h2',
                        'title_color' => '#1B1B1F',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.2 ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [
                        'editor' => '<p>Service engineers who fly out to the hull, plus the specialist partners we bring in under our own supervision.</p>',
                        'text_color' => '#4D4D54',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '400',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.6 ],
                        '_padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '40', 'left' => '0' ],
                        '_border_border' => 'solid',
                        '_border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0' ],
                        '_border_color' => '#ECECEE',
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                    'settings' => [
                        'html' => '[oem_team_grid department="Field Teams & Specialists"]',
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
];

$archive_id = oem_create_template(
    'Team Archive',
    'archive',
    [ 'include/archive/oem_team_member' ],
    $archive_data
);
echo "Archive template created (ID: $archive_id).\n";

// ─── Single Template (/team/[slug]/) ───

$single_data = [
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '64', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#F6F6F7',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0' ],
            'border_color' => '#ECECEE',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                    'settings' => [
                        'editor' => '<p><a href="/team/" style="color:#7A7A82;text-decoration:none;">Team</a> / <span style="color:#1B1B1F;">Member</span></p>',
                        '__dynamic__' => (object) [],
                        'text_color' => '#7A7A82',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '400',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Department',
                        'header_size' => 'div',
                        'css_classes' => 'oem-eyebrow',
                        'title_color' => '#7A7A82',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 12 ],
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 1.7 ],
                        'typography_text_transform' => 'uppercase',
                        '__dynamic__' => [
                            'title' => '[elementor-tag id="" name="post-custom-field" settings="%7B%22key%22%3A%22_oem_team_department%22%7D"]',
                        ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Team Member Name',
                        'header_size' => 'h1',
                        'title_color' => '#1B1B1F',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '700',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 52 ],
                        'typography_font_size_tablet' => [ 'unit' => 'px', 'size' => 40 ],
                        'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 34 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.06 ],
                        'typography_letter_spacing' => [ 'unit' => 'em', 'size' => -0.015 ],
                        '__dynamic__' => [
                            'title' => '[elementor-tag id="" name="post-title"]',
                        ],
                    ],
                    'elements' => [],
                ],
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                    'settings' => [
                        'title' => 'Location',
                        'header_size' => 'div',
                        'title_color' => '#4D4D54',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '300',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                        'typography_line_height' => [ 'unit' => 'em', 'size' => 1.5 ],
                        '__dynamic__' => [
                            'title' => '[elementor-tag id="" name="post-custom-field" settings="%7B%22key%22%3A%22_oem_team_base%22%7D"]',
                        ],
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
            'height' => 'min-height',
            'custom_height' => [ 'unit' => 'px', 'size' => 440 ],
            'background_background' => 'classic',
            'background_color' => '#ECECEE',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'image',
                    'settings' => [
                        'image' => [ 'url' => '', 'id' => '' ],
                        'image_size' => 'full',
                        'width' => [ 'unit' => '%', 'size' => 100 ],
                        'height' => [ 'unit' => 'px', 'size' => 440 ],
                        'object-fit' => 'cover',
                        '__dynamic__' => [
                            'image' => '[elementor-tag id="" name="post-featured-image"]',
                        ],
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'extended',
            'padding' => [ 'unit' => 'px', 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'structure' => '21',
        ],
        'elements' => [
            [
                'id' => eid(), 'elType' => 'column',
                'settings' => [ '_column_size' => 66 ],
                'elements' => [
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                        'settings' => [
                            'title' => 'Background',
                            'header_size' => 'h2',
                            'title_color' => '#1B1B1F',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'acumin-pro',
                            'typography_font_weight' => '700',
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ],
                            'typography_line_height' => [ 'unit' => 'em', 'size' => 1.2 ],
                        ],
                        'elements' => [],
                    ],
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
                        'settings' => [
                            'editor' => '',
                            'text_color' => '#4D4D54',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'acumin-pro',
                            'typography_font_weight' => '400',
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 15.5 ],
                            'typography_line_height' => [ 'unit' => 'em', 'size' => 1.65 ],
                            '__dynamic__' => [
                                'editor' => '[elementor-tag id="" name="post-content"]',
                            ],
                        ],
                        'elements' => [],
                    ],
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
                        'settings' => [
                            'title' => 'Expertise',
                            'header_size' => 'h2',
                            'title_color' => '#1B1B1F',
                            'typography_typography' => 'custom',
                            'typography_font_family' => 'acumin-pro',
                            'typography_font_weight' => '700',
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 28 ],
                            'typography_line_height' => [ 'unit' => 'em', 'size' => 1.2 ],
                            '_margin' => [ 'unit' => 'px', 'top' => '40', 'right' => '0', 'bottom' => '0', 'left' => '0' ],
                        ],
                        'elements' => [],
                    ],
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                        'settings' => [ 'html' => '[oem_team_expertise]' ],
                        'elements' => [],
                    ],
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                        'settings' => [ 'html' => '[oem_team_quote]' ],
                        'elements' => [],
                    ],
                ],
            ],
            [
                'id' => eid(), 'elType' => 'column',
                'settings' => [ '_column_size' => 33 ],
                'elements' => [
                    [
                        'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
                        'settings' => [ 'html' => '[oem_team_facts]' ],
                        'elements' => [],
                    ],
                ],
            ],
        ],
    ],
    [
        'id' => eid(), 'elType' => 'section',
        'settings' => [
            'layout' => 'full_width',
            'content_width' => [ 'unit' => 'px', 'size' => 1240 ],
            'gap' => 'no',
            'padding' => [ 'unit' => 'px', 'top' => '60', 'right' => '24', 'bottom' => '60', 'left' => '24' ],
            'background_background' => 'classic',
            'background_color' => '#F6F6F7',
            'structure' => '10',
        ],
        'elements' => [[
            'id' => eid(), 'elType' => 'column',
            'settings' => [ '_column_size' => 100 ],
            'elements' => [
                [
                    'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
                    'settings' => [
                        'text' => "\xE2\x86\x90 Back to all team members",
                        'link' => [ 'url' => '/team/', 'is_external' => false, 'nofollow' => false ],
                        'button_type' => 'default',
                        'background_color' => 'transparent',
                        'button_text_color' => '#7A7A82',
                        'border_border' => 'solid',
                        'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1' ],
                        'button_border_color' => '#ECECEE',
                        'border_radius' => [ 'unit' => 'px', 'top' => '6', 'right' => '6', 'bottom' => '6', 'left' => '6' ],
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'acumin-pro',
                        'typography_font_weight' => '600',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                        'text_padding' => [ 'unit' => 'px', 'top' => '12', 'right' => '28', 'bottom' => '12', 'left' => '28' ],
                        'align' => 'left',
                        'hover_color' => '#E40034',
                        'button_background_hover_color' => 'transparent',
                        'button_hover_border_color' => '#E40034',
                    ],
                    'elements' => [],
                ],
            ],
        ]],
    ],
];

$single_id = oem_create_template(
    'Team Member Single',
    'single',
    [ 'include/singular/oem_team_member' ],
    $single_data
);
echo "Single template created (ID: $single_id).\n";

// ─── Flush rewrite rules ───

flush_rewrite_rules();
echo "Rewrite rules flushed.\n";

// ─── Clear Elementor CSS cache ───

delete_post_meta_by_key( '_elementor_css' );
echo "Elementor CSS cache cleared.\n";

update_option( 'oem_team_templates_setup_done', true );
echo "\nDone! Team archive and single templates are ready in Theme Builder.\n";
