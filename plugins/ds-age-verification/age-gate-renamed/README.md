# DS Age Verification & Cookie Gate

**Plugin File:** `ds-age-verification.php`  
**Settings Location:** Settings → DS Age Verification

## Plugin Renamed to Avoid Conflicts

This plugin was renamed from `age-gate.php` to `ds-age-verification.php` to avoid conflicts with other age verification plugins that may be installed.

All function names and settings are prefixed with `ds_` for namespace safety.

## What Changed from Original

### Major Improvements

**1. Two-Step User Flow**
- **Before:** Combined age checkbox with cookie preferences (confusing)
- **After:** Clean two-step process:
  1. "I'm 21 or Older" / "I'm Under 21" buttons
  2. Then show cookie preference options
- **Why:** Clearer user experience, separates concerns logically

**2. Decline Handling**
- **Before:** No decline option
- **After:** Explicit "I'm Under 21" button that redirects to safe external site
- **Why:** Best practice for age gates, shows good-faith compliance

**3. Cookie Security**
- **Before:** Basic cookie with no security attributes
- **After:** Includes `Secure` (HTTPS) and `SameSite=Strict` (CSRF protection)
- **Why:** Follows modern security best practices

**4. No jQuery Dependency**
- **Before:** Required jQuery (30KB+)
- **After:** Vanilla JavaScript
- **Why:** Faster, lighter, more modern

**5. Accessibility**
- **Before:** Missing ARIA attributes, no keyboard navigation
- **After:** Full ARIA support, focus trap, keyboard navigation, Escape key support
- **Why:** WCAG compliance, better UX for all users

**6. Better Admin Settings**
- **Added:** Decline redirect URL configuration
- **Added:** Input validation (cookie duration 1-365 days)
- **Added:** "Clear Cookie" testing button
- **Added:** Help text explaining how it works

**7. Cleaner Visual Design**
- Smooth animations and transitions
- Better button hierarchy (primary vs secondary)
- Improved mobile responsiveness
- CSS variables for easy customization

### What Stayed the Same (Per Requirements)

- Single popup approach (not separate age gate + cookie banner)
- Cookie preference option (remember me vs session)
- Information about cookie usage
- Exclusion of logged-in users
- Exclusion of cookie policy page

## Legal Context

**Age Verification:**
- Not legally required since site doesn't sell alcohol
- Voluntary best practice for alcohol industry websites
- Client-side verification is acceptable for informational sites

**Cookie Consent:**
- Only using "strictly necessary" cookies (age verification, security)
- GDPR: Strictly necessary cookies don't require opt-in consent
- Cookie preference is UX courtesy, not legal requirement
- No tracking/analytics cookies = simpler compliance

**Result:** Single popup that informs users and gives them control is appropriate.

## Installation

1. Upload the `ds-age-verification` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu
3. Go to Settings → DS Age Verification to configure

**Note:** This plugin is uniquely named to avoid conflicts with other age verification plugins.

## Configuration

### Required Settings
- **Title:** Main heading (default: "Age Verification")
- **Subtitle:** Message below title (default: "You must be 21 or older...")

### Optional Settings
- **Cookie Duration:** How many days the "Remember me" option lasts (default: 30)
- **Cookie Policy Page:** Link to your cookie policy (optional)
- **Decline URL:** Where to redirect under-21 users (default: Responsibility.org)

## File Structure

```
ds-age-verification/
├── ds-age-verification.php   # Main plugin file (renamed to avoid conflicts)
├── admin/
│   └── settings-page.php      # Admin settings interface
└── assets/
    ├── css/
    │   └── age-gate.css       # Popup styling
    └── js/
        └── age-gate.js        # Popup functionality
```

## Customization

### Styling
Edit `assets/css/age-gate.css` or override these CSS variables in your theme:

```css
:root {
    --age-gate-overlay-bg: rgba(31, 31, 31, 0.92);
    --age-gate-popup-bg: #1f1f1f;
    --age-gate-text: #ffffff;
    --age-gate-text-muted: #cccccc;
    --age-gate-border: rgba(255, 255, 255, 0.15);
    --age-gate-primary: #ff6b35;
    --age-gate-primary-hover: #ff8555;
}
```

### Branding Colors for Integrity Wines
Replace the primary color variables with your brand colors:

```css
:root {
    --age-gate-primary: #YOUR_BRAND_COLOR;
    --age-gate-primary-hover: #SLIGHTLY_LIGHTER_BRAND_COLOR;
}
```

## Testing

1. In WordPress admin, go to Settings → DS Age Verification
2. Click "Clear Age Verification Cookie" button
3. Open your site in a new incognito/private window
4. Test both "I'm 21 or Older" and "I'm Under 21" flows

## User Flow

```
1. Visitor arrives → Age gate appears
2. Two choices:
   a) "I'm 21 or Older" → Shows cookie preferences → Enter site
   b) "I'm Under 21" → Shows decline message → Redirects after 3 seconds

Cookie Preference Options:
   - Remember me for X days (persistent cookie)
   - This session only (session cookie, expires when browser closes)
```

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with polyfills if needed)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Accessibility Features

- ARIA labels and roles
- Keyboard navigation (Tab, Shift+Tab, Enter, Escape)
- Focus trap within popup
- High contrast support
- Screen reader friendly

## Performance

- No jQuery dependency (~30KB saved)
- Minimal JavaScript (~3KB minified)
- Efficient CSS (~4KB)
- No external requests
- Cached assets with version strings

## Security

- Cookie flags: `Secure` (HTTPS only), `SameSite=Strict` (CSRF protection)
- Sanitized and validated admin inputs
- Escaped output
- No XSS vulnerabilities

## Future Enhancements (Optional)

- [ ] Add analytics tracking for age gate interactions
- [ ] Multi-language support
- [ ] Custom messaging for declined users
- [ ] A/B testing different messaging
- [ ] Background image upload option
- [ ] More color scheme presets

## Support

For Integrity Wines project:
- Developer: Nancy Dorsner, DandySite
- Email: nancy@dabbledstudios.com

## Changelog

### Version 2.1 (January 2026)
- Two-step user flow (age check → cookie preference)
- Added decline handling with redirect
- Improved cookie security (Secure, SameSite)
- Removed jQuery dependency
- Added accessibility features
- Enhanced admin settings
- Cleaner visual design
- Better mobile support

### Version 2.0 (Original ChatGPT Version)
- Combined age + cookie popup
- jQuery-based
- Basic functionality
