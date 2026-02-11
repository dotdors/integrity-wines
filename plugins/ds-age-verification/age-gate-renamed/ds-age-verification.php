<?php
/*
Plugin Name: DS Age Verification & Cookie Gate
Plugin URI: https://dabbledstudios.com
Description: Age verification (21+) with cookie preference for Integrity Wines. Clean, secure, single-popup approach.
Version: 2.1.0
Author: Nancy Dorsner
Author URI: https://dabbledstudios.com
Text Domain: ds-age-verification
*/

if (!defined('ABSPATH')) exit;

// Admin settings
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Enqueue scripts/styles
add_action('wp_enqueue_scripts', 'ds_age_gate_enqueue_assets');
function ds_age_gate_enqueue_assets() {
    // Don't show for logged-in users
    if (is_user_logged_in()) return;

    $options = get_option('ds_age_gate_settings', []);
    $cookie_page = intval($options['cookie_policy_page'] ?? 0);

    // Don't show on the cookie policy page itself
    if ($cookie_page && is_page($cookie_page)) return;

    wp_enqueue_style(
        'ds-age-gate-css',
        plugin_dir_url(__FILE__) . 'assets/css/age-gate.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/age-gate.css')
    );

    wp_enqueue_script(
        'ds-age-gate-js',
        plugin_dir_url(__FILE__) . 'assets/js/age-gate.js',
        [], // No jQuery dependency
        filemtime(plugin_dir_path(__FILE__) . 'assets/js/age-gate.js'),
        true
    );

    wp_localize_script('ds-age-gate-js', 'AgeGateData', [
        'cookieDays' => intval($options['cookie_duration'] ?? 30),
        'declineUrl' => esc_url($options['decline_url'] ?? 'https://www.responsibility.org/'),
    ]);
}

// Front-end popup HTML
add_action('wp_footer', 'ds_age_gate_render_popup');
function ds_age_gate_render_popup() {
    // Don't show for logged-in users
    if (is_user_logged_in()) return;

    $options = get_option('ds_age_gate_settings', []);
    $cookie_page = intval($options['cookie_policy_page'] ?? 0);

    // Don't show on the cookie policy page itself
    if ($cookie_page && is_page($cookie_page)) return;

    $title = $options['title'] ?? 'Age Verification';
    $subtitle = $options['subtitle'] ?? 'You must be 21 or older to enter this site.';
    $cookie_link = $cookie_page ? get_permalink($cookie_page) : '';
    $days = intval($options['cookie_duration'] ?? 30);
    ?>
    <div id="age-gate-overlay" role="dialog" aria-modal="true" aria-labelledby="age-gate-title">
        <div id="age-gate-popup">
            <h2 id="age-gate-title"><?php echo esc_html($title); ?></h2>
            <p class="subtitle"><?php echo esc_html($subtitle); ?></p>

            <div class="age-buttons">
                <button id="age-gate-enter" class="primary">I'm 21 or Older</button>
                <button id="age-gate-decline" class="secondary">I'm Under 21</button>
            </div>

            <div id="cookie-preferences" style="display: none;">
                <p class="cookie-info">
                    This site uses essential cookies for security and to remember your age verification.
                    How long would you like us to remember you?
                </p>

                <div class="cookie-options">
                    <label>
                        <input type="radio" name="cookie-pref" value="persistent" checked>
                        <span>Remember me for <strong><?php echo $days; ?> days</strong></span>
                    </label>
                    <label>
                        <input type="radio" name="cookie-pref" value="session">
                        <span>This session only</span>
                    </label>
                </div>

                <?php if ($cookie_link): ?>
                    <p class="cookie-link">
                        <a href="<?php echo esc_url($cookie_link); ?>" target="_blank" rel="noopener">Learn more about our cookie policy</a>
                    </p>
                <?php endif; ?>

                <button id="age-gate-confirm" class="primary">Enter Site</button>
            </div>

            <div id="age-gate-declined" style="display: none;">
                <p class="declined-message">You must be 21 or older to access this site.</p>
                <p>You will be redirected shortly...</p>
            </div>
        </div>
    </div>
    <?php
}
