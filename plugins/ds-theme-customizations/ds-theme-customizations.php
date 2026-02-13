<?php
/**
 * Plugin Name: DS Theme Customizations - Integrity Wines
 * Description: Site-specific branding, styling, and customizations for Integrity Wines
 * Version: 1.0.0
 * Author: Nancy Dorsner - Dabbled Studios
 * Author URI: https://dabbledstudios.com/
 * Text Domain: ds-theme-custom
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Theme Customizations Class
 */
final class DS_Theme_Customizations {

    /**
     * Plugin version
     */
    const VERSION = '1.0.0';

    /**
     * Plugin directory path
     */
    private $plugin_path;

    /**
     * Plugin directory URL
     */
    private $plugin_url;

    /**
     * Set up the plugin
     */
    public function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        
        add_action('init', [$this, 'init'], -1);
        
        // Load custom functions if file exists
        $functions_file = $this->plugin_path . 'includes/functions.php';
        if (file_exists($functions_file)) {
            require_once $functions_file;
        }
    }
  
    /**
     * Initialize plugin
     */
    public function init() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles'], 999);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('template_include', [$this, 'load_custom_templates'], 11);
    }

    /**
     * Enqueue custom styles
     */
    public function enqueue_styles() {
        // Main plugin styles - compiled from LESS
        $css_file = $this->plugin_path . 'assets/plugin-style.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'ds-custom-styles',
                $this->plugin_url . 'assets/plugin-style.css',
                ['dsp-style'], // Load after theme styles
                filemtime($css_file)
            );
        }
    }

    /**
     * Enqueue custom scripts
     */
    public function enqueue_scripts() {
        // Main custom JS
        $js_file = $this->plugin_path . 'assets/custom.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'ds-custom-js',
                $this->plugin_url . 'assets/custom.js',
                ['jquery'],
                filemtime($js_file),
                true
            );
        }
    }

    /**
     * Load custom templates from plugin
     */
    public function load_custom_templates($template) {
        $custom_template = $this->plugin_path . 'templates/' . basename($template);
        
        if (file_exists($custom_template)) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Get plugin version
     */
    public function get_version() {
        return self::VERSION;
    }

    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return $this->plugin_path;
    }

    /**
     * Get plugin URL
     */
    public function get_plugin_url() {
        return $this->plugin_url;
    }
}

/**
 * Initialize the plugin
 */
function ds_theme_customizations() {
    return new DS_Theme_Customizations();
}

// Start the plugin
add_action('plugins_loaded', 'ds_theme_customizations');

/**
 * Global access function
 */
function get_ds_customizations() {
    return ds_theme_customizations();
}
