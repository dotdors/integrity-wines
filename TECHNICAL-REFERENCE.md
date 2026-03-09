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
| Inv Number | `dswg_inventory_no` | text | Client inventory number — unique identifier for import deduplication |
| Active | `dswg_wine_active` | int | `1` = active (shown on front end), `0` = inactive (hidden). Missing meta treated as active. |
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
- `.section--alt` — alternate background (`--color-background-alt`). Active site-wide.
- `.section--narrow` — constrains to `--content-max-width`
- `.fullwidth` — edge to edge, no horizontal padding
- `.producer-connect-section` — always keeps alt background; gets `padding-top/bottom: --spacing-2xl`
- `.carosection` — carousel section, gets alt background
- `.country-archive__back` — back link section on country pages (centered)

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

**Vertical spacing at color boundaries** — handled via sibling + `:has()` selectors in `_components.less`:
- Same-color sections: default `--spacing-lg` top and bottom (total gap = `2 × --spacing-lg`)
- Color change boundary: both the outgoing and incoming section get `--spacing-2xl` (`padding-bottom` via `:has()`, `padding-top` via sibling selector)
- `.site-main` has `padding-bottom: --spacing-2xl` globally; overridden to `0` on `.single-dswg_producer` (connect section sits directly on footer)
- `.wp-block-group` gets `padding: --spacing-lg 0` (matches `.section`); color-boundary rules come after in source order to win

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
31. Full-color logo: `get_option('dsp_logo_full')` — stored as WP option, NOT `get_theme_mod()`
32. Nav overlay: full-page cream overlay injected via `wp_body_open` hook in `ds-theme-customizations.php`; class `.nav-overlay`, menu class `.nav-overlay__menu`; controlled by `body.mobile-menu-open` (same JS as before)
33. Old slide-in panel permanently suppressed (`transition: none !important`) — nav overlay replaces it entirely
34. Age gate: `ds-age-verification/age-gate-renamed/` — popup styled with brand CSS vars, cream bg, glass overlay, full-color logo via `get_option('dsp_logo_full')`
35. Color-boundary spacing: `section + section--alt` → `padding-top: --spacing-2xl`; `:has(+ .section--alt)` → `padding-bottom: --spacing-2xl`
36. `.container` (bare, no modifier) inside a `.section` is redundant — removed from country, archive, connect section templates; `.container--narrow` inside a section is still correct and intentional
37. `.split-hero` gets `.section--alt` class on country pages — panel background must be `transparent` so alt bg shows through

38. `dswg_inventory_no` — wine meta key for client inventory number (e.g. `IW1042`); used as unique identifier for import deduplication
39. `dswg_wine_active` — wine meta key, `1` = active (shown front end), `0` = inactive (hidden). Missing meta treated as active. Bulk actions: Mark Active / Mark Inactive in wine list view.
40. Wine importer: column `Inv Number` triggers update-if-exists logic; empty cells skipped on update; `%` stripped from alcohol; accent-tolerant wine type matching; UTF-8/Windows-1252 encoding handled automatically. Export CSV from Google Sheets directly to avoid Excel encoding issues.
41. Wine single template: `single-dswg_wine.php` in `ds-theme-customizations/templates/` — three-column grid (bottle | details | sidebar)
42. SVG download icons in `ds-theme-customizations/assets/images/`: `icon-bottle.svg`, `icon-document.svg`, `icon-label.svg` — `currentColor` fill, inlined via `dsp_inline_svg()` helper in template
43. Bottle image: `mix-blend-mode: multiply` on `__bottle-img` + `radial-gradient(circle, #ffffff 45%, transparent 55%)` on `__bottle` div — sharp circle alt: `radial-gradient(circle, #ffffff 55%, transparent 55%)`
45. **Wine card redesign (March 2026):** Cards are now transparent/floating — no box, no shadow. `.wine-card__bottle` holds a `radial-gradient` white circle (50% stop, sharp edge) with `mix-blend-mode: multiply` on the bottle image. `aspect-ratio: 2/3` on the bottle div ensures the circle is portrait-proportioned. `.wine-card__info` holds centered title (wine-red) and subtitle (vintage · type). NO transforms on bottle or image — transforms create isolation stacking context and break `mix-blend-mode`. Hover effect is circle expansion (50→55%) via background swap only.

46. **Wine card active state:** `.wine-card--active` applies `filter: grayscale(0.5)` to `.wine-card__bottle-img`. Gold ring approach was abandoned (can't outline a gradient background cleanly).

47. **Wine card hover/focus overrides:** `_accessibility.less` and `_modern-features.less` both apply `transform + box-shadow` to `.wine-card` via `:is(:hover,:focus-within)`. Wine cards opt out via overrides in `_wine-components.less`. This works because `_wine-components.less` now loads **after** both files (see item 48).

48. **CSS import order in `plugin-style.less`:** `_wine-components.less` moved to position 7, after `_accessibility.less` (5) and `_modern-features.less` (6). This ensures wine-card overrides win the cascade without specificity hacks.

49. **Wine card row-panel expand (producer page):** Clicking a wine card on the producer page injects a `.wine-row-panel` element with `grid-column: 1/-1` after the last card in the same visual row (detected via `offsetTop` comparison). Panel has three columns: bottle (circle treatment) | details (varietal, tasting notes trimmed to 40 words, "View Full Details" link) | sidebar (wine type, ABV, downloads). A gold caret (`::before`) points back to the active card using `--caret-x` CSS variable set by JS. Card data stored in `<template class="wine-card__panel-data">` — invisible until JS clones it. JS lives in `single-dswg_producer.php` inline script.

50. **`dsp_inline_svg_producer()` helper:** Defined at the top of `single-dswg_producer.php` (with `function_exists` guard), not inside conditional blocks. Used for SVG icons in both wine panel downloads and producer connect downloads. Must remain at top level of the while loop — not nested inside `if ($wines->have_posts())`.

51. **Producer connect section — downloads:** `dswg_producer_files` meta is now loaded and parsed in `single-dswg_producer.php`. Downloads appear as a new `producer-connect__col--downloads` column using `producer-connect__download-link/icon/list` styles (same pattern as `wine-single__` downloads, defined in `_components.less`). Socials moved into the contact column with a `border-top` divider, eliminating the separate social column.

52. **Single wine `$has_sidebar` fix:** Was only checking `$label_id || $bottle_full || !empty($files)` — missed `$wine_type`, `$producer`, `$producer_loc`, `$alcohol`. Now matches the actual sidebar render condition so `wine-single__layout--no-sidebar` is not incorrectly applied.

53. **"More from Producer" grid (single wine page):** Updated to use the same `.wine-card__bottle` / `.wine-card__info` markup as the producer page. Cards are `<a>` tags (not buttons) so click goes directly to the single wine page. No expand panel. `section--alt` removed from this section. Button text changed to "← Explore [Producer Name]".

54. **Story section expanded layout:** When `.story__body.is-expanded` is toggled, `.story__grid` switches to `grid-template-columns: 1fr` (text full-width above, photos full-width below) and `.story__photos .photo-grid` switches to `repeat(auto-fill, minmax(220px, 1fr))` multi-column grid. Pure CSS — no JS changes. The `photo-grid--single-column` class stays on the HTML element but is overridden by the expanded-state selector.

55. **`--wine-card-width` variable:** Changed to `235px` (was `240px`) in `_variables.less`.

56. **`.producer-archive__results.section` padding fix:** Was `padding: var(--spacing-2xl) 0` which zeroed the left/right padding inherited from `.section:not(.fullwidth)`. Changed to `padding-top` + `padding-bottom` only.

---

*Last updated: March 9, 2026*
*Plugin version: ds-wineguy v1.1, ds-theme-customizations v1.2*
