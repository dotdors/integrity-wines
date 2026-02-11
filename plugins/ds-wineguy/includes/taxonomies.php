<?php
/**
 * Register Taxonomies
 * 
 * Registers Country, Region, and Wine Type taxonomies
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all taxonomies
 */
function dswg_register_taxonomies() {
    dswg_register_country_taxonomy();
    dswg_register_region_taxonomy();
    dswg_register_wine_type_taxonomy();
}
add_action('init', 'dswg_register_taxonomies');

/**
 * Register Country taxonomy (hierarchical)
 */
function dswg_register_country_taxonomy() {
    $labels = [
        'name'                       => _x('Countries', 'Taxonomy General Name', 'ds-wineguy'),
        'singular_name'              => _x('Country', 'Taxonomy Singular Name', 'ds-wineguy'),
        'menu_name'                  => __('Countries', 'ds-wineguy'),
        'all_items'                  => __('All Countries', 'ds-wineguy'),
        'parent_item'                => __('Parent Country', 'ds-wineguy'),
        'parent_item_colon'          => __('Parent Country:', 'ds-wineguy'),
        'new_item_name'              => __('New Country Name', 'ds-wineguy'),
        'add_new_item'               => __('Add New Country', 'ds-wineguy'),
        'edit_item'                  => __('Edit Country', 'ds-wineguy'),
        'update_item'                => __('Update Country', 'ds-wineguy'),
        'view_item'                  => __('View Country', 'ds-wineguy'),
        'separate_items_with_commas' => __('Separate countries with commas', 'ds-wineguy'),
        'add_or_remove_items'        => __('Add or remove countries', 'ds-wineguy'),
        'choose_from_most_used'      => __('Choose from the most used', 'ds-wineguy'),
        'popular_items'              => __('Popular Countries', 'ds-wineguy'),
        'search_items'               => __('Search Countries', 'ds-wineguy'),
        'not_found'                  => __('Not Found', 'ds-wineguy'),
        'no_terms'                   => __('No countries', 'ds-wineguy'),
        'items_list'                 => __('Countries list', 'ds-wineguy'),
        'items_list_navigation'      => __('Countries list navigation', 'ds-wineguy'),
    ];
    
    $args = [
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
        'rewrite'                    => [
            'slug' => 'country',
            'with_front' => false,
            'hierarchical' => true,
        ],
    ];
    
    register_taxonomy('dswg_country', ['dswg_producer'], $args);
}

/**
 * Register Region taxonomy (non-hierarchical, like tags)
 */
function dswg_register_region_taxonomy() {
    $labels = [
        'name'                       => _x('Regions', 'Taxonomy General Name', 'ds-wineguy'),
        'singular_name'              => _x('Region', 'Taxonomy Singular Name', 'ds-wineguy'),
        'menu_name'                  => __('Regions', 'ds-wineguy'),
        'all_items'                  => __('All Regions', 'ds-wineguy'),
        'new_item_name'              => __('New Region Name', 'ds-wineguy'),
        'add_new_item'               => __('Add New Region', 'ds-wineguy'),
        'edit_item'                  => __('Edit Region', 'ds-wineguy'),
        'update_item'                => __('Update Region', 'ds-wineguy'),
        'view_item'                  => __('View Region', 'ds-wineguy'),
        'separate_items_with_commas' => __('Separate regions with commas', 'ds-wineguy'),
        'add_or_remove_items'        => __('Add or remove regions', 'ds-wineguy'),
        'choose_from_most_used'      => __('Choose from the most used', 'ds-wineguy'),
        'popular_items'              => __('Popular Regions', 'ds-wineguy'),
        'search_items'               => __('Search Regions', 'ds-wineguy'),
        'not_found'                  => __('Not Found', 'ds-wineguy'),
        'no_terms'                   => __('No regions', 'ds-wineguy'),
        'items_list'                 => __('Regions list', 'ds-wineguy'),
        'items_list_navigation'      => __('Regions list navigation', 'ds-wineguy'),
    ];
    
    $args = [
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
        'rewrite'                    => [
            'slug' => 'region',
            'with_front' => false,
        ],
    ];
    
    register_taxonomy('dswg_region', ['dswg_producer'], $args);
}

/**
 * Register Wine Type taxonomy (hierarchical)
 */
function dswg_register_wine_type_taxonomy() {
    $labels = [
        'name'                       => _x('Wine Types', 'Taxonomy General Name', 'ds-wineguy'),
        'singular_name'              => _x('Wine Type', 'Taxonomy Singular Name', 'ds-wineguy'),
        'menu_name'                  => __('Wine Types', 'ds-wineguy'),
        'all_items'                  => __('All Wine Types', 'ds-wineguy'),
        'parent_item'                => __('Parent Type', 'ds-wineguy'),
        'parent_item_colon'          => __('Parent Type:', 'ds-wineguy'),
        'new_item_name'              => __('New Wine Type Name', 'ds-wineguy'),
        'add_new_item'               => __('Add New Wine Type', 'ds-wineguy'),
        'edit_item'                  => __('Edit Wine Type', 'ds-wineguy'),
        'update_item'                => __('Update Wine Type', 'ds-wineguy'),
        'view_item'                  => __('View Wine Type', 'ds-wineguy'),
        'separate_items_with_commas' => __('Separate types with commas', 'ds-wineguy'),
        'add_or_remove_items'        => __('Add or remove types', 'ds-wineguy'),
        'choose_from_most_used'      => __('Choose from the most used', 'ds-wineguy'),
        'popular_items'              => __('Popular Wine Types', 'ds-wineguy'),
        'search_items'               => __('Search Wine Types', 'ds-wineguy'),
        'not_found'                  => __('Not Found', 'ds-wineguy'),
        'no_terms'                   => __('No wine types', 'ds-wineguy'),
        'items_list'                 => __('Wine types list', 'ds-wineguy'),
        'items_list_navigation'      => __('Wine types list navigation', 'ds-wineguy'),
    ];
    
    $args = [
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
        'rewrite'                    => [
            'slug' => 'wine-type',
            'with_front' => false,
            'hierarchical' => true,
        ],
    ];
    
    register_taxonomy('dswg_wine_type', ['dswg_wine'], $args);
}

/**
 * Add default countries on plugin activation
 */
function dswg_add_default_countries() {
    // Only add if they don't exist
    $countries = [
        'France' => 'French Republic (France)',
        'Italy' => 'Italy',
        'Spain' => 'Spain',
        'Austria' => 'Austria',
        'Slovenia' => 'Slovenia',
    ];
    
    foreach ($countries as $slug => $name) {
        if (!term_exists($slug, 'dswg_country')) {
            wp_insert_term($name, 'dswg_country', [
                'slug' => strtolower($slug),
            ]);
        }
    }
}
register_activation_hook(DSWG_PLUGIN_DIR . 'ds-wineguy.php', 'dswg_add_default_countries');

/**
 * Add default wine types on plugin activation
 */
function dswg_add_default_wine_types() {
    $wine_types = [
        'Red Wine',
        'White Wine',
        'Rosé',
        'Sparkling Wine',
        'Champagne',
        'Dessert Wine',
        'Fortified Wine',
    ];
    
    foreach ($wine_types as $type) {
        if (!term_exists($type, 'dswg_wine_type')) {
            wp_insert_term($type, 'dswg_wine_type');
        }
    }
}
register_activation_hook(DSWG_PLUGIN_DIR . 'ds-wineguy.php', 'dswg_add_default_wine_types');
