<?php
/**
 * Footer Settings
 * Registers footer layout and color options in the theme settings panel.
 * dandysite-jane theme
 */

if (!defined('ABSPATH')) {
    exit;
}


// =====================================================================
// REGISTER SETTINGS
// =====================================================================

function dsp_register_footer_settings() {

    // Section
    add_settings_section(
        'dsp_footer_section',
        __('Footer', 'dandysite-jane'),
        '__return_false',
        'dsp-theme-settings'
    );

    // Layout
    register_setting('dsp_theme_settings', 'dsp_footer_layout', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'left',
    ]);
    add_settings_field(
        'dsp_footer_layout',
        __('Footer Layout', 'dandysite-jane'),
        'dsp_footer_layout_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );

    // Dark mode
    register_setting('dsp_theme_settings', 'dsp_footer_dark', [
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'light',
    ]);
    add_settings_field(
        'dsp_footer_dark',
        __('Footer Color', 'dandysite-jane'),
        'dsp_footer_dark_field',
        'dsp-theme-settings',
        'dsp_footer_section'
    );
}
add_action('admin_init', 'dsp_register_footer_settings');


// =====================================================================
// FIELD CALLBACKS
// =====================================================================

function dsp_footer_layout_field() {
    $value = get_option('dsp_footer_layout', 'left');
    $options = [
        'left'    => __('Left justified (content-width columns)', 'dandysite-jane'),
        'center'  => __('Centered (columns grouped in center)', 'dandysite-jane'),
        'spaced'  => __('Spaced even (columns spread full width)', 'dandysite-jane'),
    ];
    foreach ($options as $key => $label) {
        printf(
            '<label style="display:block;margin-bottom:6px"><input type="radio" name="dsp_footer_layout" value="%s"%s> %s</label>',
            esc_attr($key),
            checked($value, $key, false),
            esc_html($label)
        );
    }
    echo '<p class="description">' . esc_html__('Controls how widget columns are distributed across the footer.', 'dandysite-jane') . '</p>';
}

function dsp_footer_dark_field() {
    $value = get_option('dsp_footer_dark', 'light');
    $options = [
        'light' => __('Light (matches site background)', 'dandysite-jane'),
        'dark'  => __('Dark (dark background, light text)', 'dandysite-jane'),
    ];
    foreach ($options as $key => $label) {
        printf(
            '<label style="display:block;margin-bottom:6px"><input type="radio" name="dsp_footer_dark" value="%s"%s> %s</label>',
            esc_attr($key),
            checked($value, $key, false),
            esc_html($label)
        );
    }
}


// =====================================================================
// BODY CLASS
// =====================================================================

function dsp_footer_body_classes($classes) {
    $layout = get_option('dsp_footer_layout', 'left');
    $color  = get_option('dsp_footer_dark', 'light');

    $classes[] = 'footer-layout-' . $layout;
    if ($color === 'dark') {
        $classes[] = 'footer-dark';
    }

    return $classes;
}
add_filter('body_class', 'dsp_footer_body_classes');
