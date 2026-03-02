# Integrity Wines — Project TODO

**Last updated:** March 2, 2026

---

## STATUS: Active Development — Producer Index & Country Pages Complete

Producer archive, country taxonomy, card system, and AJAX filtering are built and working.
All work tracked in GitHub: https://github.com/dotdors/integrity-wines

---

## COMPLETED ✓

### Infrastructure
- [x] New hosting setup, SSL, WordPress install
- [x] GitHub repo established with full project structure
- [x] Plugin architecture established (ds-wineguy, ds-theme-customizations, ds-age-verification)
- [x] dandysite-jane base theme connected

### Plugin: ds-wineguy
- [x] CPTs registered: `dswg_producer`, `dswg_wine`
- [x] Taxonomies: `dswg_country`, `dswg_region`, `dswg_wine_type`
- [x] Meta boxes for all producer and wine fields
- [x] Template functions (dswg_get_producer_wines, etc.)
- [x] CSV/Excel importer (admin/importer.php)
- [x] Producer single template (`single-dswg_producer.php`) — clean, no debug
- [x] Wine display within producer pages (expandable accordion cards)
- [x] Swiper.js producer carousel with country filtering + randomized order
- [x] Wine placeholder image fallback (`assets/images/wineplaceholder.png`)
- [x] Producer card partial (`templates/partials/producer-card.php`) — shared by archive, taxonomy, shortcode, AJAX
- [x] `[producer_grid]` shortcode — attrs: `country`, `region`, `limit`, `orderby`, `order`
- [x] `dswg_render_producer_card( $id )` and `dswg_render_producer_grid( $args )` helper functions (`includes/shortcodes.php`)
- [x] AJAX filter handler `dswg_filter_producers` — searches title, `dswg_location`, `dswg_short_desc`, `dswg_region` meta (`includes/search-filter.php`)
- [x] Country term meta: `dswg_country_map_id` (attachment ID) — upload UI on Edit Country screen (`includes/taxonomies.php`)

### Plugin: ds-theme-customizations
- [x] Full brand color palette and CSS variable system
- [x] `--font-body` and `--font-heading` alias variables (for homepage.css compatibility)
- [x] `.section-title-styles()` LESS mixin (use instead of hardcoding heading sizes)
- [x] Section layout system: `.section`, `.section--narrow`, `.fullwidth`, `.section--alt`
- [x] Front-page block group horizontal padding (auto-applied, no editor class needed for padding)
- [x] Design variant system with admin-only demo switcher (cookie-persisted)
- [x] **V2 "Immersive"** — all Garamond, centered headings, airy spacing, no dividers, minimal cards
- [x] **V1 "Editorial"** — Lato body, left headings, pipe nav, gold borders, section accent lines
- [x] `_design-v1.less` and `_design-v2.less` fully scoped to body class
- [x] Google Fonts: EB Garamond + Lato both enqueued
- [x] Base theme style.css conflicts neutralized in `_components.less`
- [x] Producer archive template (`templates/archive-dswg_producer.php`) — sticky filter bar, country dropdown, AJAX text search
- [x] Country taxonomy template (`templates/taxonomy-dswg_country.php`) — `.split-hero` layout, producer grid
- [x] `.split-hero` reusable component — self-contained two-column layout (image | text panel), no homepage.css dependency
- [x] `.ds-producer-card` styles moved into `_wine-components.less` (always loaded, not carousel-only)
- [x] `.tax-dswg_country .site-main { padding-top: 0 }` — country pages flush to header
- [x] Removed `grid-template-rows: subgrid` from `_modern-features.less` — was collapsing producer grid rows to 0

### Theme: dandysite-jane — Header System
- [x] Fixed-position header, logo, nav, hamburger
- [x] Two modes: solid and overlay (transparent over hero)
- [x] Per-page header style via `_dsp_header_style` meta — drives producer hero layout too
- [x] Scroll reveal states (solid / glassy)
- [x] Mobile hamburger menu
- [x] Body class system: `header-style-overlay` / `header-style-solid`

### Theme: dandysite-jane — Footer System
- [x] Three layout options: left, centered, spaced
- [x] Dark mode infrastructure
- [x] `--footer-bg` correctly mapped to `--color-background` (not alt)
- [x] Footer headings left-aligned (base + V2 exceptions)

### Producer Page
- [x] Hero layout driven by `_dsp_header_style` meta (overlay vs below)
- [x] Overlay: name/location inside hero glass bar (`.producer-hero__identity`)
- [x] Solid: name/location in `.producer-identity` section below hero
- [x] All sections constrained to `--content-max-width` except Connect (wider)
- [x] Connect section always uses `--color-background-alt` regardless of design variant
- [x] Connect logo uses `full` size (not cropped thumbnail)
- [x] All debug output removed from template
- [x] `.site-main { padding-top: 0 }` on producer/wine singles (hero flush to header)
- [x] Hero fullbleed: `.hero__logo` `max-width: 50vw; max-height: 30vh`
- [x] Hero split: image column height fix (align-items: stretch + height: 100%)
- [x] Hero split headline centered on fullbleed, left on split

---

## NEEDS ATTENTION — Before / After Demo

### Demo cleanup (low priority, do after client confirms direction)
- [ ] **Remove design switcher** once client selects V1 or V2 (or keep for client to toggle themselves)
- [ ] **V1 `.fullwidth` = `.section--narrow` padding** — this is a demo hack, revisit proper fullwidth behavior post-decision
- [ ] **V1 `.producer-connect__label`** — hidden for now, decide whether to show "Connect with [name]" heading
- [ ] **`--spacing-2xl` V2 override** — currently 4.5rem, was changed multiple times, confirm final value
- [ ] **Review hardcoded `min-height: fit-content !important`** on `.hero--split` in V1 — only needed because PHP outputs inline style, consider fixing at the PHP level instead

### Code cleanup
- [ ] Remove `footer.php-bak`, `functions2.php`, `header.cssv`, `back/` directory from repo
- [ ] Remove any remaining `<!-- DEBUG -->` HTML comments in templates (check all templates)
- [ ] Producer archive intro text is hardcoded in `archive-dswg_producer.php` — move to a WordPress option so Daniela can edit it

---

## IN PROGRESS / NEXT UP

### Post-Demo: Design Decision
- [ ] Client selects V1 or V2 direction (or hybrid)
- [ ] Refine chosen variant based on feedback
- [ ] Remove demo switcher, lock in design
- [ ] Build out whichever variant was NOT chosen as a starting point (or retire it)

### Content Templates Still Needed
- [x] Producer card partial (`ds-wineguy/templates/partials/producer-card.php`) — shared by all contexts
- [x] Producers archive (`archive-dswg_producer.php`) — AJAX search + country dropdown filter
- [x] `[producer_grid]` shortcode — `includes/shortcodes.php`
- [x] AJAX search/filter handler — `includes/search-filter.php`
- [x] Country taxonomy template (`taxonomy-dswg_country.php`) — split-hero layout, term meta map image
- [ ] Wine archive (`archive-dswg_wine.php`)
- [ ] About page template
- [ ] Blog/news templates (archive + single)
- [ ] Contact page
- [ ] Wine single page (`single-dswg_wine.php`) — currently no dedicated template

### Features Still Needed
- [x] AJAX filter by country + text search across name/location/region/short desc ✓
- [x] Country map images — stored as `dswg_country_map_id` term meta ✓
- [ ] Age verification popup (ds-age-verification plugin — exists, needs wiring)
- [ ] Instagram integration (company-level feed)
- [ ] Contact form with spam protection
- [ ] Producer archive intro text — move hardcoded copy to a WordPress option

> **⚠️ REVISIT: Country pages architecture decision**
> Currently built as taxonomy archive pages (`/country/france/`) with term meta for map image + description.
> Considered but deferred: making them proper WordPress Pages for richer editorial control (hero, Gutenberg blocks, featured image as map).
> Potential approach: do both — Pages for editorial content, keep tax archives for filtering/URLs. Needs URL strategy (redirect `/country/*` to pages? or coexist?).
> Revisit when client weighs in on how much editorial control they want over these pages.

### Data Entry (Weeks 5-6)
- [ ] All 37 producers fully built out
- [ ] All 228 wines fully built out
- [ ] Images optimized and uploaded
- [ ] Tech sheets and marketing PDFs uploaded
- [ ] Producer → Wine relationships established
- [ ] Upload `wineplaceholder.png` to `ds-wineguy/assets/images/` on server

### Performance & Polish
- [ ] Image optimization (lazy loading, compression)
- [ ] Cloudflare CDN configuration
- [ ] Caching strategy
- [ ] CSS/JS minification
- [ ] Cross-browser testing
- [ ] Mobile device testing
- [ ] Mobile nav for V1/V2 variants (pipes/sizing need mobile review)

### Pre-Launch
- [ ] Client review period
- [ ] Admin training (video + written docs)
- [ ] DNS migration
- [ ] 30-day support period

---

## KNOWN ISSUES / NOTES

- **Design variants are demo-only scaffolding** — V1 has several "quick fix for demo" comments in the LESS. These need a proper pass once the design direction is confirmed.
- **`--footer-border` specificity** — `footer.css` scopes this variable to `.site-footer`, so body-level `--color-border` overrides don't cascade into it. Must always set `--footer-border` explicitly on `.site-footer` in variant files (already done in V1).
- **Block editor section classes** — editors must manually add `section`, `section--narrow`, `fullwidth` CSS classes to block groups for the layout system to work. Document in client training.
- **`.featured` nav class** — V1 green CTA nav button requires adding "featured" CSS class to the menu item in Appearance → Menus. Document in client training.
- **Hero split min-height** — PHP outputs `style="min-height: 90vh"` inline. V1 overrides with `min-height: fit-content !important`. Long-term: fix at PHP level so the inline style isn't output at all for V1, or make it a template variable.
- **Dark footer** — infrastructure complete but not used. Easy to revisit.
- **Wine single page** — no template yet. Currently falls back to WP default single.php.

---

## ARCHITECTURE REFERENCE

```
dandysite-jane (base theme — reusable across projects)
    ↓
ds-theme-customizations (Integrity Wines brand — colors, typography, variants)
    ↓
ds-wineguy (wine/producer CPTs, meta, templates, importer)
ds-age-verification (age gate + cookie popup)
```

**Key principles:**
- Theme owns structure and behavior. Plugin owns brand/color.
- Never add layout rules that conflict with theme's body class system to the plugin without neutralizing the conflict first in `_components.less`
- Design variants (`_design-v1.less`, `_design-v2.less`) layer on top of base — they are additive/override, not replacements. Base correctness lives in `_components.less`.
- LESS mixin `.section-title-styles()` — use it anywhere a heading should match section title sizing. Do NOT hardcode the values.
- `--font-body` variable controls body font — V1 sets it to Lato, base/V2 = Garamond. All body font references should use `var(--font-body)` not hardcoded strings.

**CSS variable override chain:**
1. Theme defines neutral fallback vars (`footer.css`, `header.css`)
2. Plugin `_variables.less` → `plugin-style.css` overrides with brand values
3. Design variant files override further, scoped to body class
4. Plugin loads after theme → plugin wins (no `!important` needed except for inline styles)

**Specificity lessons learned:**
- `body.design-v2 .section--alt` (0,2,1) beats `.producer-connect-section` (0,1,0) — use `:not()` to exclude
- `.front-page-content > .wp-block-group:not(.fullwidth)` is (0,3,0) — narrow override needs same or higher
- `li + li` border trick for nav pipes requires height propagation: `header-nav → ul (align-items:stretch) → li (height:100%) → a (height:100%)`
- Footer-scoped variables (`--footer-border` etc.) must be overridden on `.site-footer` directly, not on `body`
