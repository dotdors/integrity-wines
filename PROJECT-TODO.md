# Integrity Wines — Project TODO

**Last updated:** March 11, 2026

---

## STATUS: Active Development — Styling and UX Polish

Producer archive, country taxonomy, card system, wine single, and social icons are built and working.
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

### Session: March 11, 2026 — Social Icons, Typography, CSS Standards
- [x] Social links system built in `dandysite-jane/includes/social-settings.php` — `[ds_socials]` shortcode, `Appearance → Theme Settings → Social Links` section
- [x] `render_block` filter enables shortcodes in block-based widget areas (covers footer blocks)
- [x] Social icon SVGs: `icon-instagram.svg`, `icon-facebook.svg` in `dandysite-jane/assets/images/` — `fill="currentColor"` on root `<svg>` tag
- [x] Social icon CSS variable system: `--social-color` / `--social-color-hover` in `_variables.less` — green/gold for Integrity Wines; `.ds-socials span.ds-socials__icon` selector beats `.site-footer span` override
- [x] Country archive: `dswg_country_photo_id` term meta added (editorial photo for country cards) alongside existing `dswg_country_map_id`
- [x] `[country_grid]` shortcode added (`includes/shortcodes.php`) — renders country cards with bg photo, gold glass band, producer/wine counts
- [x] Country card CSS: `aspect-ratio: 16/9` desktop, `4/3` at `@bp-xl` (900px)
- [x] Breakpoints standardized — `@bp-sm/md/lg/xl/2xl` LESS variables in `_variables.less`; all raw px values replaced in `_components.less`, `_wine-components.less`, `_layout.less`
- [x] `_modern-features.less` gutted to tombstone — dead container queries, harmful transforms removed
- [x] Typography: `html { font-size: 112.5% }`, body `1.25rem`, `h1` standalone `font-weight: 400`
- [x] `.subheading` editor utility class — italic Garamond, shares `.subheading-styles()` mixin with `.producer-identity__location`
- [x] `.page-template-default .site-main { padding-top: var(--spacing-lg) }` added
- [x] Footer widget h3 styling added to `_components.less`

### Session: March 6, 2026 — Data Import + Wine Single Page
- [x] `dswg_inventory_no` meta field added to wines (admin meta box + importer) — used as unique key for import deduplication
- [x] `dswg_wine_active` meta field added (checkbox, defaults active) — hides wines from front end without deleting
- [x] Wine importer overhauled: update-if-exists by inv number, skip empty cells on update, new wines default active, `get_page_by_title` deprecation fixed
- [x] Importer: `%` stripped from alcohol values, UTF-8/Windows-1252 encoding fix for Excel CSVs, UTF-8 BOM strip
- [x] Importer: accent-tolerant wine type matching (Rose = Rosé), partial matching (White = White Wine)
- [x] Wine list view enhancements (`admin/wine-list.php`): Inv Number + Producer + Active columns, filter dropdowns by producer and active status, Mark Active / Mark Inactive bulk actions
- [x] Front-end wine queries updated to exclude inactive wines in both `dswg_get_producer_wines()` and `single-dswg_producer.php`
- [x] `single-dswg_wine.php` built — three-column layout: bottle image | wine details | sidebar
- [x] Wine single: producer logo (full color, no filter), wine name h1, vintage subtitle, Varietal/Blend eyebrow, Tasting Notes eyebrow, description
- [x] Wine single sidebar: wine type, producer name (link), location, ABV, downloads (bottle/label/tech sheets with SVG icons), label image
- [x] SVG download icons created (`icon-bottle.svg`, `icon-document.svg`, `icon-label.svg`) — `currentColor` fill, stored in `ds-theme-customizations/assets/images/`
- [x] "More from [Producer]" wine grid at bottom of wine single — excludes current wine, links through to wine singles
- [x] Bottle image: radial gradient white circle on `__bottle` div + `mix-blend-mode: multiply` on img — dissolves rectangular white bg, white labels still readable
- [x] Gold-line dividers between tasting notes fields; description headings left-aligned at 1.3em

### Session: March 5, 2026
- [x] Section vertical spacing system — `--spacing-lg` default, `--spacing-2xl` at color boundaries via sibling + `:has()` selectors
- [x] `.site-main padding-bottom: --spacing-2xl` globally; `0` on producer single (connect sits on footer)
- [x] `.producer-connect-section` explicit `padding-top/bottom: --spacing-2xl`
- [x] `.producer-connect__label` hidden (`display: none`)
- [x] `.section--alt` restored — removed V2 flattening rule; alt bg active site-wide again
- [x] Customizer rules migrated to `_components.less`: carousel wrapper full-bleed, `.carosection p` centered, `.wp-block-group` padding
- [x] Bare `.container` removed from country, archive, and connect section templates (redundant inside `.section`)
- [x] Country template: `.section--alt` added to `.split-hero`, "Explore All Producers" button centered + arrow removed
- [x] `.split-hero__panel` background → transparent; title → `text-align: left`; label → `--font-size-small`, `--color-text-light`
- [x] Full-page nav overlay (`.nav-overlay`) — cream bg, full-color green logo, large Garamond links, fades in on `body.mobile-menu-open`
- [x] Old slide-in panel permanently suppressed; no flash on resize
- [x] Age gate (`ds-age-verification`) — full restyle: cream popup, warm glass overlay, full-color logo, brand CSS variables throughout, `display:flex` centering fix

## NEEDS ATTENTION — Before / After Demo

### Demo cleanup (low priority, do after client confirms direction)
- [ ] **Remove design switcher** once client selects V1 or V2 (or keep for client to toggle themselves)
- [ ] **V1 `.fullwidth` = `.section--narrow` padding** — this is a demo hack, revisit proper fullwidth behavior post-decision
- [x] **`.producer-connect__label`** — hidden via `display: none` in `_components.less` (decided: label not needed, logo identifies the producer)
- [ ] **`--spacing-2xl` V2 override** — currently 4.5rem, was changed multiple times, confirm final value
- [ ] **Review hardcoded `min-height: fit-content !important`** on `.hero--split` in V1 — only needed because PHP outputs inline style, consider fixing at the PHP level instead

### Code cleanup
- [ ] Remove `footer.php-bak`, `functions2.php`, `header.cssv`, `back/` directory from repo
- [ ] Remove any remaining `<!-- DEBUG -->` HTML comments in templates (check all templates)
- [ ] Producer archive intro text is hardcoded in `archive-dswg_producer.php` — move to a WordPress option so Daniela can edit it

### CSS audit (do once layout is stable)
- [ ] **`.eyebrow` LESS mixin** — create shared mixin for eyebrow/label text (uppercase, tracked, small) and apply to `.section-header__label`, `.split-hero__label`, and any other instances. Currently these are near-duplicates drifting apart.
- [ ] **Full mixin audit** — scan all LESS files for repeated property groups (font stacks, text treatments, spacing patterns) and consolidate into mixins
- [ ] **Review front-page-specific rules in `_layout.less`** — may be fully redundant now that block groups use `.section` class consistently; verify and remove if so

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
- [x] Wine single page (`single-dswg_wine.php`) — three-column layout: bottle | details | sidebar

### Features Still Needed
- [x] AJAX filter by country + text search across name/location/region/short desc ✓
- [x] Country map images — stored as `dswg_country_map_id` term meta ✓
- [x] Age verification popup — restyled with brand CSS: cream popup, glass overlay, full-color logo, EB Garamond, brand colors throughout
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

### Wine Lifecycle / Data Management (future)
- [ ] **Bulk inactive wine cleanup** — once Mark Inactive bulk action has been used end-of-season, decide on a cleanup workflow: export inactive wines to a record, then delete? Or archive indefinitely? Consider building a "Delete all inactive wines" tool in the importer UI to make this one-click.
- [ ] **Season rollover workflow** — document the process: import new vintage spreadsheet → review updated/created count → bulk mark old vintages inactive as needed

### Producer Page
- [ ] **File downloads** — `dswg_producer_files` exists but not surfaced on the front end yet. Decide where it lives on the producer page (Connect section? After the story? Separate section?) then add download links using the same SVG icon pattern as the wine single page.
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
- **Mobile nav dead code** — The `===== MOBILE / SLIDEOUT NAV OVERRIDES =====` block at the top of the mobile nav section in `_components.less` (`:root` CSS vars, backdrop-filter on `.header--mobile-nav .header-nav`, cream nav link colors) predates the full-page overlay and applies to the old slide-in panel which is now permanently suppressed. Safe to audit and remove in a future CSS cleanup pass.
- **`--footer-border` specificity** — `footer.css` scopes this variable to `.site-footer`, so body-level `--color-border` overrides don't cascade into it. Must always set `--footer-border` explicitly on `.site-footer` in variant files (already done in V1).
- **Block editor section classes** — editors must manually add `section`, `section--narrow`, `fullwidth` CSS classes to block groups for the layout system to work. Document in client training.
- **`.featured` nav class** — V1 green CTA nav button requires adding "featured" CSS class to the menu item in Appearance → Menus. Document in client training.
- **Hero split min-height** — PHP outputs `style="min-height: 90vh"` inline. V1 overrides with `min-height: fit-content !important`. Long-term: fix at PHP level so the inline style isn't output at all for V1, or make it a template variable.
- **Dark footer** — infrastructure complete but not used. Easy to revisit.

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
