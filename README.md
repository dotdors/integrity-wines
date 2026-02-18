# Integrity Wines — Website Rebuild

WordPress website for Integrity Wines, a wine importer/distributor representing artisanal estate-grown wines from European producers.

**Developer:** Nancy Dorsner, DandySite / Dabbled Studios  
**Repo:** https://github.com/dotdors/integrity-wines  
**See:** `PROJECT-TODO.md` for current status and task list  
**See:** `TECHNICAL-REFERENCE.md` for CPT names, meta keys, taxonomy slugs

---

## Plugin Architecture

```
dandysite-jane (base theme — reusable)
    ↓
ds-theme-customizations (Integrity Wines brand)
    ↓
ds-wineguy (wine/producer functionality)
ds-age-verification (age gate)
```

## Key Files

| File | Purpose |
|------|---------|
| `themes/dandysite-jane/includes/header-functions.php` | Header style detection, body classes |
| `themes/dandysite-jane/includes/header-settings.php` | Header WP settings panel |
| `themes/dandysite-jane/includes/footer-settings.php` | Footer WP settings panel |
| `themes/dandysite-jane/assets/css/header.css` | Header styles (edit this file directly) |
| `themes/dandysite-jane/assets/css/footer.css` | Footer styles (edit this file directly) |
| `plugins/ds-theme-customizations/assets/_variables.less` | **Brand colors and all CSS variable overrides — start here** |
| `plugins/ds-theme-customizations/assets/plugin-style.less` | Main LESS import — compile this |

## Development Notes

- Edit `.less` files, save → Easy LESS auto-compiles to `.css`
- `header.css` and `footer.css` are plain CSS — edit directly, no compilation needed
- Never add `.site-header` or `.site-footer` position/layout rules to the plugin
- Theme has neutral fallback variables; plugin overrides with brand values
