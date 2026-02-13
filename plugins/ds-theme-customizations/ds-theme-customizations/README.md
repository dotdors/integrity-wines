# DS Theme Customizations - Integrity Wines

**Version:** 1.0.0  
**Purpose:** Site-specific branding, styling, and customizations for Integrity Wines  
**Base Theme:** dandysite-jane  
**Approach:** CSS-first, modern features, progressive enhancement

---

## Purpose

This plugin provides the visual branding layer for Integrity Wines, sitting between the base theme (dandysite-jane) and the functional plugin (ds-wineguy).

**3-Layer Architecture:**
1. **dandysite-jane theme** - Foundation CSS, basic structure
2. **ds-theme-customizations** ← This plugin - Integrity Wines branding
3. **ds-wineguy plugin** - Wine/producer functionality

---

## File Structure

```
ds-theme-customizations/
├── ds-theme-customizations.php    # Main plugin file
├── README.md                      # This file
├── assets/
│   ├── plugin-style.less          # Main import file (compile this!)
│   ├── plugin-style.css           # Compiled output (enqueued)
│   ├── _variables.less            # Brand colors, spacing, typography
│   ├── _typography.less           # Font styling
│   ├── _layout.less               # Containers, grids, spacing
│   ├── _components.less           # Buttons, cards, forms, nav
│   ├── _wine-components.less      # Wine/producer specific components
│   ├── _accessibility.less        # Reduced motion, focus states
│   ├── _modern-features.less      # Container queries, :has(), etc.
│   └── custom.js                  # Minimal JavaScript
├── includes/
│   └── functions.php              # Custom PHP functions (optional)
└── templates/                     # Template overrides (if needed)
```

---

## Color Palette

Based on logo design (pending final branding from designer):

### Core Brand Colors
- **Background:** `#F3E9D7` - Warm parchment/antique cream
- **Wine Red:** `#7A1F2B` - Deep burgundy (primary brand color)
- **Vineyard Green:** `#3F6B3A` - Classic wine country green
- **Gold Accent:** `#C6A64B` - Warm antique gold (for highlights)
- **Text:** `#2A2420` - Warm black (dark brown-black)

### Supporting Colors
- Burgundy highlight: `#9B2C3A`
- Dark green accent: `#2E4F2A`
- Darker gold: `#9E7C2F`
- Wood tone: `#B0895A`
- Brass/metal: `#8A6A2F`

### Semantic Assignments
```less
@color-primary: @color-wine-red;        // Primary brand
@color-secondary: @color-green-primary; // Secondary brand
@color-accent: @color-gold-primary;     // Highlights
```

**Note:** Final colors will be updated when designer provides branding assets.

---

## Typography

**Fonts:** TBD - Waiting for branding  
**Temporary:** Georgia (serif) for primary, system fonts for secondary

### Font Sizes (Fluid with clamp())
- Base: `1rem`
- Small: `0.875rem`
- Large: `1.125rem`
- XL: `1.25rem`
- 2XL: `1.5rem`
- 3XL: `2rem`
- 4XL: `2.5rem`

---

## Component Library

### Cards
- **Producer Card** - For archive and featured displays
- **Wine Card** - Bottle image, vintage, type
- **Country Card** - Map, producer count

### Buttons
- **Primary** - Wine red background
- **Secondary** - Outlined wine red
- **Accent** - Gold background
- **Small/Large variants**

### Forms
- Text inputs
- Textareas
- Select dropdowns
- Checkboxes/radios
- Filter groups

### Wine-Specific
- Producer grid
- Wine grid  
- Filter sidebar
- Gallery
- Download links
- Social links

---

## Modern CSS Features Used

### Container Queries
Cards adapt to their container width:
```css
@container card (min-width: 300px) {
  .wine-card__content {
    padding: var(--spacing-lg);
  }
}
```

### :has() Parent Selector
```css
.producer-card:has(.wine-count--zero) {
  opacity: 0.7;
}
```

### :is() / :where()
```css
.wine-card,
.producer-card,
.country-card {
  &:is(:hover, :focus-within) {
    transform: translateY(-2px);
  }
}
```

### Accessibility Features
- **prefers-reduced-motion** - Respects user preferences
- **Focus states** - High contrast, visible outlines
- **Skip links** - Keyboard navigation
- **Screen reader text** - Semantic HTML

**All features include fallbacks for older browsers!**

---

## Development Workflow

### LESS Compilation

1. **Edit** `.less` files in VS Code
2. **Auto-compile** using Easy LESS extension
3. **Compiled** `plugin-style.css` is auto-generated
4. **WordPress** enqueues the compiled CSS

**Main import file:** `plugin-style.less`  
**Compiled output:** `plugin-style.css`

### Adding New Styles

**For brand colors/spacing:**
→ Edit `_variables.less`

**For typography:**
→ Edit `_typography.less`

**For layout utilities:**
→ Edit `_layout.less`

**For general components:**
→ Edit `_components.less`

**For wine-specific components:**
→ Edit `_wine-components.less`

---

## CSS Variable Usage

All LESS variables are converted to CSS custom properties for runtime flexibility:

```less
// In _variables.less
@color-wine-red: #7A1F2B;

:root {
  --color-wine-red: @color-wine-red;
}

// Use in components
.wine-card__title {
  color: var(--color-wine-red);
}
```

**Why both LESS and CSS variables?**
- **LESS vars** = Compile-time calculations
- **CSS vars** = Runtime changes (JS access, theming)

---

## Updating Brand Colors

When designer provides final branding:

1. Open `assets/_variables.less`
2. Update brand color values:
   ```less
   @color-background: #NewColor;
   @color-wine-red: #NewColor;
   @color-gold-primary: #NewColor;
   // etc.
   ```
3. Update font families:
   ```less
   @font-primary: 'BrandFont', serif;
   ```
4. Save (LESS auto-compiles to CSS)
5. Test across site

**All components inherit the new brand automatically!**

---

## Browser Support

**Target browsers:**
- Chrome/Edge 90+
- Firefox 85+
- Safari 15+
- Mobile browsers (last 2 versions)

**Strategy:**
- Progressive enhancement
- Feature detection with `@supports`
- Graceful degradation
- Core functionality works everywhere

---

## What Lives Where

### In This Plugin (ds-theme-customizations)
✅ Brand colors and fonts  
✅ Site-wide styling  
✅ Visual components (cards, buttons)  
✅ Layout patterns  
✅ Navigation styling  

### NOT in This Plugin
❌ CPT registration (ds-wineguy)  
❌ Meta boxes (ds-wineguy)  
❌ Template files (theme)  
❌ Theme framework (dandysite-jane)  

---

## Dependencies

**Required:**
- dandysite-jane theme (active)
- WordPress 6.0+
- Easy LESS extension (VS Code)

**Works With:**
- ds-wineguy plugin (wine functionality)
- ds-age-verification plugin (age gate)

---

## Performance

- CSS is compiled and minified
- Cache-busting with `filemtime()`
- Minimal JavaScript
- CSS-first approach reduces JS payload

---

## Next Steps

1. **Wait for branding** - Designer providing final colors/fonts
2. **Update variables** - Apply brand to `_variables.less`
3. **Build templates** - Create page layouts in theme
4. **Test components** - Verify all components work
5. **Launch** - Activate and go live!

---

## Changelog

### Version 1.0.0 (January 2025)
- Initial plugin setup
- Brand color palette (preliminary)
- Modern CSS features
- Component library
- Accessibility features
- Wine-specific components

---

**Last Updated:** January 14, 2025  
**Status:** Ready for branding updates  
**Author:** Nancy Dorsner - Dabbled Studios
