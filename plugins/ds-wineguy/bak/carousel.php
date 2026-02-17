<?php
/**
 * Producer Carousel Functionality
 * 
 * File: includes/carousel.php
 * Handles producer carousel display, shortcodes, and asset loading
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Producer Carousel Assets
 * 
 * Conditionally loads Swiper.js and carousel scripts
 * Theme can override styles by enqueueing after this with higher priority
 */
function ds_wineguy_enqueue_carousel_assets() {
    // Define pages/templates where carousel should load
    $load_carousel = apply_filters('ds_wineguy_load_carousel', false);
    
    // Default loading conditions
    if (!$load_carousel) {
        // Load on homepage
        if (is_front_page() || is_home()) {
            $load_carousel = true;
        }
        
        // Load on pages with 'country' in slug or template
        if (is_page() && (strpos(get_post_field('post_name'), 'country') !== false || 
            is_page_template('template-country.php'))) {
            $load_carousel = true;
        }
        
        // Load on producer archive
        if (is_post_type_archive('dswg_producer')) {
            $load_carousel = true;
        }
    }
    
    // Allow themes to force loading
    $load_carousel = apply_filters('ds_wineguy_force_load_carousel', $load_carousel);
    
    if (!$load_carousel) {
        return;
    }
    
    $plugin_url = plugin_dir_url(__FILE__);
    $plugin_version = '1.1.0'; // Match your plugin version
    
    // Swiper CSS
    // OPTION A: Use local files (recommended for production)
    wp_enqueue_style(
        'swiper',
        $plugin_url . 'assets/css/swiper-bundle.min.css',
        array(),
        '12.1.1'
    );
    
    // OPTION B: Use CDN (uncomment to use instead of local files)
    // wp_enqueue_style(
    //     'swiper',
    //     'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css',
    //     array(),
    //     null
    // );
    
    // Producer Carousel CSS
    wp_enqueue_style(
        'ds-wineguy-carousel',
        $plugin_url . 'assets/css/producer-carousel.css',
        array('swiper'),
        $plugin_version
    );
    
    // Swiper JS
    // OPTION A: Use local files (recommended for production)
    wp_enqueue_script(
        'swiper',
        $plugin_url . 'assets/js/swiper-bundle.min.js',
        array(),
        '12.1.1',
        true
    );
    
    // OPTION B: Use CDN (uncomment to use instead of local files)
    // wp_enqueue_script(
    //     'swiper',
    //     'https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js',
    //     array(),
    //     null,
    //     true
    // );
    
    // Producer Carousel JS
    wp_enqueue_script(
        'ds-wineguy-carousel',
        $plugin_url . 'assets/js/producer-carousel.js',
        array('swiper'),
        $plugin_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'ds_wineguy_enqueue_carousel_assets');


/**
 * Register template directory for carousel template
 * Allows theme to override by placing template in theme/ds-wineguy/
 */
function ds_wineguy_template_path() {
    return plugin_dir_path(__FILE__) . 'templates/';
}


/**
 * Get template part (like WooCommerce does)
 * First checks theme, then falls back to plugin
 * 
 * @param string $slug Template slug
 * @param array $args Arguments to pass to template
 */
function ds_wineguy_get_template($slug, $args = array()) {
    // Extract args to variables
    if (!empty($args) && is_array($args)) {
        extract($args);
    }
    
    // Check theme first
    $template = locate_template(array(
        "ds-wineguy/{$slug}.php",
        "ds-wineguy-templates/{$slug}.php",
    ));
    
    // Fall back to plugin
    if (!$template) {
        $template = ds_wineguy_template_path() . "{$slug}.php";
    }
    
    if (file_exists($template)) {
        include $template;
    }
}


/**
 * Display producer carousel
 * 
 * @param array $args {
 *     Optional. Array of arguments.
 *     
 *     @type string $country        Country slug to filter producers. Default empty (all).
 *     @type bool   $autoplay       Enable automatic scrolling. Default false.
 *     @type int    $autoplay_delay Delay between slides in milliseconds. Default 5000.
 *     @type bool   $randomize      Randomize producer order. Default true.
 * }
 */
function ds_wineguy_producer_carousel($args = array()) {
    $defaults = array(
        'country' => '',
        'autoplay' => false,
        'autoplay_delay' => 5000,
        'randomize' => true
    );
    
    $args = wp_parse_args($args, $defaults);
    
    // Allow filtering of args
    $args = apply_filters('ds_wineguy_carousel_args', $args);
    
    ds_wineguy_get_template('producer-carousel', $args);
}


/**
 * Shortcode for producer carousel
 * Usage: [producer_carousel country="spain" autoplay="true" autoplay_delay="6000"]
 */
function ds_wineguy_carousel_shortcode($atts) {
    $atts = shortcode_atts(array(
        'country' => '',
        'autoplay' => 'false',
        'autoplay_delay' => '5000',
        'randomize' => 'true'
    ), $atts);
    
    $args = array(
        'country' => sanitize_text_field($atts['country']),
        'autoplay' => filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN),
        'autoplay_delay' => intval($atts['autoplay_delay']),
        'randomize' => filter_var($atts['randomize'], FILTER_VALIDATE_BOOLEAN)
    );
    
    ob_start();
    ds_wineguy_producer_carousel($args);
    return ob_get_clean();
}
add_shortcode('producer_carousel', 'ds_wineguy_carousel_shortcode');


/**
 * Helper: Get current country from page context
 * Checks custom field, then page slug
 * 
 * @return string Country slug or empty string
 */
function ds_wineguy_get_current_country() {
    // Check custom field first
    $country = get_post_meta(get_the_ID(), 'country_slug', true);
    
    if ($country) {
        return $country;
    }
    
    // Check page slug
    $page_slug = get_post_field('post_name', get_the_ID());
    $valid_countries = array('austria', 'france', 'italy', 'slovenia', 'spain');
    
    if (in_array($page_slug, $valid_countries)) {
        return $page_slug;
    }
    
    // Check if slug contains country name
    foreach ($valid_countries as $country) {
        if (strpos($page_slug, $country) !== false) {
            return $country;
        }
    }
    
    return '';
}


/**
 * USAGE EXAMPLES FOR THEMES
 * 
 * In your theme templates, use:
 * 
 * // Homepage - all producers with autoplay
 * ds_wineguy_producer_carousel(array(
 *     'autoplay' => true,
 *     'autoplay_delay' => 6000,
 *     'randomize' => true
 * ));
 * 
 * // Country page - Spain only
 * ds_wineguy_producer_carousel(array(
 *     'country' => 'spain',
 *     'autoplay' => false,
 *     'randomize' => false
 * ));
 * 
 * // Auto-detect country
 * ds_wineguy_producer_carousel(array(
 *     'country' => ds_wineguy_get_current_country(),
 *     'autoplay' => true
 * ));
 * 
 * // Or use shortcode in page content/widgets:
 * [producer_carousel autoplay="true" autoplay_delay="6000"]
 * [producer_carousel country="france" randomize="false"]
 */


/**
 * THEME CUSTOMIZATION HOOKS
 * 
 * Themes can customize carousel behavior:
 * 
 * // Force carousel to load on specific pages
 * add_filter('ds_wineguy_force_load_carousel', function($load) {
 *     if (is_page('our-wines')) {
 *         return true;
 *     }
 *     return $load;
 * });
 * 
 * // Modify carousel arguments
 * add_filter('ds_wineguy_carousel_args', function($args) {
 *     if (is_front_page()) {
 *         $args['autoplay_delay'] = 8000; // Slower on homepage
 *     }
 *     return $args;
 * });
 * 
 * // Override carousel styles in theme
 * add_action('wp_enqueue_scripts', function() {
 *     if (wp_style_is('ds-wineguy-carousel', 'enqueued')) {
 *         wp_enqueue_style(
 *             'theme-carousel-overrides',
 *             get_stylesheet_directory_uri() . '/css/carousel-custom.css',
 *             array('ds-wineguy-carousel'),
 *             '1.0.0'
 *         );
 *     }
 * }, 20);
 */
