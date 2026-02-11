<?php
/**
 * Admin Settings Page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings page to admin menu
 */
function dswg_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=dswg_producer',
        __('Wine Guy Settings', 'ds-wineguy'),
        __('Settings', 'ds-wineguy'),
        'manage_options',
        'dswg-settings',
        'dswg_render_settings_page'
    );
}
add_action('admin_menu', 'dswg_add_settings_page');

/**
 * Render settings page
 */
function dswg_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p><?php _e('Settings for Wine Guy plugin will be added here.', 'ds-wineguy'); ?></p>
        
        <h2><?php _e('Quick Stats', 'ds-wineguy'); ?></h2>
        <ul>
            <li><?php
                $producer_count = wp_count_posts('dswg_producer');
                printf(__('Producers: %d', 'ds-wineguy'), $producer_count->publish);
            ?></li>
            <li><?php
                $wine_count = wp_count_posts('dswg_wine');
                printf(__('Wines: %d', 'ds-wineguy'), $wine_count->publish);
            ?></li>
            <li><?php
                $country_count = wp_count_terms(['taxonomy' => 'dswg_country']);
                printf(__('Countries: %d', 'ds-wineguy'), $country_count);
            ?></li>
        </ul>
    </div>
    <?php
}
