<?php
/**
 * Register Custom Post Types
 * 
 * Registers Producers and Wines CPTs
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all custom post types
 */
function dswg_register_post_types() {
    dswg_register_producer_cpt();
    dswg_register_wine_cpt();
}
add_action('init', 'dswg_register_post_types');

/**
 * Register Producer CPT
 */
function dswg_register_producer_cpt() {
    $labels = [
        'name'                  => _x('Producers', 'Post Type General Name', 'ds-wineguy'),
        'singular_name'         => _x('Producer', 'Post Type Singular Name', 'ds-wineguy'),
        'menu_name'             => __('Wine Producers', 'ds-wineguy'),
        'name_admin_bar'        => __('Producer', 'ds-wineguy'),
        'archives'              => __('Producer Archives', 'ds-wineguy'),
        'attributes'            => __('Producer Attributes', 'ds-wineguy'),
        'parent_item_colon'     => __('Parent Producer:', 'ds-wineguy'),
        'all_items'             => __('All Producers', 'ds-wineguy'),
        'add_new_item'          => __('Add New Producer', 'ds-wineguy'),
        'add_new'               => __('Add New', 'ds-wineguy'),
        'new_item'              => __('New Producer', 'ds-wineguy'),
        'edit_item'             => __('Edit Producer', 'ds-wineguy'),
        'update_item'           => __('Update Producer', 'ds-wineguy'),
        'view_item'             => __('View Producer', 'ds-wineguy'),
        'view_items'            => __('View Producers', 'ds-wineguy'),
        'search_items'          => __('Search Producers', 'ds-wineguy'),
        'not_found'             => __('No producers found', 'ds-wineguy'),
        'not_found_in_trash'    => __('No producers found in Trash', 'ds-wineguy'),
        'featured_image'        => __('Producer Image', 'ds-wineguy'),
        'set_featured_image'    => __('Set producer image', 'ds-wineguy'),
        'remove_featured_image' => __('Remove producer image', 'ds-wineguy'),
        'use_featured_image'    => __('Use as producer image', 'ds-wineguy'),
        'insert_into_item'      => __('Insert into producer', 'ds-wineguy'),
        'uploaded_to_this_item' => __('Uploaded to this producer', 'ds-wineguy'),
        'items_list'            => __('Producers list', 'ds-wineguy'),
        'items_list_navigation' => __('Producers list navigation', 'ds-wineguy'),
        'filter_items_list'     => __('Filter producers list', 'ds-wineguy'),
    ];
    
    $args = [
        'label'                 => __('Producer', 'ds-wineguy'),
        'description'           => __('Wine producers and wineries', 'ds-wineguy'),
        'labels'                => $labels,
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'taxonomies'            => ['dswg_country', 'dswg_region'],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-admin-home',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'rewrite'               => [
            'slug' => 'producers',
            'with_front' => false,
        ],
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rest_base'             => 'producers',
    ];
    
    register_post_type('dswg_producer', $args);
}

/**
 * Register Wine CPT
 */
function dswg_register_wine_cpt() {
    $labels = [
        'name'                  => _x('Wines', 'Post Type General Name', 'ds-wineguy'),
        'singular_name'         => _x('Wine', 'Post Type Singular Name', 'ds-wineguy'),
        'menu_name'             => __('Wines', 'ds-wineguy'),
        'name_admin_bar'        => __('Wine', 'ds-wineguy'),
        'archives'              => __('Wine Archives', 'ds-wineguy'),
        'attributes'            => __('Wine Attributes', 'ds-wineguy'),
        'parent_item_colon'     => __('Parent Wine:', 'ds-wineguy'),
        'all_items'             => __('All Wines', 'ds-wineguy'),
        'add_new_item'          => __('Add New Wine', 'ds-wineguy'),
        'add_new'               => __('Add New', 'ds-wineguy'),
        'new_item'              => __('New Wine', 'ds-wineguy'),
        'edit_item'             => __('Edit Wine', 'ds-wineguy'),
        'update_item'           => __('Update Wine', 'ds-wineguy'),
        'view_item'             => __('View Wine', 'ds-wineguy'),
        'view_items'            => __('View Wines', 'ds-wineguy'),
        'search_items'          => __('Search Wines', 'ds-wineguy'),
        'not_found'             => __('No wines found', 'ds-wineguy'),
        'not_found_in_trash'    => __('No wines found in Trash', 'ds-wineguy'),
        'featured_image'        => __('Bottle Image', 'ds-wineguy'),
        'set_featured_image'    => __('Set bottle image', 'ds-wineguy'),
        'remove_featured_image' => __('Remove bottle image', 'ds-wineguy'),
        'use_featured_image'    => __('Use as bottle image', 'ds-wineguy'),
        'insert_into_item'      => __('Insert into wine', 'ds-wineguy'),
        'uploaded_to_this_item' => __('Uploaded to this wine', 'ds-wineguy'),
        'items_list'            => __('Wines list', 'ds-wineguy'),
        'items_list_navigation' => __('Wines list navigation', 'ds-wineguy'),
        'filter_items_list'     => __('Filter wines list', 'ds-wineguy'),
    ];
    
    $args = [
        'label'                 => __('Wine', 'ds-wineguy'),
        'description'           => __('Individual wines from producers', 'ds-wineguy'),
        'labels'                => $labels,
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'taxonomies'            => ['dswg_wine_type'],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'edit.php?post_type=dswg_producer',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'rewrite'               => [
            'slug' => 'wines',
            'with_front' => false,
        ],
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rest_base'             => 'wines',
    ];
    
    register_post_type('dswg_wine', $args);
}

/**
 * Add custom image sizes for producers and wines
 */
function dswg_add_image_sizes() {
    // Producer images
    add_image_size('dswg-producer-thumb', 400, 400, true);
    add_image_size('dswg-producer-large', 800, 800, false);
    
    // Wine bottle images
    add_image_size('dswg-bottle-thumb', 300, 450, true);
    add_image_size('dswg-bottle-large', 600, 900, false);
    
    // Wine logo images
    add_image_size('dswg-logo-thumb', 200, 200, true);
}
add_action('after_setup_theme', 'dswg_add_image_sizes');

/**
 * Add custom columns to producer list
 */
function dswg_producer_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['country'] = __('Country', 'ds-wineguy');
    $new_columns['wine_count'] = __('Wines', 'ds-wineguy');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_dswg_producer_posts_columns', 'dswg_producer_columns');

/**
 * Populate custom columns for producer list
 */
function dswg_producer_column_content($column, $post_id) {
    switch ($column) {
        case 'country':
            $terms = get_the_terms($post_id, 'dswg_country');
            if ($terms && !is_wp_error($terms)) {
                $countries = wp_list_pluck($terms, 'name');
                echo esc_html(implode(', ', $countries));
            } else {
                echo '—';
            }
            break;
            
        case 'wine_count':
            $wines = get_posts([
                'post_type' => 'dswg_wine',
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'key' => 'dswg_producer_id',
                        'value' => $post_id,
                    ]
                ],
                'fields' => 'ids',
            ]);
            echo count($wines);
            break;
    }
}
add_action('manage_dswg_producer_posts_custom_column', 'dswg_producer_column_content', 10, 2);

/**
 * Add custom columns to wine list
 */
function dswg_wine_columns($columns) {
    $new_columns = [];
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['producer'] = __('Producer', 'ds-wineguy');
    $new_columns['vintage'] = __('Vintage', 'ds-wineguy');
    $new_columns['wine_type'] = __('Type', 'ds-wineguy');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_dswg_wine_posts_columns', 'dswg_wine_columns');

/**
 * Populate custom columns for wine list
 */
function dswg_wine_column_content($column, $post_id) {
    switch ($column) {
        case 'producer':
            $producer_id = get_post_meta($post_id, 'dswg_producer_id', true);
            if ($producer_id) {
                $producer = get_post($producer_id);
                if ($producer) {
                    echo '<a href="' . esc_url(get_edit_post_link($producer_id)) . '">';
                    echo esc_html($producer->post_title);
                    echo '</a>';
                }
            } else {
                echo '—';
            }
            break;
            
        case 'vintage':
            $vintage = get_post_meta($post_id, 'dswg_vintage', true);
            echo $vintage ? esc_html($vintage) : '—';
            break;
            
        case 'wine_type':
            $terms = get_the_terms($post_id, 'dswg_wine_type');
            if ($terms && !is_wp_error($terms)) {
                $types = wp_list_pluck($terms, 'name');
                echo esc_html(implode(', ', $types));
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_dswg_wine_posts_custom_column', 'dswg_wine_column_content', 10, 2);

/**
 * Make custom columns sortable
 */
function dswg_sortable_columns($columns) {
    $columns['vintage'] = 'vintage';
    return $columns;
}
add_filter('manage_edit-dswg_wine_sortable_columns', 'dswg_sortable_columns');
