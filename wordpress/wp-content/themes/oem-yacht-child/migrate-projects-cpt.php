<?php
/**
 * Migration: Convert project Pages to oem_project CPT posts.
 * Run once via: wp eval-file wp-content/themes/oem-yacht-child/migrate-projects-cpt.php --allow-root
 */

// 1. Get project data from the existing helper
$projects = oem_get_projects();

// 2. Delete existing project Pages (children of /projects/ page)
$projects_page = get_page_by_path( 'projects' );
if ( $projects_page ) {
    $children = get_children([
        'post_parent' => $projects_page->ID,
        'post_type'   => 'page',
        'numberposts' => -1,
    ]);
    foreach ( $children as $child ) {
        wp_delete_post( $child->ID, true );
        echo "Deleted page: {$child->post_title} (ID {$child->ID})\n";
    }
    // Delete the projects index page itself (archive-oem_project.php takes over)
    wp_delete_post( $projects_page->ID, true );
    echo "Deleted projects index page (ID {$projects_page->ID})\n";
}

// 3. Create CPT posts
$order = 1;
$cpt_ids = [];
foreach ( $projects as $p ) {
    $post_id = wp_insert_post([
        'post_type'    => 'oem_project',
        'post_title'   => $p['title'],
        'post_name'    => $p['slug'],
        'post_content' => $p['body'],
        'post_status'  => 'publish',
        'menu_order'   => $order++,
    ]);
    if ( is_wp_error( $post_id ) ) {
        echo "ERROR creating {$p['slug']}: {$post_id->get_error_message()}\n";
        continue;
    }
    update_post_meta( $post_id, '_oem_tag', $p['tag'] );
    update_post_meta( $post_id, '_oem_vessel', $p['vessel'] );
    update_post_meta( $post_id, '_oem_delivered', $p['delivered'] );
    update_post_meta( $post_id, '_oem_facts', $p['facts'] );

    $cpt_ids[ $p['slug'] ] = $post_id;
    echo "Created CPT: {$p['title']} (ID {$post_id}, slug {$p['slug']})\n";
}

// 4. Update navigation menu — replace project page items with CPT items
$menu_name = 'Primary Navigation';
$menu = wp_get_nav_menu_object( $menu_name );
if ( $menu ) {
    $items = wp_get_nav_menu_items( $menu->term_id );

    // Find the Projects parent menu item and its children
    $projects_parent_id = 0;
    $items_to_remove = [];
    foreach ( $items as $item ) {
        // The parent "Projects" item — keep it but update to point to CPT archive
        if ( in_array( 'menu-item-projects', (array) $item->classes ) && $item->menu_item_parent == 0 ) {
            $projects_parent_id = $item->ID;
            // Update to custom link pointing to CPT archive
            wp_update_nav_menu_item( $menu->term_id, $item->ID, [
                'menu-item-title'    => 'Projects',
                'menu-item-url'      => home_url( '/projects/' ),
                'menu-item-status'   => 'publish',
                'menu-item-type'     => 'custom',
                'menu-item-classes'  => 'menu-item-projects',
                'menu-item-parent-id' => 0,
            ]);
            echo "Updated Projects parent menu item to CPT archive URL\n";
        }
        // Children of Projects — mark for removal
        if ( $item->menu_item_parent == $projects_parent_id && $projects_parent_id > 0 ) {
            $items_to_remove[] = $item->ID;
        }
    }

    // Remove old project sub-items
    foreach ( $items_to_remove as $item_id ) {
        wp_delete_post( $item_id, true );
    }
    echo "Removed " . count( $items_to_remove ) . " old project sub-menu items\n";

    // Add new CPT posts as sub-items
    $position = 1;
    foreach ( $projects as $p ) {
        if ( ! isset( $cpt_ids[ $p['slug'] ] ) ) continue;
        wp_update_nav_menu_item( $menu->term_id, 0, [
            'menu-item-title'     => $p['nav'],
            'menu-item-object'    => 'oem_project',
            'menu-item-object-id' => $cpt_ids[ $p['slug'] ],
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $projects_parent_id,
            'menu-item-position'  => $position++,
        ]);
    }
    echo "Added " . count( $cpt_ids ) . " CPT project sub-menu items\n";
}

// 5. Update homepage — replace hardcoded project cards with shortcode
$home_page = get_option( 'page_on_front' );
if ( $home_page ) {
    $content = get_post_field( 'post_content', $home_page );
    // Find the projects section and replace the hardcoded cards with shortcode
    // The cards are inside <div class="oem-card-grid oem-card-grid--4">...</div>
    $pattern = '/<div class="oem-card-grid oem-card-grid--4">\s*(<a href="\/projects\/.*?<\/a>\s*)+<\/div>/s';
    $replacement = '[oem_projects_grid count="4" columns="4"]';
    $new_content = preg_replace( $pattern, $replacement, $content );
    if ( $new_content && $new_content !== $content ) {
        wp_update_post([
            'ID'           => $home_page,
            'post_content' => $new_content,
        ]);
        echo "Updated homepage: replaced hardcoded project cards with shortcode\n";
    } else {
        echo "WARNING: Could not find hardcoded project cards on homepage to replace\n";
    }
}

// 6. Flush rewrite rules
flush_rewrite_rules();
echo "\nDone! Rewrite rules flushed. Projects are now a Custom Post Type.\n";
