# Integrity Wines — Project Quick Reference

**Client:** Pat + Daniela Pozo (Integrity Wines)
**Developer:** Nancy Dorsner, DandySite / Dabbled Studios
**Contract:** $9,500 | Contract signed | 50% deposit received
**Timeline:** Dec 2025 start → end Feb/early March 2026 launch
**Repo:** https://github.com/dotdors/integrity-wines (public)
**Hosting:** A2 Hosting (TBD) | CDN: Cloudflare

---

## Scope
- 37 producers, 228 wines, 5 countries (France, Italy, Spain, Austria, Slovenia)
- Full turnkey rebuild: custom WordPress theme + plugins, complete data migration
- No page builders, no ACF — native WordPress only
- LESS for CSS (Easy LESS compiles to .css), GitHub for version control

---

## Architecture
```
dandysite-jane (base theme)
ds-theme-customizations (branding/styling plugin)
ds-wineguy (producer/wine CPT functionality)
ds-age-verification (age gate popup)
```

---

## CPTs & Taxonomies
| Item | Registered Name | Slug |
|------|----------------|------|
| Producers CPT | `dswg_producer` | `/producers/` |
| Wines CPT | `dswg_wine` | `/wines/` |
| Country taxonomy | `dswg_country` | `/country/` |
| Region taxonomy | `dswg_region` | `/region/` |
| Wine Type taxonomy | `dswg_wine_type` | `/wine-type/` |

Producer → Wine relationship: `dswg_producer_id` meta key on wine post.

---

## Key Meta Keys
**Producer:** `dswg_location`, `dswg_short_desc`, `dswg_highlights`, `dswg_region`, `dswg_website`, `dswg_contact_email`, `dswg_contact_phone`, `dswg_instagram`, `dswg_facebook`, `dswg_twitter`, `dswg_address`, `dswg_latitude`, `dswg_longitude`, `dswg_gallery_ids`, `dswg_producer_logo`, `dswg_producer_files`

**Wine:** `dswg_producer_id`, `dswg_vintage`, `dswg_varietal`, `dswg_alcohol`, `dswg_wine_logo` *(label: "Wine Label")*, `dswg_wine_files`

---

## Key Conventions
- CSS class prefix: none (BEM) for frontend, `dswg-` for admin
- LESS variables in `_variables.less` — never hardcode values
- Producer logos: PNG with transparent bg, rendered white via CSS filter
- URL fields: `esc_url_raw()` not `sanitize_text_field()`
- Meta box registration ID must never match input field IDs within it
- Header style and hero layout controlled independently via per-page meta
- Story section: CSS max-height collapse + JS toggle
- Wine grid: inline accordion expansion (Option A)

---

## Fonts & Colors
- **Fonts:** EB Garamond (headings), Lato (body/UI) — Google Fonts
- **Background:** `#FFF7E9` | Alt section: `#F3E9D7`
- **Brand:** Wine red `#7A1F2B` | Green `#3F6B3A` | Gold `#C6A64B`

---

## Plugin File Locations
```
plugins/ds-wineguy/includes/post-types.php
plugins/ds-wineguy/includes/taxonomies.php
plugins/ds-wineguy/includes/meta-boxes.php
plugins/ds-wineguy/includes/template-functions.php
plugins/ds-wineguy/admin/importer.php
plugins/ds-wineguy/assets/js/admin.js
plugins/ds-theme-customizations/assets/plugin-style.less  ← COMPILE THIS
plugins/ds-theme-customizations/assets/_variables.less
```
