# Integrity Wines - Technical Reference

**IMPORTANT: Use this document to level-set at the start of any new chat.**
**Do NOT guess CPT names, taxonomy names, or meta keys - check here first!**

---

## Plugin Architecture

```
dandysite-jane (base theme)
    ↓
ds-theme-customizations (Integrity Wines branding & styling)
    ↓
ds-wineguy (wine/producer functionality)
ds-age-verification (age gate + cookie popup)
```

---

## Custom Post Types

### Producers
| Property | Value |
|----------|-------|
| **Registered name** | `dswg_producer` |
| **URL slug** | `/producers/` |
| **Admin menu** | "Wine Producers" |
| **Supports** | title, editor, thumbnail, excerpt, revisions |
| **Taxonomies** | `dswg_country`, `dswg_region` |
| **Template file** | `single-dswg_producer.php` (in ds-theme-customizations/templates/) |
| **Archive template** | `archive-dswg_producer.php` |
| **show_in_rest** | true (block editor active) |

### Wines
| Property | Value |
|----------|-------|
| **Registered name** | `dswg_wine` |
| **URL slug** | `/wines/` |
| **Admin menu** | Under "Wine Producers" |
| **Supports** | title, editor, thumbnail, excerpt, revisions |
| **Taxonomies** | `dswg_wine_type` |
| **Template file** | `single-dswg_wine.php` |
| **Archive template** | `archive-dswg_wine.php` |
| **show_in_rest** | true (block editor active) |

---

## Taxonomies

### Country (for Producers)
| Property | Value |
|----------|-------|
| **Registered name** | `dswg_country` |
| **URL slug** | `/country/` |
| **Type** | Hierarchical (like categories) |
| **Applies to** | `dswg_producer` |
| **Template file** | `taxonomy-dswg_country.php` |
| **Default terms** | France, Italy, Spain, Austria, Slovenia |

### Region (for Producers)
| Property | Value |
|----------|-------|
| **Registered name** | `dswg_region` |
| **URL slug** | `/region/` |
| **Type** | Non-hierarchical (like tags) |
| **Applies to** | `dswg_producer` |

### Wine Type (for Wines)
| Property | Value |
|----------|-------|
| **Registered name** | `dswg_wine_type` |
| **URL slug** | `/wine-type/` |
| **Type** | Hierarchical |
| **Applies to** | `dswg_wine` |
| **Default terms** | Red Wine, White Wine, Rosé, Sparkling Wine, Champagne, Dessert Wine, Fortified Wine |

---

## Meta Fields (Post Meta Keys)

### Producer Meta Fields
| Field | Meta Key | Type | Notes |
|-------|----------|------|-------|
| Display Location | `dswg_location` | text | Full display string e.g. "Ribera del Duero, Spain" |
| Short Description | `dswg_short_desc` | textarea | 2-3 sentence intro shown at top of producer page |
| Key Highlights | `dswg_highlights` | textarea | One highlight per line, rendered as bullet list |
| Region/Appellation | `dswg_region` | text | For taxonomy/filtering use |
| Website URL | `dswg_website` | URL | |
| Contact Email | `dswg_contact_email` | email | |
| Contact Phone | `dswg_contact_phone` | tel | |
| Instagram URL | `dswg_instagram` | URL | |
| Facebook URL | `dswg_facebook` | URL | |
| Twitter/X URL | `dswg_twitter` | URL | |
| Full Address | `dswg_address` | textarea | For geocoding |
| Latitude | `dswg_latitude` | text | GPS coordinate |
| Longitude | `dswg_longitude` | text | GPS coordinate |
| Gallery Images | `dswg_gallery_ids` | string | Comma-separated attachment IDs |
| Producer Logo | `dswg_producer_logo` | text | Attachment ID — **must be PNG or SVG with transparent bg** |
| Producer Files | `dswg_producer_files` | string | Comma-separated attachment IDs (PDFs etc) |

**Also available on Producers:**
- Featured Image = main producer photo (standard WP `_thumbnail_id`)

### Wine Meta Fields
| Field | Meta Key | Type | Notes |
|-------|----------|------|-------|
| Producer (relationship) | `dswg_producer_id` | int | Post ID of parent `dswg_producer` |
| Vintage | `dswg_vintage` | text | Year or "NV" |
| Varietal/Blend | `dswg_varietal` | text | |
| Alcohol % | `dswg_alcohol` | text | |
| Wine Logo | `dswg_wine_logo` | int | Single attachment ID |
| Wine Files | `dswg_wine_files` | string | Comma-separated attachment IDs (PDFs etc) |

**Also available on Wines:**
- Featured Image = bottle photo (standard WP `_thumbnail_id`)

### Theme Meta Fields (dandysite-jane)
| Field | Meta Key | Type | Notes |
|-------|----------|------|-------|
| Header Style | `_dsp_header_style` | text | '', 'overlay', or 'solid' — per-page override |

---

## Meta Box Registration IDs

⚠️ **CRITICAL CONVENTION:** Meta box registration IDs must NOT match any input field
`id` attributes inside them. WordPress wraps each meta box in `<div id="{registration_id}">`.
If an input inside has the same `id`, jQuery finds the div instead of the input — `.val()`
appears to work in JS but the div is never serialized on form submit.

**Convention: prefix all meta box IDs with `dswg_mb_`**

| Meta Box ID | Title |
|-------------|-------|
| `dswg_producer_details` | Producer Details |
| `dswg_producer_contact` | Contact & Social Media |
| `dswg_producer_location` | Location & Coordinates |
| `dswg_mb_producer_logo` | Producer Logo ← note `dswg_mb_` prefix |
| `dswg_mb_producer_gallery` | Image Gallery ← note `dswg_mb_` prefix |
| `dswg_mb_producer_files` | Documents & Files ← note `dswg_mb_` prefix |
| `dswg_wine_details` | Wine Details |
| `dswg_mb_wine_images` | Wine Images ← note `dswg_mb_` prefix |
| `dswg_mb_wine_files` | Tech Sheets & Marketing Materials ← note `dswg_mb_` prefix |

---

## Custom Image Sizes

| Size Name | Dimensions | Crop | Use |
|-----------|-----------|------|-----|
| `dswg-producer-thumb` | 400×400 | Yes | Producer listing |
| `dswg-producer-large` | 800×800 | No | Producer single |
| `dswg-bottle-thumb` | 300×450 | Yes | Wine listing |
| `dswg-bottle-large` | 600×900 | No | Wine single |
| `dswg-logo-thumb` | 200×200 | Yes | Producer/wine logos |

---

## Template Functions (from ds-wineguy)

```php
// Get all wines for a producer
dswg_get_producer_wines($producer_id)

// Display producer contact info
dswg_display_producer_contact($post_id)

// Display producer social links
dswg_display_producer_social($post_id)

// Display producer gallery
dswg_display_producer_gallery($post_id)

// Get the producer for a wine
dswg_get_wine_producer($wine_id)

// Display wine details
dswg_display_wine_details($post_id)

// Display wine downloadable files
dswg_display_wine_files($post_id)
```

---

## CSS Class Naming Conventions

Plugin prefix: `dswg-` (for admin/functional CSS)
Theme prefix: none - BEM-style component names

### Key Component Classes (from ds-theme-customizations)

**Cards:**
- `.producer-card` / `.producer-card__image` / `.producer-card__content` / `.producer-card__title` / `.producer-card__country` / `.producer-card__excerpt` / `.producer-card__meta`
- `.wine-card` / `.wine-card__image` / `.wine-card__content` / `.wine-card__producer` / `.wine-card__title` / `.wine-card__vintage` / `.wine-card__type`
- `.country-card` / `.country-card__map` / `.country-card__content` / `.country-card__title`

**Wine type badge modifiers:**
- `.wine-card__type--red`
- `.wine-card__type--white`
- `.wine-card__type--rose`
- `.wine-card__type--sparkling`

**Grids:**
- `.producer-grid`
- `.wine-grid`

**Filters:**
- `.filter-sidebar`
- `.filter-group`
- `.filter-group__title`
- `.filter-option`

**Downloads:**
- `.download-list`
- `.download-item`
- `.download-link`

**Gallery:**
- `.producer-gallery`
- `.producer-gallery__item`

**Buttons:**
- `.button` (primary - wine red)
- `.button--secondary` (outlined)
- `.button--accent` (gold)
- `.button--small` / `.button--large`

---

## CSS Variables (Key Ones)

```css
/* Colors */
--color-background: #F3E9D7;     /* Warm parchment - alt sections */
--color-background-page: #FFF7E9; /* Main page background */
--color-wine-red: #7A1F2B;       /* Primary brand */
--color-wine-highlight: #9B2C3A; /* Hover state */
--color-green-primary: #3F6B3A;  /* Secondary brand */
--color-green-dark: #2E4F2A;     /* Dark accent */
--color-gold-primary: #C6A64B;   /* Accent gold */
--color-gold-dark: #9E7C2F;      /* Dark gold */
--color-text: #2A2420;           /* Warm black */
--color-text-light: #5A524D;     /* Lighter text */
--color-text-muted: #7A706A;     /* Muted text */
--color-surface: #FDFBF7;        /* Card backgrounds */
--color-border: #D4C8B5;         /* Borders */

/* Spacing */
--spacing-xs: 0.25rem;
--spacing-sm: 0.5rem;
--spacing-md: 1rem;
--spacing-lg: 2rem;
--spacing-xl: 3rem;
--spacing-2xl: 4rem;

/* Layout */
--container-max-width: 1400px;
--content-max-width: 900px;
--sidebar-width: 300px;

/* UI */
--border-radius: 4px;
--border-radius-lg: 8px;
--box-shadow: 0 2px 8px rgba(42, 36, 32, 0.08);
--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
```

---

## Admin JS Patterns (admin.js)

### Media Uploader — Single Image (logo)
```javascript
var frame;
$('.dswg-upload-producer-logo').on('click', function(e) {
    e.preventDefault();
    if (frame) { frame.open(); return; }
    frame = wp.media({ title: 'Select Logo', button: { text: 'Use as Logo' }, multiple: false });
    frame.on('select', function() {
        var attachment = frame.state().get('selection').first().toJSON();
        $('#dswg_producer_logo').val(attachment.id);
        // Handle SVG (no thumbnail size)
        var url = attachment.sizes && attachment.sizes.thumbnail
            ? attachment.sizes.thumbnail.url : attachment.url;
        $('.dswg-logo-preview').html('<img src="' + url + '" />');
    });
    frame.open();
});
```

### Media Uploader — Multiple Files
```javascript
// library.type must be a STRING not an array — array breaks silently
// Omit library filter to allow all file types
frame = wp.media({ title: 'Select Files', button: { text: 'Add Files' }, multiple: true });
```

### Enqueue Requirements
```php
wp_enqueue_media(); // Must be called explicitly — not loaded by default
wp_enqueue_script('dswg-admin-script', ..., ['jquery'], DSWG_VERSION, true);
// 'jquery' is the only dependency needed — do NOT add 'media-upload' or 'thickbox'
```

---

## Plugin File Locations

```
plugins/
├── ds-wineguy/
│   ├── ds-wineguy.php                  # Main plugin, singleton class
│   ├── includes/
│   │   ├── post-types.php              # CPT registration + image sizes
│   │   ├── taxonomies.php              # Taxonomy registration + defaults
│   │   ├── meta-boxes.php              # All meta box callbacks + save
│   │   ├── template-functions.php      # Frontend helper functions
│   │   ├── search-filter.php           # Search/filter (placeholder)
│   │   └── carousel.php               # Swiper.js producer carousel
│   ├── admin/
│   │   ├── settings.php                # Admin settings page
│   │   └── importer.php                # CSV/Excel importer
│   └── assets/
│       ├── css/admin.css               # Admin interface styles
│       ├── css/wineguy.css             # Frontend styles
│       ├── js/admin.js                 # Media uploads, geocoding
│       ├── js/wineguy.js               # Frontend JS
│       ├── js/producer-carousel.js     # Carousel init + country filter
│       └── js/swiper-bundle.min.js     # Swiper library

├── ds-theme-customizations/
│   ├── ds-theme-customizations.php     # Main plugin + template loader
│   ├── includes/
│   │   └── functions.php               # SVG support, custom helpers
│   ├── templates/
│   │   └── single-dswg_producer.php    # Producer single page template
│   └── assets/
│       ├── plugin-style.less           # Main import (COMPILE THIS)
│       ├── plugin-style.css            # Compiled output (enqueued)
│       ├── _variables.less             # All CSS variables
│       ├── _typography.less            # Type styling
│       ├── _layout.less                # Containers, grids
│       ├── _components.less            # Buttons, cards, forms
│       ├── _wine-components.less       # Wine-specific components
│       ├── _accessibility.less         # Reduced motion, focus
│       ├── _modern-features.less       # Container queries, :has()
│       └── custom.js                   # Minimal JavaScript

└── ds-age-verification/
    └── (handles age gate + cookie popup)
```

---

## Data Scope

- **37 producers** (filtered from 54 total in inventory)
- **228 wines** (filtered from 306 total)
- **5 countries:** France (111 wines), Italy (75), Spain (32), Austria (7), Slovenia (3)
- Wines linked to producers via `dswg_producer_id` meta key

---

## GitHub Repository

**URL:** https://github.com/dotdors/integrity-wines (public)

**Structure:**
```
integrity-wines/
├── themes/dandysite-jane/
├── plugins/ds-wineguy/
├── plugins/ds-theme-customizations/
├── plugins/ds-age-verification/
├── PROJECT-TODO.md
└── TECHNICAL-REFERENCE.md
```

---

## Level-Set Checklist for New Chats

**At the start of any new chat, I should know:**

1. CPT names: `dswg_producer`, `dswg_wine`
2. Taxonomy names: `dswg_country`, `dswg_region`, `dswg_wine_type`
3. URL slugs: `/producers/`, `/wines/`, `/country/`, `/wine-type/`
4. Producer→Wine relationship: `dswg_producer_id` meta key on wine
5. Template naming: `single-dswg_producer.php` lives in `ds-theme-customizations/templates/`
6. Producer fields: `dswg_location`, `dswg_short_desc`, `dswg_highlights`
7. Meta box IDs use `dswg_mb_` prefix — inputs use `dswg_` prefix — NEVER the same
8. CSS prefix: component classes (no prefix), admin classes (`dswg-`)
9. LESS compilation: edit `.less` files, save, Easy LESS compiles to `.css`
10. Fonts: EB Garamond (headings/display), Lato (body/UI) - both Google Fonts
11. Background: `#FFF7E9` (page), `#F3E9D7` (alt sections)
12. Producer logos must be PNG or SVG with transparent background (rendered white over images via CSS filter)
13. Story section uses CSS max-height collapse with JS toggle
14. Wine grid uses inline accordion expansion (Option A) not row-takeover
15. Block editor IS active on producers and wines (`show_in_rest: true`)
16. `wp_enqueue_media()` is called in `enqueue_admin_assets()` — dependency is just `['jquery']`

---

*Last updated: February 21, 2026*
*Plugin version: ds-wineguy v1.0.0, ds-theme-customizations v1.0*
