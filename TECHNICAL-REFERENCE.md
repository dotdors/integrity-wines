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
| **Template file** | `single-dswg_producer.php` |
| **Archive template** | `archive-dswg_producer.php` |

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
| Producer Logo | `dswg_producer_logo` | int | Single attachment ID - **must be PNG with transparent bg** |
| Producer Files | `dswg_producer_files` | string | Comma-separated attachment IDs (PDFs etc) |

**Also available on Producers:**
- Featured Image = main producer photo (standard WP `_thumbnail_id`)
- Header style = `_dsp_header_style` (`overlay` or `solid`) — drives hero layout on producer page

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

## Design System

The site uses a single unified design — the "Immersive" aesthetic (formerly V2). All styles live directly in the base LESS files. No design variant switching is active.

**Typography:** EB Garamond throughout (headings and body). Body size 1.125rem. Headings centered by default, with explicit left-align exceptions inside cards, connect sections, footer, and story text.

**What happened to V1/V2:** A two-variant demo switcher existed during client review. Client selected V2 (March 2026). All V2 styles were merged into the base files. `_design-v2.less` is now a tombstone placeholder. `_design-v1.less` is preserved in repo but not imported. The switcher UI is commented out in `ds-theme-customizations.php` — re-enable by uncommenting `render_design_switcher` action hook and restoring the cookie logic in `add_design_body_class()`.

---

## CSS Class Naming Conventions

Plugin prefix: `dswg-` (for admin/functional CSS)
Theme prefix: none — BEM-style component names

### Key Component Classes (from ds-theme-customizations)

**Producer page:**
- `.producer-hero` / `.producer-hero--overlay` / `.producer-hero__image` / `.producer-hero__overlay` / `.producer-hero__logo` / `.producer-hero__identity` / `.producer-hero__name` / `.producer-hero__location`
- `.producer-identity` / `.producer-identity__name` / `.producer-identity__location` (solid header layout)
- `.producer-intro-grid` / `.producer-intro` / `.producer-highlights`
- `.producer-connect-section` / `.producer-connect` / `.producer-connect__logo-col` / `.producer-connect__identity` / `.producer-connect__label` / `.producer-connect__col` / `.producer-connect__social` / `.producer-connect__map`

**Split Hero (reusable):**
- `.split-hero` / `.split-hero__image` / `.split-hero__image-placeholder` / `.split-hero__panel` / `.split-hero__label` / `.split-hero__title` / `.split-hero__desc`
- Self-contained two-column layout: image left, text panel right. Does NOT depend on homepage.css.
- Collapses to stacked single column below 900px.

**Cards:**
- `.producer-card` / `.producer-card__image` / `.producer-card__content` / `.producer-card__title` / `.producer-card__country` / `.producer-card__excerpt` / `.producer-card__meta`
- `.wine-card` / `.wine-card__image` / `.wine-card__content` / `.wine-card__title` / `.wine-card__vintage` / `.wine-card__type` / `.wine-card__meta-row` / `.wine-card__expand-hint`
- `.wine-card--expandable` / `.wine-card--open` / `.wine-card__toggle` / `.wine-card__expand` / `.wine-card__expand-inner` / `.wine-card__details` / `.wine-card__excerpt` / `.wine-card__files`
- `.ds-producer-card` / `.ds-producer-card__image` / `.ds-producer-card__info` / `.ds-producer-card__name` / `.ds-producer-card__country` / `.ds-producer-card__link` (carousel cards — gold glass info bar)

**Wine type badge modifiers:**
- `.wine-card__type--red` / `--white` / `--rose` / `--sparkling`

**Sections:**
- `.section` — base section, vertical padding (`--spacing-lg`)
- `.section--alt` — alternate background. Flattened to match main bg site-wide EXCEPT `.producer-connect-section`
- `.section--narrow` — constrains to `--content-max-width`
- `.fullwidth` — edge to edge, no horizontal padding
- `.producer-connect-section` — always keeps alt background regardless of section-alt flattening
- `.carosection` — carousel section, gets alt background

**Section layout:** Add CSS classes in Gutenberg block editor to apply to block groups. `.section` = vertical padding, `.section--narrow` or `.fullwidth` = horizontal control.

**Grids:**
- `.producer-grid` — `minmax(var(--producer-card-width), 1fr)`, gap `--spacing-md`
- `.wine-grid` — `minmax(var(--wine-card-width), 1fr)`

**Story:**
- `.story` / `.story__body` / `.story__grid` / `.story__text` / `.story__photos` / `.story__toggle` / `.story__toggle-text`
- JS toggle: `#producer-story`, `#producer-story-content`, `#story-toggle`
- Expanded state: `.is-expanded` on `story__body` and toggle button

**Section headers:**
- `.section-header` / `.section-header__label` / `.section-header__title` / `.section-header__desc`
- LESS mixin: `.section-title-styles()` — use instead of hardcoding heading sizes

**Downloads:** `.download-list` / `.download-item` / `.download-link`

**Gallery:** `.photo-grid` / `.photo-grid--single-column` / `.photo-grid__item`

**Buttons:** `.button` / `.button--secondary` / `.button--accent` / `.button--small` / `.button--large`

---

## CSS Variables (Key Ones)

```css
/* Colors */
--color-background: #FFF7E9;
--color-background-alt: #F3E9D7;
--color-wine-red: #7A1F2B;
--color-wine-highlight: #9B2C3A;
--color-green-primary: #3F6B3A;
--color-gold-primary: #C6A64B;
--color-text: #2A2420;
--color-text-light: #5A524D;
--color-text-muted: #7A706A;
--color-surface: #FFFFFF;
--color-border: #E8D8C0;

/* Typography */
--font-primary: 'EB Garamond', Georgia, serif;  /* All text */
--font-secondary: 'EB Garamond', Georgia, serif;
--font-heading: var(--font-primary);             /* Alias for homepage.css */
--font-body: var(--font-primary);                /* Alias for homepage.css */

/* Spacing */
--spacing-xs: 0.25rem;  --spacing-sm: 0.5rem;
--spacing-md: 1rem;     --spacing-lg: 2rem;
--spacing-xl: 3rem;     --spacing-2xl: 4.5rem;

/* Layout */
--container-max-width: 1600px;
--content-max-width: 1100px;
--producer-card-width: 260px;
--wine-card-width: 240px;

/* UI */
--border-radius: 0;
--border-radius-lg: 0;
--box-shadow: 0 1px 4px rgba(42, 36, 32, 0.05);
--box-shadow-lg: 0 2px 12px rgba(42, 36, 32, 0.08);
--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

/* Header */
--header-height: 70px;
--header-logo-height: 44px;  /* overlay body class: 20px, solid: 25px */
--header-nav-font-size: 0.85rem;

/* Footer */
--footer-bg: var(--color-background);
--footer-border: var(--color-border);
--footer-colophon-bg: var(--color-surface);
```

---

## Plugin File Locations

```
plugins/
├── ds-wineguy/
│   ├── ds-wineguy.php                  # Main plugin, singleton class
│   ├── includes/
│   │   ├── post-types.php              # CPT registration + image sizes
│   │   ├── taxonomies.php              # Taxonomy registration + defaults + dswg_country term meta
│   │   ├── meta-boxes.php              # All meta box callbacks + save
│   │   ├── template-functions.php      # Frontend helper functions
│   │   ├── shortcodes.php              # [producer_grid] shortcode + dswg_render_producer_card() + dswg_render_producer_grid()
│   │   └── search-filter.php           # AJAX handler: dswg_filter_producers (searches title, location, short_desc, region meta)
│   ├── admin/
│   │   ├── settings.php                # Admin settings page
│   │   └── importer.php                # CSV/Excel importer
│   ├── templates/
│   │   ├── producer-carousel.php       # Swiper.js carousel shortcode
│   │   └── partials/
│   │       └── producer-card.php       # Shared card partial — used by archive, taxonomy, shortcode, AJAX
│   └── assets/
│       ├── css/admin.css               # Admin interface styles
│       ├── css/wineguy.css             # Frontend styles
│       ├── js/admin.js                 # Media uploads, geocoding
│       ├── js/wineguy.js               # Frontend JS
│       └── images/wineplaceholder.png  # Fallback bottle image (upload to server)
│
├── ds-theme-customizations/
│   ├── ds-theme-customizations.php     # Main plugin — switcher preserved but inactive
│   └── assets/
│       ├── plugin-style.less           # Main import — COMPILE THIS
│       ├── plugin-style.css            # Compiled output (enqueued)
│       ├── _variables.less             # All CSS variables, spacing, layout, card widths
│       ├── _typography.less            # Type styling + heading alignment rules
│       ├── _layout.less                # Containers, grids, section layout
│       ├── _components.less            # Buttons, nav, forms, sections, producer-identity, story, etc.
│       ├── _wine-components.less       # Wine/producer cards, grids, carousel card, connect section
│       ├── _accessibility.less         # Reduced motion, focus
│       ├── _modern-features.less       # Container queries, :has()
│       ├── _design-v1.less             # NOT imported — preserved in repo only
│       └── _design-v2.less             # Tombstone — styles merged into base files March 2026
│   └── templates/
│       ├── single-dswg_producer.php    # Producer single page template
│       ├── archive-dswg_producer.php   # Producer index (/producers/) — AJAX filter bar + card grid
│       └── taxonomy-dswg_country.php   # Country pages (/country/{slug}/) — split-hero + card grid
│
└── ds-age-verification/
    └── (handles age gate + cookie popup)
```

---

## Producer Page Template Architecture

`single-dswg_producer.php` is loaded by `ds-theme-customizations` template loader.

**Hero layout** is driven by `_dsp_header_style` post meta:
- `overlay` → producer name/location rendered inside the hero image (`.producer-hero__identity` glass bar)
- `solid` (default) → name/location rendered in `.producer-identity` section below hero

**Section structure** (in order):
1. `.producer-hero` — full-width hero image + logo
2. `.producer-identity` — name + location (solid header only)
3. `#producer-intro-section .section` → `.container--narrow` — short desc + highlights grid
4. `.section` → `.container--narrow` — The Story (expandable JS toggle)
5. `.section` → `.container--narrow` — The Wines grid (expandable wine cards)
6. `.section.section--alt.producer-connect-section` → `.container` (wider) — Connect section

**Key template decisions:**
- All content sections use `.container--narrow` except Connect (intentionally wider)
- `.section-header > h2.section-header__title` used consistently on Story and Wines sections
- Gallery IDs read from `dswg_gallery_ids` meta (comma-separated attachment IDs)
- Wine placeholder: `WP_PLUGIN_URL . '/ds-wineguy/assets/images/wineplaceholder.png'`
- Connect logo uses `full` image size (not `thumbnail`) to avoid crop on non-square logos

---

## Section Layout System

Horizontal padding managed in `_layout.less` using `max()`:

```css
.section:not(.fullwidth) {
  padding-left:  max(--spacing-xl, calc((100% - --container-max-width) / 2));
}
.section.section--narrow {
  padding-left:  max(--spacing-xl, calc((100% - --content-max-width) / 2));
}
```

Front-page block groups get the same treatment automatically via `.front-page-content > .wp-block-group`.

---

## Data Scope

- **37 producers** (filtered from 54 total in inventory)
- **228 wines** (filtered from 306 total)
- **5 countries:** France (111 wines), Italy (75), Spain (32), Austria (7), Slovenia (3)

---

## GitHub Repository

**URL:** https://github.com/dotdors/integrity-wines (public)

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

1. CPT names: `dswg_producer`, `dswg_wine`
2. Taxonomy names: `dswg_country`, `dswg_region`, `dswg_wine_type`
3. URL slugs: `/producers/`, `/wines/`, `/country/`, `/wine-type/`
4. Producer→Wine relationship: `dswg_producer_id` meta key on wine
5. Template naming: `single-dswg_producer.php`, `archive-dswg_producer.php`, etc.
6. Producer page template is in `ds-theme-customizations/templates/` not the theme
7. Producer fields: `dswg_location`, `dswg_short_desc`, `dswg_highlights`
8. Hero layout driven by `_dsp_header_style` meta (`overlay` vs `solid`)
9. CSS prefix: component classes (no prefix), admin classes (`dswg-`)
10. LESS compilation: edit `.less` files, save, Easy LESS compiles to `.css`
11. Fonts: EB Garamond everywhere — single font, no Lato in production
12. `--font-body` / `--font-heading` aliases exist for homepage.css compatibility
13. Background: `#FFF7E9` (main), alt: `#F3E9D7`
14. Producer logos: PNG with transparent bg — rendered white via `brightness(0) invert(1)`
15. Story section: CSS max-height 500px collapse with JS toggle (`#producer-story-content`)
16. Wine cards: inline accordion expand (`.wine-card--expandable`, JS in producer template)
17. Design variant system retired March 2026 — single unified style, no body class switching
18. Section layout: add CSS classes in block editor — `section`, `section--narrow`, `fullwidth`
19. `.section-title-styles()` LESS mixin exists — use it instead of hardcoding heading sizes
20. `dswg_country` term meta: `dswg_country_map_id` (attachment ID) — upload on Edit Country screen
21. Producer card partial: include via `dswg_render_producer_card( $id )`
22. Producer grid helper: `dswg_render_producer_grid( $args )` — used by shortcode + AJAX
23. `[producer_grid]` shortcode attrs: `country`, `region`, `limit`, `orderby`, `order`
24. AJAX action: `dswg_filter_producers` — POST country + search, returns `{html, count}`
25. `.split-hero` — reusable two-column layout, defined in `_wine-components.less`, no homepage.css dependency
26. `grid-template-rows: subgrid` removed from `_modern-features.less` — was collapsing producer-grid rows to 0
27. Archive page uses `<div class="producer-archive">` not `<main>` — header.php owns `<main id="primary">`
28. `.section--alt` flattened to main bg site-wide EXCEPT `.producer-connect-section`
29. Meta box registration ID must never match input field IDs within it (jQuery selector bug)
30. URL fields use `esc_url_raw()` not `sanitize_text_field()`

---

*Last updated: March 2026*
*Plugin version: ds-wineguy v1.1, ds-theme-customizations v1.1*
