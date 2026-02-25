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

## Design Variant System

Two design variants are implemented, switchable via an admin-only demo switcher (fixed bottom-right, visible to `manage_options` users only). The choice persists in a 7-day cookie (`iw_design`).

| | V1 "Editorial" | V2 "Immersive" |
|--|--|--|
| **Body class** | `design-v1` | `design-v2` |
| **Character** | Dense, structured, editorial | Airy, cinematic, European |
| **Body font** | Lato (sans-serif) | EB Garamond |
| **Heading alignment** | Left | Centered (with exceptions) |
| **Section dividers** | Visible border lines | None — sections flow together |
| **Nav style** | Full-height pipe separators, `.featured` item gets green CTA | Spaced uppercase, no borders |
| **Cards** | 1px border, sharp | Minimal, no border |
| **Default** | No | Yes (cookie default) |

**LESS files:** `_design-v1.less`, `_design-v2.less` — both scoped to their body class.
**Switching logic:** `ds-theme-customizations.php` → `add_design_body_class()` filter + `render_design_switcher()`.

### Design variant override rules
- Both variants load **after** base styles — load order handles most specificity
- Base theme `style.css` conflicts must be neutralized in `_components.less` (not in variant files)
- V2 flattens `section--alt` background to match main bg — `.producer-connect-section` is explicitly excluded via `:not()` so it always keeps its alt background
- V1 overrides `--color-border: var(--color-gold-dark)` and `--footer-border` on `.site-footer` directly (footer.css scopes its variable to `.site-footer`, so body-level override doesn't cascade)
- V1 overrides `--font-body: 'Lato'` — base has it pointing to Garamond. All body font references use `var(--font-body)` not hardcoded strings.

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

**Cards:**
- `.producer-card` / `.producer-card__image` / `.producer-card__content` / `.producer-card__title` / `.producer-card__country` / `.producer-card__excerpt` / `.producer-card__meta`
- `.wine-card` / `.wine-card__image` / `.wine-card__content` / `.wine-card__title` / `.wine-card__vintage` / `.wine-card__type` / `.wine-card__meta-row` / `.wine-card__expand-hint`
- `.wine-card--expandable` / `.wine-card--open` / `.wine-card__toggle` / `.wine-card__expand` / `.wine-card__expand-inner` / `.wine-card__details` / `.wine-card__excerpt` / `.wine-card__files`
- `.ds-producer-card` / `.ds-producer-card__image` / `.ds-producer-card__info` / `.ds-producer-card__name` / `.ds-producer-card__country` / `.ds-producer-card__link` (carousel cards)

**Wine type badge modifiers:**
- `.wine-card__type--red` / `--white` / `--rose` / `--sparkling`

**Sections:**
- `.section` — base section, applies vertical padding
- `.section--alt` — alternate background (`--color-background-alt`)
- `.section--narrow` — constrains to `--content-max-width` via `max()` horizontal padding
- `.fullwidth` — opts out of horizontal padding constraints (edge to edge)
- `.producer-connect-section` — always alt background regardless of design variant
- `.carosection` — carousel section, gets alt background in V2

**Section layout note:** Add CSS classes in the Gutenberg block editor (Additional CSS Class field) to apply these to block groups on the homepage. `.section` provides vertical padding, `.section--narrow` or `.fullwidth` controls horizontal.

**Grids:**
- `.producer-grid`
- `.wine-grid`

**Story:**
- `.story` / `.story__body` / `.story__grid` / `.story__text` / `.story__photos` / `.story__toggle` / `.story__toggle-text`
- JS toggle: `#producer-story`, `#producer-story-content`, `#story-toggle`
- Expanded state: `.is-expanded` on content, `.is-expanded` on toggle button

**Section headers:**
- `.section-header` / `.section-header__label` / `.section-header__title` / `.section-header__desc`
- LESS mixin: `.section-title-styles()` — use instead of hardcoding font/size/weight on any element that should match section title sizing

**Downloads:**
- `.download-list` / `.download-item` / `.download-link`

**Gallery:**
- `.photo-grid` / `.photo-grid--single-column` / `.photo-grid__item`

**Buttons:**
- `.button` (primary - wine red)
- `.button--secondary` (outlined)
- `.button--accent` (gold)
- `.button--small` / `.button--large`

**Nav (V1 specific):**
- Add `featured` CSS class to a menu item (Appearance → Menus → expand item → CSS Classes) to give it the green filled CTA treatment

---

## CSS Variables (Key Ones)

```css
/* Colors */
--color-background: #FFF7E9;      /* Main page background (warm cream) */
--color-background-alt: #F3E9D7;  /* Alt section background (deeper parchment) */
--color-wine-red: #7A1F2B;        /* Primary brand */
--color-wine-highlight: #9B2C3A;  /* Hover state */
--color-green-primary: #3F6B3A;   /* Secondary brand */
--color-green-dark: #2E4F2A;      /* Dark accent / nav CTA */
--color-gold-primary: #C6A64B;    /* Accent gold */
--color-gold-dark: #9E7C2F;       /* Dark gold / V1 borders */
--color-text: #2A2420;            /* Warm black */
--color-text-light: #5A524D;      /* Lighter text */
--color-text-muted: #7A706A;      /* Muted text */
--color-surface: #FDFBF7;         /* Card backgrounds */
--color-border: #D4C8B5;          /* Borders (base) */

/* Typography */
--font-primary: 'EB Garamond', Georgia, serif;   /* Headings */
--font-secondary: 'Lato', system-ui, sans-serif; /* UI elements */
--font-heading: var(--font-primary);             /* Alias for homepage.css */
--font-body: var(--font-primary);                /* Body text — V1 overrides to Lato */

/* Spacing */
--spacing-xs: 0.25rem;
--spacing-sm: 0.5rem;
--spacing-md: 1rem;
--spacing-lg: 2rem;
--spacing-xl: 3rem;
--spacing-2xl: 4rem;  /* V2 overrides to 4.5rem */

/* Layout */
--container-max-width: 1400px;   /* V2: 1600px, V1: 1280px */
--content-max-width: 900px;      /* V2: 1100px, V1: 820px */
--sidebar-width: 300px;

/* Header */
--header-height: 70px;           /* V1: 52px */
--header-logo-height: 44px;      /* V2 overlay: 20px, V2 solid: 25px, V1: 22px */
--header-nav-font-size: 0.95rem; /* V2: 1em */

/* Footer */
--footer-bg: var(--color-background);      /* Was color-background-alt, corrected */
--footer-border: var(--color-border);      /* V1 overrides on .site-footer directly */
--footer-colophon-bg: var(--color-background); /* V1 sets this */

/* UI */
--border-radius: 4px;   /* V1/V2: 0 or 2px */
--border-radius-lg: 8px;
--box-shadow: 0 2px 8px rgba(42, 36, 32, 0.08);
--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
│   │   └── search-filter.php           # Search/filter (placeholder)
│   ├── admin/
│   │   ├── settings.php                # Admin settings page
│   │   └── importer.php                # CSV/Excel importer
│   ├── templates/
│   │   └── producer-carousel.php       # Swiper.js carousel shortcode
│   └── assets/
│       ├── css/admin.css               # Admin interface styles
│       ├── css/wineguy.css             # Frontend styles
│       ├── js/admin.js                 # Media uploads, geocoding
│       ├── js/wineguy.js               # Frontend JS
│       └── images/wineplaceholder.png  # Fallback bottle image (upload to server)
│
├── ds-theme-customizations/
│   ├── ds-theme-customizations.php     # Main plugin (incl. design switcher)
│   └── assets/
│       ├── plugin-style.less           # Main import — COMPILE THIS
│       ├── plugin-style.css            # Compiled output (enqueued)
│       ├── _variables.less             # All CSS variables + font-body alias
│       ├── _typography.less            # Type styling
│       ├── _layout.less                # Containers, grids, section layout
│       ├── _components.less            # Buttons, cards, forms + .section-title-styles() mixin
│       ├── _wine-components.less       # Wine-specific components
│       ├── _accessibility.less         # Reduced motion, focus
│       ├── _modern-features.less       # Container queries, :has()
│       ├── _design-v1.less             # V1 "Editorial" variant (scoped to body.design-v1)
│       ├── _design-v2.less             # V2 "Immersive" variant (scoped to body.design-v2)
│       └── custom.js                   # Minimal JavaScript
│   └── templates/
│       └── single-dswg_producer.php    # Producer single page template
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
1. `.producer-hero` — full-width hero image + logo (all content widths)
2. `.producer-identity` — name + location (solid header only)
3. `#producer-intro-section .section` → `.container--narrow` — short desc + highlights grid
4. `.section` → `.container--narrow` — The Story (with expandable JS toggle)
5. `.section` → `.container--narrow` — The Wines grid (expandable wine cards)
6. `.section.section--alt.producer-connect-section` → `.container` (wider) — Connect section

**Key template decisions:**
- All content sections use `.container.container--narrow` except Connect (intentionally wider)
- `.section-header > h2.section-header__title` used consistently on Story and Wines sections
- All debug output removed — clean production template
- Gallery IDs read from `dswg_gallery_ids` meta (comma-separated attachment IDs)
- Wine placeholder image: `WP_PLUGIN_URL . '/ds-wineguy/assets/images/wineplaceholder.png'`
- Connect logo uses `full` image size (not `thumbnail`) to avoid WordPress crop on non-square logos

---

## Section Layout System

Horizontal padding is managed in `_layout.less` using `max()`:

```css
/* Default — padded, max-width constrained */
.section:not(.fullwidth) {
  padding-left:  max(--spacing-xl, calc((100% - --container-max-width) / 2));
  padding-right: max(--spacing-xl, calc((100% - --container-max-width) / 2));
}

/* Narrow — constrained to content-max-width */
.section.section--narrow { /* double-class for specificity */
  padding-left:  max(--spacing-xl, calc((100% - --content-max-width) / 2));
}

/* Full-width — edge to edge, no horizontal padding */
.fullwidth { /* just add class, overrides via section:not(.fullwidth) absence */ }
```

Front-page block groups get the same treatment automatically:
```css
.front-page-content > .wp-block-group:not(.fullwidth) { /* same as default */ }
.front-page-content > .wp-block-group.section--narrow  { /* same as narrow */ }
```

**In the block editor**, add these CSS classes to Gutenberg Group blocks:
- `section` → vertical padding
- `section--narrow` → narrow content width
- `fullwidth` → edge-to-edge (carousel, full-bleed images)
- `carosection` → carousel section (gets alt background in V2)
- `section--alt` → alternate background

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
├── FRONTEND-DEVELOPMENT-PLAN.md
└── TECHNICAL-REFERENCE.md (this file)
```

---

## Level-Set Checklist for New Chats

**At the start of any new chat, I should know:**

1. CPT names: `dswg_producer`, `dswg_wine`
2. Taxonomy names: `dswg_country`, `dswg_region`, `dswg_wine_type`
3. URL slugs: `/producers/`, `/wines/`, `/country/`, `/wine-type/`
4. Producer→Wine relationship: `dswg_producer_id` meta key on wine
5. Template naming: `single-dswg_producer.php`, `archive-dswg_wine.php`, etc.
6. Producer page template is in `ds-theme-customizations/templates/` not the theme
7. New producer fields: `dswg_location`, `dswg_short_desc`, `dswg_highlights`
8. Hero layout driven by `_dsp_header_style` meta (`overlay` vs `solid`)
9. CSS prefix: component classes (no prefix), admin classes (`dswg-`)
10. LESS compilation: edit `.less` files, save, Easy LESS compiles to `.css`
11. Fonts: EB Garamond (headings), Lato (V1 body) — both in Google Fonts enqueue
12. `--font-body` variable exists — V1 sets it to Lato, base/V2 = Garamond
13. Background: `#FFF7E9` (main), alt: `#F3E9D7`
14. Producer logos must be PNG with transparent background
15. Story section: CSS max-height collapse with JS toggle (`#producer-story-content`)
16. Wine cards: inline accordion expand (`.wine-card--expandable`, JS in producer template)
17. Design variants: `body.design-v1` / `body.design-v2` — cookie-switched, admin only
18. Section layout: add CSS classes in block editor — `section`, `section--narrow`, `fullwidth`
19. `.section-title-styles()` LESS mixin exists — use it instead of hardcoding heading sizes
20. `--font-body` / `--font-heading` aliases exist for homepage.css compatibility
21. V1 nav: `.featured` CSS class on menu item = green CTA button treatment
22. V1 borders use `--color-gold-dark`; `--footer-border` must be set on `.site-footer` directly (not body)

---

*Last updated: February 2026*
*Plugin version: ds-wineguy v1.1, ds-theme-customizations v1.1*
