<?php

add_action( 'wp_enqueue_scripts', function() {
    wp_deregister_style( 'font-awesome' );
    wp_dequeue_style( 'font-awesome' );
    wp_enqueue_style( 'oem-parent', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'oem-child', get_stylesheet_uri(), ['oem-parent'], '1.0.0' );
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap', [], null );
    wp_enqueue_style( 'font-awesome-6', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1' );
}, 20 );

add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption'] );
    register_nav_menus([
        'primary'          => 'Primary Navigation',
        'footer-services'  => 'Footer – What We Do',
        'footer-projects'  => 'Footer – Projects',
        'footer-about'     => 'Footer – About',
    ]);
});

add_filter( 'upload_mimes', function( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

add_action( 'after_switch_theme', function() {
    update_option( 'elementor_container_width', 1240 );
    update_option( 'elementor_page_title_selector', 'h1.entry-title' );
    update_option( 'elementor_disable_color_schemes', 'yes' );
    update_option( 'elementor_disable_typography_schemes', 'yes' );
});

remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

add_filter( 'body_class', function( $classes ) {
    $classes[] = 'oem-site';
    return $classes;
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

// Shortcode: [oem_projects_grid count="4"]
add_shortcode( 'oem_projects_grid', function( $atts ) {
    $atts = shortcode_atts( ['count' => -1, 'columns' => '3'], $atts );
    $q = new WP_Query([
        'post_type'      => 'oem_project',
        'posts_per_page' => (int) $atts['count'],
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ]);
    if ( ! $q->have_posts() ) return '';
    $cols = $atts['columns'] === '4' ? ' oem-card-grid--4' : '';
    $out = '<div class="oem-card-grid' . $cols . '">';
    while ( $q->have_posts() ) : $q->the_post();
        $tag    = get_post_meta( get_the_ID(), '_oem_tag', true );
        $vessel = get_post_meta( get_the_ID(), '_oem_vessel', true );
        $out .= '<a href="' . get_permalink() . '" class="oem-project-card">';
        if ( has_post_thumbnail() ) {
            $out .= '<div class="oem-project-card__image">' . get_the_post_thumbnail( null, 'medium_large' ) . '</div>';
        } else {
            $out .= '<div class="oem-project-card__image"><i class="fa-solid fa-image"></i></div>';
        }
        $out .= '<div class="oem-project-card__tag">' . esc_html( $tag ) . '</div>';
        $out .= '<h3 class="oem-project-card__title">' . get_the_title() . '</h3>';
        if ( $vessel && $atts['columns'] !== '4' ) {
            $out .= '<p class="oem-project-card__vessel">' . esc_html( $vessel ) . '</p>';
        }
        $out .= '</a>';
    endwhile;
    wp_reset_postdata();
    $out .= '</div>';
    return $out;
});

class OEM_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="sub-menu">';
    }
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }
    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $item = $data_object;
        $classes = empty( $item->classes ) ? [] : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes );

        $slug_classes = [];
        if ( $item->object_id ) {
            $post = get_post( $item->object_id );
            if ( $post ) {
                $slug_classes[] = 'menu-item-' . $post->post_name;
            }
        }

        $li_classes = array_merge( $classes, $slug_classes );
        $output .= '<li class="' . esc_attr( implode( ' ', array_filter( $li_classes ) ) ) . '">';
        $output .= '<a href="' . esc_url( $item->url ) . '">';
        $output .= esc_html( $item->title );
        if ( $has_children && $depth === 0 ) {
            $output .= ' <i class="fa-solid fa-chevron-down"></i>';
        }
        $output .= '</a>';
    }
    public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}

function oem_get_logo_url( $variant = 'red' ) {
    return get_stylesheet_directory_uri() . '/assets/logo-' . $variant . '.svg';
}

function oem_get_services() {
    return [
        [
            'slug'     => 'electrical-control-systems',
            'title'    => 'Electrical control systems',
            'eyebrow'  => 'PL / ECC',
            'nav'      => 'PL/ECC — Electrical control systems',
            'icon'     => 'fa-microchip',
            'short'    => 'PLC & ECC control cabinets, from single-panel upgrades to full vessel automation rewires.',
            'headline' => 'Reliable control technology, built and maintained by OEM-trained engineers.',
            'intro'    => 'Our electrical team designs, builds and commissions PLC and ECC cabinets for superyacht systems — from a single helm-station panel swap to a full vessel-wide automation rewire. We work with Siemens S7, Beckhoff and proprietary OEM platforms and supply stainless IP56-rated enclosures as standard.',
            'scope'    => [
                'PLC / ECC cabinet design, build & commissioning',
                'Siemens S7 & Beckhoff software engineering',
                'Electrical switchboard upgrades',
                'Shore-power and generator control panels',
                'Emergency-mode logic programming',
            ],
            'specs' => [
                ['k' => 'Platforms', 'v' => 'Siemens S7-1500 / S7-300, Beckhoff TwinCAT'],
                ['k' => 'Enclosure', 'v' => 'Stainless steel, IP 56 rated'],
                ['k' => 'Standards', 'v' => 'IEC 61131-3, MCA LY3, Lloyd\'s, DNV'],
                ['k' => 'Commissioning', 'v' => 'FAT, harbour trial, sea trial'],
            ],
        ],
        [
            'slug'     => 'hydraulic',
            'title'    => 'Hydraulic systems',
            'eyebrow'  => 'HYDRAULICS',
            'nav'      => 'Hydraulic systems',
            'icon'     => 'fa-gear',
            'short'    => 'HPU overhauls, valve-block re-manufacturing, cylinder repair — flushed to NAS 6.',
            'headline' => 'Full hydraulic lifecycle support, from HPU overhaul to on-board troubleshooting.',
            'intro'    => 'We service, rebuild and re-manufacture hydraulic power units, valve blocks and cylinders for every on-board system — tender cranes, passerelles, shell doors, stabilisers. All assemblies are bench-tested and flushed to NAS 6 cleanliness before delivery.',
            'scope'    => [
                'HPU overhaul & bench testing',
                'Valve-block re-manufacturing',
                'Cylinder repair & re-chroming',
                'Hose routing & tube bending on board',
                'System flushing to NAS 6 cleanliness',
            ],
            'specs' => [
                ['k' => 'Pressure', 'v' => 'Systems up to 350 bar'],
                ['k' => 'Cleanliness', 'v' => 'Flushed to NAS 6 (ISO 4406)'],
                ['k' => 'Testing', 'v' => 'Flow, pressure & leak-off bench tests'],
                ['k' => 'OEM scope', 'v' => 'Bosch Rexroth, Parker, Hydac, Bucher'],
            ],
        ],
        [
            'slug'     => 'mechanic',
            'title'    => 'Mechanic',
            'eyebrow'  => 'MECHANICAL',
            'nav'      => 'Mechanic',
            'icon'     => 'fa-gears',
            'short'    => 'Laser alignment, vibration analysis, bearing replacement — class-compliant reporting.',
            'headline' => 'Precision mechanical work, from shaft-line alignment to on-board vibration surveys.',
            'intro'    => 'Our mechanical engineers perform laser alignment of shaft lines, gearboxes and gensets, vibration analysis to ISO 20283 and ISO 10816, and bearing replacements — on board or in our workshop. Every job ships with a class-compliant measurement report.',
            'scope'    => [
                'Laser shaft-line alignment',
                'Vibration survey & analysis',
                'Bearing & seal replacement',
                'Genset & gearbox overhaul',
                'Fly-out field service worldwide',
            ],
            'specs' => [
                ['k' => 'Alignment', 'v' => 'Laser (Fixturlaser / Prüftechnik)'],
                ['k' => 'Vibration', 'v' => 'ISO 20283 / ISO 10816 compliant'],
                ['k' => 'Reporting', 'v' => 'Class-accepted measurement reports'],
                ['k' => 'Reach', 'v' => 'Workshop + worldwide fly-out teams'],
            ],
        ],
        [
            'slug'     => 'tender-cranes',
            'title'    => 'Tender Cranes',
            'eyebrow'  => 'CRANES',
            'nav'      => 'Tender Cranes',
            'icon'     => 'fa-arrow-up-from-bracket',
            'short'    => 'Hydraulic, electric and mechanical drive crane systems — class load-tested.',
            'headline' => 'Crane service from scheduled maintenance to full drive-system replacement.',
            'intro'    => 'We maintain, repair and upgrade tender cranes with hydraulic, electric or mechanical drive trains. Services include five-year class load tests, slew-bearing inspection, wire-rope replacement and full drive-unit swaps — all performed to the manufacturer\'s procedures.',
            'scope'    => [
                'Scheduled crane maintenance programmes',
                'Five-year class load testing',
                'Slew-bearing inspection & replacement',
                'Wire-rope & sheave renewal',
                'Drive-unit swap (hydraulic ↔ electric)',
            ],
            'specs' => [
                ['k' => 'Drive types', 'v' => 'Hydraulic, electric, mechanical'],
                ['k' => 'Load test', 'v' => 'Class-witnessed, certified'],
                ['k' => 'OEM scope', 'v' => 'Vestdavit, Allied Marine Crane, Palfinger'],
                ['k' => 'Capacity', 'v' => 'Tenders up to 12 m / 15 t'],
            ],
        ],
        [
            'slug'     => 'boarding',
            'title'    => 'Boarding',
            'eyebrow'  => 'BOARDING',
            'nav'      => 'Boarding',
            'icon'     => 'fa-stairs',
            'short'    => 'Passerelles, accommodation stairs, bathing platforms — damped actuation, silent operation.',
            'headline' => 'Boarding equipment service, from passerelle overhauls to platform actuator upgrades.',
            'intro'    => 'We service and upgrade passerelles, accommodation stairs and bathing platforms. Typical work includes actuator overhaul, damper tuning for silent operation, structural weld inspection and control-system upgrades — on board or in our workshop.',
            'scope'    => [
                'Passerelle actuator overhaul',
                'Accommodation-stair mechanism service',
                'Bathing-platform cylinder & hinge repair',
                'Damper tuning for silent deployment',
                'Control-panel upgrade & integration',
            ],
            'specs' => [
                ['k' => 'Systems', 'v' => 'Passerelles, stairs, platforms'],
                ['k' => 'Actuation', 'v' => 'Hydraulic & electric, damped'],
                ['k' => 'OEM scope', 'v' => 'Opacmare, Besenzoni, Steelhead Marine'],
                ['k' => 'Standards', 'v' => 'LY3, MCA, ISO 17842'],
            ],
        ],
        [
            'slug'     => 'hull-doors',
            'title'    => 'Hull Doors',
            'eyebrow'  => 'HULL DOORS',
            'nav'      => 'Hull Doors',
            'icon'     => 'fa-door-open',
            'short'    => 'Shell doors, transom doors, inflatable and compression seals — hose-tested watertight.',
            'headline' => 'Hull-door service, from seal replacement to full structural overhaul.',
            'intro'    => 'We maintain and overhaul shell doors, transom doors and lazarette hatches. Scope ranges from inflatable-seal replacement and hose-test verification to full structural repair, hinge re-bushing and hydraulic-ram overhaul.',
            'scope'    => [
                'Inflatable & compression seal replacement',
                'Hose-test watertight verification',
                'Hinge & pin re-bushing',
                'Hydraulic ram overhaul',
                'Structural repair & re-certification',
            ],
            'specs' => [
                ['k' => 'Door types', 'v' => 'Shell doors, transom doors, hatches'],
                ['k' => 'Sealing', 'v' => 'Inflatable & compression seals'],
                ['k' => 'Testing', 'v' => 'Hose-test to class requirements'],
                ['k' => 'OEM scope', 'v' => 'Van Aalst, Navatech, TTS Marine'],
            ],
        ],
    ];
}

function oem_get_projects() {
    return [
        [
            'slug'      => 'project-1',
            'nav'       => 'Project 1',
            'tag'       => 'Refit',
            'title'     => 'Tender garage hull door replacement',
            'vessel'    => '85 m fleet support vessel, Northern Europe',
            'body'      => 'The original tender-garage hull door had reached end of life after 18 years of service. We engineered and installed a new hydraulically operated shell door, including frame modifications, new rams, a rebuilt HPU and a fully re-wired control panel. The yard integration window was nine weeks; we delivered on week eight.',
            'delivered' => [
                'New hydraulic shell door, frame & hinge assemblies',
                'Rebuilt HPU with proportional valve block',
                'PLC control panel with touch-screen HMI',
                'Class-witnessed load test & sea trial',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Refit'],
                ['k' => 'Vessel', 'v' => '85 m fleet support vessel'],
                ['k' => 'Location', 'v' => 'Northern Europe'],
                ['k' => 'Duration', 'v' => '9 weeks'],
                ['k' => 'Disciplines', 'v' => 'Hull doors, Hydraulics, Electrical'],
            ],
        ],
        [
            'slug'      => 'project-2',
            'nav'       => 'Project 2',
            'tag'       => 'Newbuild support',
            'title'     => 'Hydraulic passerelle installation',
            'vessel'    => '62 m motor yacht, Dutch yard',
            'body'      => 'During the outfitting phase of a 62 m new build we installed and commissioned the main passerelle, including hydraulic power unit, proportional valve block and PLC integration. The passerelle was tuned for silent deployment using velocity-profiled damping and delivered with a full set of class drawings.',
            'delivered' => [
                'Passerelle mechanical installation',
                'Dedicated HPU & proportional valve block',
                'PLC integration with bridge alarm system',
                'Silent-deployment velocity profiling',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Newbuild support'],
                ['k' => 'Vessel', 'v' => '62 m motor yacht'],
                ['k' => 'Location', 'v' => 'Dutch yard'],
                ['k' => 'Duration', 'v' => '5 weeks'],
                ['k' => 'Disciplines', 'v' => 'Boarding, Hydraulics, Electrical'],
            ],
        ],
        [
            'slug'      => 'project-3',
            'nav'       => 'Project 3',
            'tag'       => 'Service',
            'title'     => 'Crane control cabinet overhaul',
            'vessel'    => '74 m motor yacht, Barcelona',
            'body'      => 'The port tender crane exhibited intermittent faults traced to corroded relay bases and ageing contactors inside the original control cabinet. We stripped the cabinet, re-manufactured the wiring loom, replaced all switching components and uploaded updated PLC software. A class-witnessed load test confirmed the crane back to full SWL.',
            'delivered' => [
                'Full cabinet strip-down & re-wire',
                'New contactors, relay bases & terminal blocks',
                'PLC software update with enhanced diagnostics',
                'Class-witnessed SWL load test',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Service'],
                ['k' => 'Vessel', 'v' => '74 m motor yacht'],
                ['k' => 'Location', 'v' => 'Barcelona'],
                ['k' => 'Duration', 'v' => '3 weeks'],
                ['k' => 'Disciplines', 'v' => 'Cranes, Electrical'],
            ],
        ],
        [
            'slug'      => 'project-4',
            'nav'       => 'Project 4',
            'tag'       => 'Refit',
            'title'     => 'Bathing platform actuation upgrade',
            'vessel'    => '58 m motor yacht, Fort Lauderdale',
            'body'      => 'The bathing platform\'s original single-acting cylinders were replaced with double-acting units to improve deployment speed and enable controlled descent under load. We also replaced the hydraulic manifold, re-routed hoses and integrated a new PLC sequence with auto-level sensing.',
            'delivered' => [
                'Double-acting cylinder conversion',
                'New hydraulic manifold & hose routing',
                'PLC auto-level sequence integration',
                'On-water deployment trials',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Refit'],
                ['k' => 'Vessel', 'v' => '58 m motor yacht'],
                ['k' => 'Location', 'v' => 'Fort Lauderdale'],
                ['k' => 'Duration', 'v' => '4 weeks'],
                ['k' => 'Disciplines', 'v' => 'Boarding, Hydraulics, Electrical'],
            ],
        ],
        [
            'slug'      => 'project-5',
            'nav'       => 'Project 5',
            'tag'       => 'Refit',
            'title'     => 'Toy garage crane replacement',
            'vessel'    => '68 m motor yacht, Northern Europe',
            'body'      => 'A 15-year-old hydraulic toy-garage crane was beyond economic repair. We removed the old unit, engineered foundation modifications and installed a new crane with electric slew drive and hydraulic luffing. The project included a new control panel, load-moment limiter and class-witnessed testing.',
            'delivered' => [
                'Old crane removal & foundation modification',
                'New crane installation (electric slew, hydraulic luff)',
                'Control panel with load-moment limiter',
                'Class-witnessed SWL & dynamic load test',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Refit'],
                ['k' => 'Vessel', 'v' => '68 m motor yacht'],
                ['k' => 'Location', 'v' => 'Northern Europe'],
                ['k' => 'Duration', 'v' => '7 weeks'],
                ['k' => 'Disciplines', 'v' => 'Cranes, Mechanical, Electrical'],
            ],
        ],
        [
            'slug'      => 'project-6',
            'nav'       => 'Project 6',
            'tag'       => 'Service',
            'title'     => 'Shaft line alignment and vibration survey',
            'vessel'    => '92 m motor yacht, Mediterranean',
            'body'      => 'Following a grounding incident the owner requested a full shaft-line survey. We performed laser alignment of both shaft lines, a vibration survey to ISO 20283 and bearing-clearance measurements. The report identified a 0.12 mm offset on the starboard intermediate bearing, which we corrected on site.',
            'delivered' => [
                'Dual shaft-line laser alignment',
                'Vibration survey to ISO 20283 / ISO 10816',
                'Bearing clearance measurement report',
                'On-site offset correction (0.12 mm starboard)',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Service'],
                ['k' => 'Vessel', 'v' => '92 m motor yacht'],
                ['k' => 'Location', 'v' => 'Mediterranean'],
                ['k' => 'Duration', 'v' => '2 weeks'],
                ['k' => 'Disciplines', 'v' => 'Mechanical'],
            ],
        ],
        [
            'slug'      => 'project-7',
            'nav'       => 'Project 7',
            'tag'       => 'Refit',
            'title'     => 'Beach club transom door rebuild',
            'vessel'    => '55 m motor yacht, Dutch yard',
            'body'      => 'The beach-club transom door leaked past its inflatable seals and the hinge pins were heavily worn. We disassembled the door, re-bushed all hinges, replaced both inflatable seals and overhauled the hydraulic rams. A hose test to class requirements confirmed watertight integrity before the vessel left the yard.',
            'delivered' => [
                'Hinge disassembly & pin re-bushing',
                'Dual inflatable seal replacement',
                'Hydraulic ram overhaul',
                'Hose-test watertight verification',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Refit'],
                ['k' => 'Vessel', 'v' => '55 m motor yacht'],
                ['k' => 'Location', 'v' => 'Dutch yard'],
                ['k' => 'Duration', 'v' => '6 weeks'],
                ['k' => 'Disciplines', 'v' => 'Hull doors, Hydraulics'],
            ],
        ],
        [
            'slug'      => 'project-8',
            'nav'       => 'Project 8',
            'tag'       => 'Newbuild support',
            'title'     => 'Fleet-wide OEM Connect rollout',
            'vessel'    => 'Four hulls, 23 equipment items',
            'body'      => 'A fleet operator asked us to connect 23 pieces of deck equipment across four vessels to OEM Connect for remote diagnostics and predictive maintenance. We installed edge gateways on each vessel, mapped every sensor and actuator to the platform and delivered a single fleet dashboard to the shore-side operations team.',
            'delivered' => [
                'Edge gateway installation on four vessels',
                'Sensor & actuator mapping (23 equipment items)',
                'Fleet dashboard for shore-side ops team',
                'Remote-diagnostics training for crew',
            ],
            'facts' => [
                ['k' => 'Type', 'v' => 'Newbuild support'],
                ['k' => 'Fleet', 'v' => 'Four hulls'],
                ['k' => 'Equipment', 'v' => '23 connected items'],
                ['k' => 'Duration', 'v' => '5 months'],
                ['k' => 'Disciplines', 'v' => 'Electrical, OEM Connect'],
            ],
        ],
    ];
}

function oem_get_team() {
    return [
        'team' => [
            'eyebrow'  => 'ABOUT — TEAM',
            'headline' => 'The people who sign off the job.',
            'intro'    => 'Eight senior staff lead every project from first survey to final sea trial. Between them they hold class-accepted qualifications in electrical, hydraulic, mechanical and structural marine engineering.',
            'people'   => [
                ['name' => 'Head of Service',       'role' => 'Management',          'base' => 'Bolsward'],
                ['name' => 'Technical Director',     'role' => 'Management',          'base' => 'Bolsward'],
                ['name' => 'Lead Engineer',          'role' => 'Control Technology',  'base' => 'Bolsward'],
                ['name' => 'Lead Engineer',          'role' => 'Hydraulics',          'base' => 'Bolsward'],
                ['name' => 'Project Coordinator',    'role' => 'Aftersales',          'base' => 'Bolsward'],
                ['name' => 'Project Coordinator',    'role' => 'Refit',               'base' => 'Barcelona'],
                ['name' => 'Service Manager',        'role' => 'Americas',            'base' => 'Fort Lauderdale'],
                ['name' => 'Workshop Manager',       'role' => 'Production',          'base' => 'Bolsward'],
            ],
        ],
        'sub' => [
            'eyebrow'  => 'ABOUT — TEAM SUB',
            'headline' => 'The hands on the tools.',
            'intro'    => 'Our field engineers and workshop specialists deliver the physical work — from quayside troubleshooting to precision workshop assembly. Each holds relevant trade certifications and manufacturer training.',
            'people'   => [
                ['name' => 'Service Engineer',         'role' => 'Hydraulics',                    'base' => 'Field Europe'],
                ['name' => 'Service Engineer',         'role' => 'Electrical',                    'base' => 'Field Europe'],
                ['name' => 'Service Engineer',         'role' => 'Mechanic',                      'base' => 'Field Mediterranean'],
                ['name' => 'Service Engineer',         'role' => 'Electrical',                    'base' => 'Field Americas'],
                ['name' => 'Certified Welder',         'role' => 'Stainless / aluminium',         'base' => 'Bolsward'],
                ['name' => 'Class Surveyor liaison',   'role' => 'Certification',                 'base' => 'Bolsward'],
            ],
        ],
    ];
}

function oem_get_vacancies() {
    return [
        ['title' => 'Service Engineer — Hydraulics',     'location' => 'Bolsward / field',  'type' => 'Full-time'],
        ['title' => 'Service Engineer — Electrical',     'location' => 'Bolsward / field',  'type' => 'Full-time'],
        ['title' => 'Refit Coordinator',                 'location' => 'Barcelona',          'type' => 'Full-time'],
        ['title' => 'Mechanical Engineer',               'location' => 'Bolsward',           'type' => 'Full-time'],
        ['title' => 'Workshop Technician',               'location' => 'Bolsward',           'type' => 'Full-time'],
    ];
}

function oem_get_locations() {
    return [
        ['city' => 'Bolsward',          'role' => 'Headquarters',  'address' => 'Industriepark 10, 8701 PN Bolsward',       'tel' => '+31 (0) 6 1100 4005', 'tel_href' => 'tel:+31611004005'],
        ['city' => 'Barcelona',          'role' => 'Mediterranean', 'address' => 'Port Olímpic, 08005 Barcelona',             'tel' => '+31 (0) 6 1100 4005', 'tel_href' => 'tel:+31611004005'],
        ['city' => 'Fort Lauderdale',    'role' => 'Americas',      'address' => 'Marina Mile, Fort Lauderdale, FL 33316',    'tel' => '+31 (0) 6 1100 4005', 'tel_href' => 'tel:+31611004005'],
    ];
}
