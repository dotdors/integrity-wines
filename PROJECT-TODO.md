# Integrity Wines — Project TODO

**Last updated:** February 18, 2026

---

## STATUS: Active Development

Site is live on staging. Theme and plugins actively being built and tested.
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
- [x] Producer single template (`single-dswg_producer.php`)
- [x] Wine display within producer pages
- [x] Swiper.js producer carousel

### Theme: dandysite-jane — Header System
- [x] `header.php` — fixed position, logo, nav, hamburger
- [x] `header.css` — full header styling system
- [x] `header-functions.php` — style detection, body classes, logo output
- [x] `header-settings.php` — WP Settings API (default style, overlay contexts)
- [x] `header.js` — scroll behavior, mobile nav, reveal states
- [x] Two layout modes: **solid** and **overlay** (transparent over hero)
- [x] Body class system: `header-style-overlay` / `header-style-solid`
- [x] Page templates for per-page override: `page-overlay-header.php`, `page-solid-header.php`
- [x] Admin bar offset (top: 32px / 46px mobile)
- [x] Mobile hamburger menu with slide-out panel
- [x] Full-viewport dim overlay when mobile menu open
- [x] Scroll reveal states (solid / glassy)
- [x] Single logo upload — CSS filter handles color inversion on dark bg
- [x] SVG upload support

### Theme: dandysite-jane — Footer System
- [x] `footer.php` — logo (optional), dual widget areas, footer nav, colophon
- [x] `footer.css` — layout system + dark mode via CSS variables
- [x] `footer-settings.php` — WP Settings API for layout, color, logo, widget max-width
- [x] Three layout options: **left**, **centered**, **spaced**
- [x] Logo hidden by default, shown via settings checkbox
- [x] Two widget areas: Primary (layout-controlled) + Secondary (centered)
- [x] Registered footer nav menu location
- [x] Dark mode toggle (global setting) — variables in plugin, theme has fallbacks
- [x] Optional widget max-width field (outputs inline CSS)
- [x] Copyright/colophon bar with dabbledstudios credit

### Plugin: ds-theme-customizations — Variables
- [x] Full brand color palette (wine red, greens, gold, parchment)
- [x] `@color-dark-*` variables for dark mode contexts
- [x] All CSS custom properties in `:root` for runtime use
- [x] Header override variables (sizing, colors, nav style, mobile menu)
- [x] Footer override variables (`.site-footer` + `body.footer-dark .site-footer`)
- [x] `--font-heading` alias added
- [x] All header/footer variables now overridable from plugin — theme has neutral fallbacks

---

## IN PROGRESS / NEXT UP

### Immediate
- [ ] **Mobile header scroll jitter** — slight up/down movement on logged-in mobile view
  (only affects admin bar context, low priority, check before launch)
- [ ] **Compile `plugin-style.less`** after today's `_variables.less` and `_components.less` changes
- [ ] **Clean up theme files** — `footer.php-bak`, `functions2.php`, `header.cssv`, `back/` directory found in repo — remove or archive

### Content Templates Still Needed
- [ ] Homepage template (`front-page.php`) — hero section, featured producers/wines
- [ ] Producers archive (`archive-dswg_producer.php`) with search/filter
- [ ] Wine archive (`archive-dswg_wine.php`)
- [ ] Country taxonomy template (`taxonomy-dswg_country.php`)
- [ ] Country showcase pages (5 pages: France, Italy, Spain, Austria, Slovenia)
- [ ] About page template
- [ ] Blog/news templates (archive + single)
- [ ] Contact page

### Features Still Needed
- [ ] Search and filtering (producer + wine archive)
  - Filter by country
  - Search across producers and wines
- [ ] Age verification popup (ds-age-verification plugin — exists, needs wiring)
- [ ] Instagram integration (company-level feed)
- [ ] Static country map graphics (one per country)
- [ ] Contact form with spam protection

### Data Entry (Weeks 5-6)
- [ ] All 37 producers fully built out
- [ ] All 228 wines fully built out
- [ ] Images optimized and uploaded
- [ ] Tech sheets and marketing PDFs uploaded
- [ ] Producer → Wine relationships established

### Performance & Polish
- [ ] Image optimization (lazy loading, compression)
- [ ] Cloudflare CDN configuration
- [ ] Caching strategy
- [ ] CSS/JS minification
- [ ] Cross-browser testing
- [ ] Mobile device testing

### Pre-Launch
- [ ] Client review period
- [ ] Admin training (video + written docs)
- [ ] DNS migration
- [ ] 30-day support period

---

## KNOWN ISSUES / NOTES

- **Dark footer** — infrastructure complete but readability needs work if client wants it.
  Variables are clean in plugin, easy to revisit. Not confirmed needed for this project.
- **`functions2.php`** — appears to be a backup/draft, needs review and removal
- **`back/` directory** in theme — check contents and remove from repo
- **`header.cssv`** — old version file, remove from repo
- **Footer secondary widget area** — centered by default, no settings control by design.
  Style overrides via CSS in ds-theme-customizations if needed.

---

## ARCHITECTURE REFERENCE

```
dandysite-jane (base theme — reusable across projects)
    ↓
ds-theme-customizations (Integrity Wines brand — colors, typography, overrides)
    ↓
ds-wineguy (wine/producer CPTs, meta, templates, importer)
ds-age-verification (age gate + cookie popup)
```

**Key principle:** Theme owns structure and behavior. Plugin owns brand/color.
Never add `.site-header` or `.site-footer` layout rules to the plugin —
theme's body class system controls that.

**CSS variable override chain:**
1. Theme defines neutral fallback vars (e.g. `footer.css`, `header.css`)
2. Plugin `_variables.less` → `plugin-style.css` overrides with brand values
3. Plugin loads after theme → plugin wins

**Footer layout body classes** (added by `footer-settings.php`):
- `footer-layout-left` / `footer-layout-center` / `footer-layout-spaced`
- `footer-dark`
- `footer-show-logo`

**Header layout body classes** (added by `header-functions.php`):
- `header-style-overlay` / `header-style-solid`
- `header-reveal-solid` / `header-reveal-transparent`
