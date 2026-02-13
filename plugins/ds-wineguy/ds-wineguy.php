<?php
/**
 * Plugin Name: DS Wine Guy
 * Plugin URI: https://dabbledstudios.com
 * Description: Wine distributor functionality for Integrity Wines - Producers, Wines, Countries, and more
 * Version: 1.0.0
 * Author: Nancy Dorsner
 * Author URI: https://dabbledstudios.com
 * Text Domain: ds-wineguy
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('DSWG_VERSION', '1.0.0');
define('DSWG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DSWG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DSWG_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class DS_WineGuy {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->includes();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Load text domain
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core functionality
        require_once DSWG_PLUGIN_DIR . 'includes/post-types.php';
        require_once DSWG_PLUGIN_DIR . 'includes/taxonomies.php';
        require_once DSWG_PLUGIN_DIR . 'includes/meta-boxes.php';
        
        // Admin functionality
        if (is_admin()) {
            require_once DSWG_PLUGIN_DIR . 'admin/settings.php';
            require_once DSWG_PLUGIN_DIR . 'admin/importer.php';
        }
        
        // Frontend functionality
        require_once DSWG_PLUGIN_DIR . 'includes/template-functions.php';
        require_once DSWG_PLUGIN_DIR . 'includes/search-filter.php';
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Register post types and taxonomies
        require_once DSWG_PLUGIN_DIR . 'includes/post-types.php';
        require_once DSWG_PLUGIN_DIR . 'includes/taxonomies.php';
        
        dswg_register_post_types();
        dswg_register_taxonomies();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'ds-wineguy',
            false,
            dirname(DSWG_PLUGIN_BASENAME) . '/languages'
        );
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        // Frontend CSS
        wp_enqueue_style(
            'dswg-style',
            DSWG_PLUGIN_URL . 'assets/css/wineguy.css',
            [],
            DSWG_VERSION
        );
        
        // Frontend JS
        wp_enqueue_script(
            'dswg-script',
            DSWG_PLUGIN_URL . 'assets/js/wineguy.js',
            [],
            DSWG_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('dswg-script', 'dswgData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dswg_nonce')
        ]);
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our CPT edit screens
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->post_type, ['dswg_producer', 'dswg_wine'])) {
            return;
        }
        
        // Admin CSS
        wp_enqueue_style(
            'dswg-admin-style',
            DSWG_PLUGIN_URL . 'assets/css/admin.css',
            [],
            DSWG_VERSION
        );
        
        // Admin JS
        wp_enqueue_script(
            'dswg-admin-script',
            DSWG_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            DSWG_VERSION,
            true
        );
    }
}

/**
 * Initialize the plugin
 */
function dswg_init() {
    return DS_WineGuy::instance();
}

// Start the plugin
dswg_init();
