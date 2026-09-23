# Elementor _elementor_data JSON Format

This reference documents the JSON structure stored in `_elementor_data` post meta.
It is the core data format that defines every Elementor page's layout and content.

## Structure hierarchy

```
Page → Section[] → Column[] → Widget[]
```

Every element has:
- `id` — unique 7-character hex string (e.g., `"74beedc"`)
- `elType` — `"section"`, `"column"`, or `"widget"`
- `settings` — object with all configuration
- `elements` — nested child elements (sections contain columns, columns contain widgets)

## Section (row)

```json
{
  "id": "abc1234",
  "elType": "section",
  "settings": {
    "layout": "full_width",
    "content_width": {"unit": "px", "size": 1240},
    "gap": "no",
    "padding": {"unit": "px", "top": "80", "right": "24", "bottom": "80", "left": "24"},
    "background_background": "classic",
    "background_color": "#F6F6F7",
    "structure": "30"
  },
  "elements": [/* columns */]
}
```

### Section structure codes

The `structure` setting defines column layout:
- `"10"` — 1 column (100%)
- `"20"` — 2 equal columns (50/50)
- `"30"` — 3 equal columns (33/33/33)
- `"21"` — 2 columns (66/33)
- `"22"` — 2 columns (33/66)
- `"40"` — 4 equal columns (25/25/25/25)

### Background types

```json
// Solid color
"background_background": "classic",
"background_color": "#1B1B1F"

// Image
"background_background": "classic",
"background_image": {"url": "https://...", "id": ""},

// Video
"background_background": "video",
"background_video_link": "https://.../hero-bg.mov",

// Overlay on video/image
"background_overlay_background": "classic",
"background_overlay_color": "rgba(27,27,31,0.65)"
```

## Column

```json
{
  "id": "def5678",
  "elType": "column",
  "settings": {
    "_column_size": 33,
    "padding": {"unit": "px", "top": "0", "right": "0", "bottom": "0", "left": "0"}
  },
  "elements": [/* widgets */]
}
```

`_column_size` is the percentage width (33, 50, 66, 100, etc.).

## Widgets

All widgets follow this pattern:

```json
{
  "id": "ghi9012",
  "elType": "widget",
  "widgetType": "<widget-name>",
  "settings": {/* widget-specific */},
  "elements": []
}
```

### Heading widget

```json
{
  "widgetType": "heading",
  "settings": {
    "title": "Your heading text here",
    "header_size": "h2",
    "align": "left",
    "title_color": "#1B1B1F",
    "typography_typography": "custom",
    "typography_font_family": "acumin-pro",
    "typography_font_weight": "700",
    "typography_font_size": {"unit": "px", "size": 42},
    "typography_line_height": {"unit": "em", "size": 1.15}
  }
}
```

### Text Editor widget

```json
{
  "widgetType": "text-editor",
  "settings": {
    "editor": "<p>Your HTML content here. Supports <strong>bold</strong>, <a href=\"#\">links</a>, and other HTML.</p>",
    "text_color": "#4D4D54",
    "typography_typography": "custom",
    "typography_font_family": "acumin-pro",
    "typography_font_weight": "400",
    "typography_font_size": {"unit": "px", "size": 17},
    "typography_line_height": {"unit": "em", "size": 1.65}
  }
}
```

### Button widget

```json
{
  "widgetType": "button",
  "settings": {
    "text": "Contact us",
    "link": {"url": "/contact/", "is_external": false, "nofollow": false},
    "button_type": "default",
    "background_color": "#E40034",
    "button_text_color": "#FFFFFF",
    "border_radius": {"unit": "px", "top": "6", "right": "6", "bottom": "6", "left": "6"},
    "typography_typography": "custom",
    "typography_font_family": "acumin-pro",
    "typography_font_weight": "700",
    "typography_font_size": {"unit": "px", "size": 15},
    "text_padding": {"unit": "px", "top": "14", "right": "32", "bottom": "14", "left": "32"}
  }
}
```

### Ghost/outline button variant

```json
{
  "widgetType": "button",
  "settings": {
    "text": "Read more",
    "button_type": "default",
    "background_color": "transparent",
    "button_text_color": "#FFFFFF",
    "button_border_color": "#FFFFFF",
    "border_border": "solid",
    "border_width": {"unit": "px", "top": "2", "right": "2", "bottom": "2", "left": "2"}
  }
}
```

### Image widget

```json
{
  "widgetType": "image",
  "settings": {
    "image": {
      "url": "https://example.com/wp-content/themes/child/assets/logo.png",
      "id": ""
    },
    "image_size": "full",
    "width": {"unit": "px", "size": 180},
    "align": "left"
  }
}
```

### Icon Box widget

Combines an icon, title, description, and optional link — ideal for service cards and feature grids.

```json
{
  "widgetType": "icon-box",
  "settings": {
    "selected_icon": {"value": "fas fa-bolt", "library": "fa-solid"},
    "title_text": "Electrical systems",
    "description_text": "PLC & ECC control cabinets, from single-panel upgrades to full vessel automation.",
    "link": {"url": "/services/electrical/"},
    "position": "top",
    "icon_space": {"unit": "px", "size": 20},
    "primary_color": "#E40034",
    "title_typography_typography": "custom",
    "title_typography_font_family": "acumin-pro",
    "title_typography_font_weight": "700",
    "title_typography_font_size": {"unit": "px", "size": 20},
    "description_typography_typography": "custom",
    "description_typography_font_family": "acumin-pro",
    "description_typography_font_weight": "400",
    "description_typography_font_size": {"unit": "px", "size": 16}
  }
}
```

### Icon List widget

A bulleted list with custom icons per item.

```json
{
  "widgetType": "icon-list",
  "settings": {
    "icon_list": [
      {
        "text": "First item",
        "selected_icon": {"value": "fas fa-check", "library": "fa-solid"},
        "link": {"url": ""}
      },
      {
        "text": "Second item",
        "selected_icon": {"value": "fas fa-check", "library": "fa-solid"},
        "link": {"url": ""}
      }
    ],
    "icon_color": "#E40034",
    "text_color": "#4D4D54",
    "text_indent": {"unit": "px", "size": 8}
  }
}
```

### HTML widget (for shortcodes or custom markup)

```json
{
  "widgetType": "html",
  "settings": {
    "html": "[oem_delivered]"
  }
}
```

Use this for shortcodes that render dynamic PHP content.

### Counter widget

```json
{
  "widgetType": "counter",
  "settings": {
    "starting_number": 0,
    "ending_number": 250,
    "suffix": "+",
    "title": "Projects delivered",
    "number_color": "#E40034",
    "title_color": "#4D4D54"
  }
}
```

### Form widget (Pro)

Replaces Contact Form 7.

```json
{
  "widgetType": "form",
  "settings": {
    "form_name": "Contact Form",
    "form_fields": [
      {
        "custom_id": "name",
        "field_type": "text",
        "field_label": "Name",
        "placeholder": "Your name",
        "required": "true",
        "width": "50"
      },
      {
        "custom_id": "email",
        "field_type": "email",
        "field_label": "Email",
        "placeholder": "your@email.com",
        "required": "true",
        "width": "50"
      },
      {
        "custom_id": "message",
        "field_type": "textarea",
        "field_label": "Message",
        "placeholder": "How can we help?",
        "required": "",
        "rows": 5,
        "width": "100"
      }
    ],
    "button_text": "Send message",
    "button_background_color": "#E40034",
    "button_color": "#FFFFFF",
    "email_to": "info@example.com",
    "email_subject": "New contact form submission"
  }
}
```

### Nav Menu widget (Pro)

For header navigation. Only available with Elementor Pro.

```json
{
  "widgetType": "nav-menu",
  "settings": {
    "menu": "",
    "layout": "horizontal",
    "align_items": "flex-end",
    "pointer": "none",
    "text_color": "#FFFFFF",
    "typography_typography": "custom",
    "typography_font_family": "acumin-pro",
    "typography_font_weight": "700",
    "typography_font_size": {"unit": "px", "size": 14},
    "typography_letter_spacing": {"unit": "px", "size": 1.5}
  }
}
```

Note: `"menu": ""` uses the primary registered menu location. To use a specific menu,
set `"menu"` to the menu's term_id.

### Posts widget (Pro)

Displays a grid of posts from any post type.

```json
{
  "widgetType": "posts",
  "settings": {
    "posts_post_type": "oem_project",
    "posts_per_page": 4,
    "columns": "4",
    "thumbnail": "yes",
    "thumbnail_size_size": "medium_large",
    "title": "yes",
    "excerpt": "yes",
    "meta_data": ["none"],
    "read_more": "yes",
    "read_more_text": "Read more",
    "pagination_type": "none"
  }
}
```

## Theme Builder template conditions

Conditions are stored in the `elementor_pro_theme_builder_conditions` option:

```php
[
    'header' => [
        70 => ['include/general']  // show on all pages
    ],
    'footer' => [
        71 => ['include/general']
    ],
    'single' => [
        72 => ['include/singular/oem_project']  // only on single project pages
    ],
    'archive' => [
        73 => ['include/archive/oem_project']  // only on project archive
    ]
]
```

Common conditions:
- `include/general` — all pages
- `include/singular` — all single posts/pages
- `include/singular/<post_type>` — specific post type single pages
- `include/archive/<post_type>` — specific post type archive
- `include/front_page` — only the front page

## Dynamic Tags (Pro) for Single templates

In Single post templates, use Dynamic Tags to pull in post data:

```json
{
  "widgetType": "heading",
  "settings": {
    "title": "",
    "__dynamic__": {
      "title": "[elementor-tag id=\"\" name=\"post-title\"]"
    }
  }
}
```

Common dynamic tag names:
- `post-title` — the post title
- `post-content` — the post content (rendered)
- `post-featured-image` — the featured image
- `post-custom-field` — a specific meta field (pass `key` parameter)

## Helper function for generating unique IDs

```php
function eid() {
    return substr(md5(uniqid(mt_rand(), true)), 0, 7);
}
```

Use this for every `id` field in the Elementor data structure.
