# DS Wine Guy Plugin

A comprehensive WordPress plugin for managing wine producers, wines, and wine-related content for Integrity Wines.

## Version
1.0.0

## Description
This plugin provides complete wine distributor functionality including:
- Producer (Winery) Custom Post Type with full details
- Wine Custom Post Type with relationships to producers
- Country, Region, and Wine Type taxonomies
- Image galleries for producers
- File uploads for tech sheets and marketing materials
- Search and filtering capabilities
- Template functions for frontend display

## Features

### Custom Post Types

#### Producers (dswg_producer)
- Full bio/description
- Featured image
- Image gallery
- Contact information (email, phone)
- Website URL
- Social media links (Instagram, Facebook, Twitter)
- Location coordinates (for future map integration)
- Region information
- Relationship to Country taxonomy

#### Wines (dswg_wine)
- Wine name and description
- Relationship to Producer
- Vintage year
- Varietal/blend information
- Alcohol percentage
- Wine Type taxonomy
- Bottle image (featured image)
- Wine logo image
- Tech sheets and marketing materials (PDF uploads)

### Taxonomies

#### Countries (dswg_country)
- Hierarchical taxonomy
- Applies to Producers
- Default countries: France, Italy, Spain, Austria, Slovenia

#### Regions (dswg_region)
- Non-hierarchical (tag-like) taxonomy
- Applies to Producers
- For wine regions/appellations

#### Wine Types (dswg_wine_type)
- Hierarchical taxonomy
- Applies to Wines
- Default types: Red, White, Rosé, Sparkling, Champagne, Dessert, Fortified

## Installation

1. Upload the `ds-wineguy` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to "Wine Producers" in the WordPress admin menu

## File Structure

```
ds-wineguy/
├── ds-wineguy.php          # Main plugin file
├── includes/
│   ├── post-types.php      # CPT registration
│   ├── taxonomies.php      # Taxonomy registration
│   ├── meta-boxes.php      # Custom fields (native WP)
│   ├── template-functions.php  # Frontend helper functions
│   └── search-filter.php   # Search/filter functionality
├── admin/
│   └── settings.php        # Admin settings page
├── assets/
│   ├── css/
│   │   ├── admin.css       # Admin styling
│   │   └── wineguy.css     # Frontend styling
│   ├── js/
│   │   ├── admin.js        # Admin JavaScript (media uploads)
│   │   └── wineguy.js      # Frontend JavaScript
│   └── images/
└── templates/              # Optional template overrides
```

## Usage

### Adding a Producer

1. Go to "Wine Producers" > "Add New"
2. Enter the producer name as the title
3. Add the bio/description in the main editor
4. Set a featured image
5. Fill in Producer Details:
   - Region
   - Website URL
6. Add Contact & Social Media information
7. Add Location coordinates (optional, for future map)
8. Add gallery images
9. Assign Country taxonomy
10. Publish

### Adding a Wine

1. Go to "Wine Producers" > "Wines" > "Add New"
2. Enter the wine name as the title
3. Add description/tasting notes in the main editor
4. **Select the Producer** (required)
5. Fill in Wine Details:
   - Vintage
   - Varietal/Blend
   - Alcohol %
6. Set featured image (bottle image)
7. Upload wine logo (optional)
8. Upload tech sheets/marketing materials (optional)
9. Assign Wine Type taxonomy
10. Publish

## Template Functions

Use these functions in your theme templates:

### Producer Functions

```php
// Get wines from a producer
$wines = dswg_get_producer_wines($producer_id);

// Display producer contact info
dswg_display_producer_contact($producer_id);

// Display producer social media
dswg_display_producer_social($producer_id);

// Display producer gallery
dswg_display_producer_gallery($producer_id);
```

### Wine Functions

```php
// Get wine's producer
$producer = dswg_get_wine_producer($wine_id);

// Display wine details
dswg_display_wine_details($wine_id);

// Display wine files/downloads
dswg_display_wine_files($wine_id);
```

## Admin Columns

### Producer List
- Title
- Country
- Wine Count
- Date

### Wine List
- Title
- Producer (linked)
- Vintage
- Wine Type
- Date

## Data Structure

### Producer Meta Fields
- `dswg_region` - Region/appellation
- `dswg_website` - Website URL
- `dswg_contact_email` - Contact email
- `dswg_contact_phone` - Contact phone
- `dswg_instagram` - Instagram URL
- `dswg_facebook` - Facebook URL
- `dswg_twitter` - Twitter/X URL
- `dswg_latitude` - GPS latitude
- `dswg_longitude` - GPS longitude
- `dswg_gallery_ids` - Comma-separated image IDs

### Wine Meta Fields
- `dswg_producer_id` - Related producer post ID (required)
- `dswg_vintage` - Vintage year
- `dswg_varietal` - Grape varieties
- `dswg_alcohol` - Alcohol percentage
- `dswg_wine_logo` - Logo image ID
- `dswg_wine_files` - Comma-separated file attachment IDs

## Custom Image Sizes

- `dswg-producer-thumb` - 400x400 (cropped)
- `dswg-producer-large` - 800x800 (not cropped)
- `dswg-bottle-thumb` - 300x450 (cropped)
- `dswg-bottle-large` - 600x900 (not cropped)
- `dswg-logo-thumb` - 200x200 (cropped)

## REST API Support

Both Producers and Wines are available through the WordPress REST API:
- `/wp-json/wp/v2/producers`
- `/wp-json/wp/v2/wines`

## Planned Features (Future Versions)

- Interactive map of producers using lat/long coordinates
- Advanced search and filtering (AJAX)
- Instagram feed integration per producer
- Newsletter signup integration
- Import/export functionality for bulk data
- Custom archive templates
- Single producer/wine templates

## Development Notes

- Uses native WordPress functionality (no ACF or complex dependencies)
- Follows WordPress coding standards
- Namespace prefix: `dswg_` (DandySite Wine Guy)
- All strings are translatable
- Security: nonces, capability checks, sanitization
- Performance: efficient queries, minimal database calls

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher

## Author

Nancy Dorsner - DandySite
https://dabbledstudios.com

## Changelog

### 1.0.0 (2026-01-XX)
- Initial release
- Producer and Wine CPTs
- Country, Region, and Wine Type taxonomies
- Native meta boxes for all custom fields
- Image gallery support
- File upload support
- Template helper functions
- Admin UI enhancements

## Support

For support, contact nancy@dabbledstudios.com

## License

Proprietary - Integrity Wines Project
