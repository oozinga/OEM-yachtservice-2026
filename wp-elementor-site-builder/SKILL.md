---
name: wp-elementor-site-builder
description: >
  Build a complete WordPress website powered entirely by Elementor Pro — no custom PHP templates,
  no hardcoded HTML, everything editable in the Elementor visual editor. Use this skill whenever
  the user wants to create a WordPress website from a design (uploaded screenshot, PDF, Figma export),
  a design system (colors, fonts, spacing), or a page layout description. Also use it when the user
  wants to convert an existing custom-coded WordPress theme to Elementor Pro, or when they ask for
  an Elementor-based site they can easily import and edit. Triggers on: "WordPress site", "Elementor",
  "maak een website", "WordPress website bouwen", "design naar WordPress", "Elementor template",
  "importeerbare WordPress site", "website zonder custom code", "theme naar Elementor".
---

# WordPress Elementor Site Builder

Build production-ready WordPress sites that run 100% on Elementor Pro. The output is a child theme +
PHP setup script that creates all pages, templates, and settings as native Elementor data. Zero custom
templates, zero hardcoded HTML — everything the client can edit visually.

## When to use this skill

- User uploads a design (screenshot, PDF, mockup) and wants a WordPress site
- User provides a design system (brand colors, fonts, spacing) and page content
- User wants to migrate an existing custom WordPress theme to Elementor Pro
- User asks for a WordPress site they can "easily import" or "edit without code"

## Prerequisites

The target WordPress installation needs:
- WordPress 6.x+ (or 7.x)
- **Hello Elementor** theme (free parent theme)
- **Elementor Pro** plugin (required for Nav Menu, Posts widget, Form widget, Dynamic Tags, Theme Builder)
- WP-CLI access to run the setup script

## Workflow overview

```
Design input → Extract design tokens → Create child theme → Write setup script → Run → Export
```

1. **Extract design tokens** from the uploaded design or design system
2. **Create a Hello Elementor child theme** with minimal functions.php
3. **Write a PHP setup script** that creates everything in Elementor
4. **Run the script** via `wp eval-file setup.php --allow-root`
5. **Export** a WordPress XML + child theme ZIP for import on production

## Step 1: Extract design tokens

From the design input, identify and document:

| Token | Example | Where it's used |
|---|---|---|
| Primary color | `#E40034` | Buttons, links, accents |
| Dark / ink color | `#1B1B1F` | Headings, footer background |
| Body text color | `#4D4D54` | Paragraphs, descriptions |
| Light / paper color | `#F6F6F7` | Section backgrounds |
| Heading font | `acumin-pro` weight 700 | H1-H4, buttons |
| Body font | `acumin-pro` weight 400 | Paragraphs, lists |
| Container max-width | `1240px` | Content sections |
| Section padding | `80px` top/bottom | Vertical rhythm |
| Border radius | `12px` | Cards, buttons |
| Border color | `#ECECEE` | Cards, dividers |

Store these as CSS custom properties for the setup script to reference.

### Font loading

Determine the font source:
- **Google Fonts**: enqueue via `wp_enqueue_style` with fonts.googleapis.com URL
- **Adobe Fonts (Typekit)**: enqueue via `wp_enqueue_style` with `use.typekit.net/<kit-id>.css`
- **System fonts**: no enqueue needed

Important: Adobe Fonts uses lowercase hyphenated names in CSS (e.g., `acumin-pro`, not `Acumin Pro`).
Fetch the Typekit CSS to verify the exact `font-family` name before using it in Elementor widget settings.

## Step 2: Create the child theme

The child theme has three files maximum:

### style.css (required)

```css
/*
Theme Name: <Site Name>
Template: hello-elementor
Version: 1.0.0
Text Domain: <text-domain>
*/
```

Only the WordPress theme header — no layout CSS. All styling lives in Elementor widget settings.

### functions.php

Keep this as small as possible. Only include what Elementor Pro cannot do natively:

```php
<?php
// Theme setup (required)
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    register_nav_menus(['primary' => 'Primary Navigation']);
});

// SVG upload support (needed for SVG logos)
add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

// Font loading
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('custom-fonts', '<FONT_URL>', [], null);
});
```

Add only if the site needs them:
- **Custom Post Type registration** — when the site has structured content (projects, team, portfolio)
- **Meta box** — when the CPT has custom fields that aren't ACF
- **Shortcodes** — only for rendering PHP array data that Elementor Dynamic Tags can't handle (e.g., repeater fields stored as serialized arrays in post_meta)

Never add to functions.php:
- Nav walkers (use Elementor Nav Menu widget)
- Template redirects (Elementor handles templates)
- Content rendering functions (use Elementor widgets)
- Custom CSS enqueues (use Elementor Global Kit)
- Data-fetching functions for footer/header (use Elementor Theme Builder)

### assets/ directory

Only for files that need to ship with the theme:
- Logo files (PNG/SVG)
- Background videos (.mov/.mp4)

Media that can be uploaded via WordPress Media Library should not be in the theme.

## Step 3: Write the setup script

This is the core of the skill. The setup script is a PHP file (`setup-elementor.php`) that runs
once via WP-CLI and creates all Elementor content in the database.

Read `references/elementor-data-format.md` for the complete `_elementor_data` JSON structure.

### Script structure

```php
<?php
// 1. Configure Elementor Global Kit (colors, fonts, container width)
// 2. Create Theme Builder templates (header, footer, single, archive)
// 3. Create/update pages with Elementor widget data
// 4. Set up WordPress settings (front page, menus, permalinks)
// 5. Report results
```

### 3a. Configure the Global Kit

The Global Kit is stored as `_elementor_page_settings` on the Elementor Kit post
(find it via `get_option('elementor_active_kit')`).

```php
$kit_id = get_option('elementor_active_kit');
$settings = get_post_meta($kit_id, '_elementor_page_settings', true);
if (!is_array($settings)) $settings = [];

// System colors
$settings['system_colors'] = [
    ['_id' => 'primary',   'title' => 'Primary',   'color' => '#E40034'],
    ['_id' => 'secondary', 'title' => 'Dark',      'color' => '#1B1B1F'],
    ['_id' => 'text',      'title' => 'Body Text',  'color' => '#4D4D54'],
    ['_id' => 'accent',    'title' => 'Light BG',   'color' => '#F6F6F7'],
];

// System typography
$settings['system_typography'] = [
    ['_id' => 'primary', 'title' => 'Heading',
     'typography_typography' => 'custom',
     'typography_font_family' => 'acumin-pro',
     'typography_font_weight' => '700'],
    ['_id' => 'secondary', 'title' => 'Subheading',
     'typography_typography' => 'custom',
     'typography_font_family' => 'acumin-pro',
     'typography_font_weight' => '400'],
    ['_id' => 'text', 'title' => 'Body',
     'typography_typography' => 'custom',
     'typography_font_family' => 'acumin-pro',
     'typography_font_weight' => '400',
     'typography_font_size' => ['unit' => 'px', 'size' => 17],
     'typography_line_height' => ['unit' => 'em', 'size' => 1.65]],
    ['_id' => 'accent', 'title' => 'Small/Caption',
     'typography_typography' => 'custom',
     'typography_font_family' => 'acumin-pro',
     'typography_font_weight' => '400'],
];

// Container width
$settings['container_width'] = ['unit' => 'px', 'size' => 1240];

update_post_meta($kit_id, '_elementor_page_settings', $settings);
```

### 3b. Create Theme Builder templates

Theme Builder templates are `elementor_library` posts with special meta. Create four:

1. **Header** — `_elementor_template_type` = `header`, condition: `include/general`
2. **Footer** — `_elementor_template_type` = `footer`, condition: `include/general`
3. **Single CPT** — `_elementor_template_type` = `single`, condition: `include/singular/<post_type>`
4. **Archive CPT** — `_elementor_template_type` = `archive`, condition: `include/archive/<post_type>`

Only create Single/Archive templates when the site has a CPT.

```php
function create_template($title, $type, $conditions, $elementor_data) {
    $post_id = wp_insert_post([
        'post_title'  => $title,
        'post_status' => 'publish',
        'post_type'   => 'elementor_library',
    ]);
    update_post_meta($post_id, '_elementor_template_type', $type);
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_data', wp_json_encode($elementor_data));

    // Set display conditions
    $all_conditions = get_option('elementor_pro_theme_builder_conditions', []);
    $all_conditions[$type][$post_id] = $conditions;
    update_option('elementor_pro_theme_builder_conditions', $all_conditions);

    return $post_id;
}
```

### 3c. Build pages with Elementor data

Each page gets `_elementor_data` — a JSON array of sections, columns, and widgets.
See `references/elementor-data-format.md` for the complete widget reference.

```php
function set_page_elementor_data($post_id, $elementor_data) {
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_template_type', 'wp-page');
    update_post_meta($post_id, '_elementor_page_settings', [
        'hide_title' => 'yes',
    ]);
    update_post_meta($post_id, '_elementor_data', wp_json_encode($elementor_data));
    // Clear rendered CSS cache
    delete_post_meta($post_id, '_elementor_css');
}
```

### 3d. WordPress settings

```php
// Set front page
$home = get_page_by_title('Home');
if ($home) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home->ID);
}

// Flush rewrite rules (for CPT archives)
flush_rewrite_rules();
```

### Important: Script is NOT idempotent

The setup script creates new posts each time it runs. Add a guard at the top:

```php
$marker = get_option('site_elementor_setup_done');
if ($marker) {
    echo "Setup already completed. Delete option 'site_elementor_setup_done' to re-run.\n";
    return;
}
// ... script body ...
update_option('site_elementor_setup_done', true);
```

## Step 4: Run the setup

```bash
cd /path/to/wordpress
php -r "require_once 'wp-load.php'; ob_start(); include 'wp-content/themes/<child-theme>/setup-elementor.php'; echo ob_get_clean();"
```

Or via WP-CLI if available:
```bash
wp eval-file wp-content/themes/<child-theme>/setup-elementor.php --allow-root
```

After running, clear the Elementor CSS cache:
```php
delete_post_meta_by_key('_elementor_css');
```

## Step 5: Export for import

Generate two deliverables:

1. **WordPress XML export** — all content, Elementor data, menus
```php
require_once ABSPATH . 'wp-admin/includes/export.php';
ob_start();
export_wp(['content' => 'all']);
$xml = ob_get_clean();
file_put_contents('site-export.xml', $xml);
```

2. **Child theme ZIP** — the theme directory
```bash
cd wp-content/themes && zip -r /output/child-theme.zip <child-theme>/
```

### Import instructions for the user

1. Install WordPress + Hello Elementor theme + Elementor Pro on the target host
2. Upload the child theme ZIP via Appearance → Themes → Add New → Upload, activate it
3. Go to Tools → Import → WordPress Importer
4. Upload the XML export file
5. After import: Settings → Reading → set the correct front page
6. Elementor → Tools → Regenerate CSS

## Design-to-widget mapping

When translating a design to Elementor widgets, use this mapping:

| Design element | Elementor widget | Notes |
|---|---|---|
| Navigation bar | Section + Logo + Nav Menu (Pro) + Button | Use Theme Builder header template |
| Hero with background | Section with background image/video | `background_background: 'video'` or `'classic'` |
| Heading text | Heading widget | Set `header_size` (h1-h6), typography per design |
| Body text | Text Editor widget | Supports HTML formatting |
| CTA button | Button widget | `button_type`, `background_color`, `border_radius` |
| Image | Image widget | Set `image.url` to theme asset or media library URL |
| Icon + text card | Icon Box widget | Icon, title, description, link in one widget |
| Feature grid | Section with 3+ columns, each with Icon Box | Use `flex-wrap` container |
| Contact form | Form widget (Pro) | Replaces Contact Form 7 entirely |
| Project/post grid | Posts widget (Pro) | `query_post_type`, columns, image ratio |
| Footer | Section with columns of Text Editor widgets | Use Theme Builder footer template |
| Stats/counters | Counter widget | `starting_number`, `ending_number`, `suffix` |
| Accordion/FAQ | Accordion widget | `tabs` array with title + content |
| Tabs | Tabs widget | For switching between content panels |
| Testimonials | Testimonial widget | Or Testimonial Carousel for multiple |

## Elementor Pro features to prefer over custom code

| Custom code approach | Elementor Pro alternative |
|---|---|
| PHP nav walker class | Nav Menu widget (Pro) |
| PHP header.php template | Theme Builder → Header template |
| PHP footer.php template | Theme Builder → Footer template |
| PHP single-{cpt}.php | Theme Builder → Single template + Dynamic Tags |
| PHP archive-{cpt}.php | Theme Builder → Archive template + Posts widget |
| Contact Form 7 plugin | Form widget (Pro) |
| PHP data functions for lists | Posts widget (Pro) with custom query |
| Custom CSS file | Elementor Global Kit + per-widget styling |
| Google Fonts enqueue | Elementor Typography settings (built-in) |
| PHP shortcodes for simple content | Text Editor or HTML widget |

## When custom PHP IS still needed

Some things genuinely cannot be done in Elementor alone:

- **Custom Post Type registration** — `register_post_type()` must be in PHP
- **Meta boxes** for CPT custom fields — `add_meta_box()` in PHP
- **Shortcodes for array-type post_meta** — Elementor Dynamic Tags work with scalar values, but serialized arrays (e.g., a list of deliverables, a key-value fact sheet) need a shortcode that renders the HTML
- **Font loading from Adobe Fonts** — `wp_enqueue_style()` for the Typekit CSS
- **SVG upload support** — `upload_mimes` filter

Put these in functions.php and nothing else. If possible, move CPT registration to a small custom plugin so it's theme-independent.
