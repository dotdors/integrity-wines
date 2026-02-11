<?php
if (!defined('ABSPATH')) exit;

// Add admin menu
add_action('admin_menu', 'ds_age_gate_add_admin_menu');
function ds_age_gate_add_admin_menu() {
    add_options_page(
        'DS Age Verification Settings',
        'DS Age Verification',
        'manage_options',
        'ds-age-gate-settings',
        'ds_age_gate_settings_page'
    );
}

// Register settings
add_action('admin_init', 'ds_age_gate_register_settings');
function ds_age_gate_register_settings() {
    register_setting('ds_age_gate_settings_group', 'ds_age_gate_settings', 'ds_age_gate_sanitize_settings');
}

// Sanitize and validate settings
function ds_age_gate_sanitize_settings($input) {
    $sanitized = [];
    
    $sanitized['title'] = sanitize_text_field($input['title'] ?? 'Age Verification');
    $sanitized['subtitle'] = sanitize_text_field($input['subtitle'] ?? 'You must be 21 or older to enter this site.');
    $sanitized['cookie_policy_page'] = intval($input['cookie_policy_page'] ?? 0);
    $sanitized['cookie_duration'] = max(1, min(365, intval($input['cookie_duration'] ?? 30))); // Between 1-365 days
    $sanitized['decline_url'] = esc_url_raw($input['decline_url'] ?? 'https://www.responsibility.org/');
    
    return $sanitized;
}

// Helper function for text inputs
function ds_age_gate_text_input($key, $default = '') {
    $options = get_option('ds_age_gate_settings', []);
    $value = esc_attr($options[$key] ?? $default);
    echo "<input type='text' name='ds_age_gate_settings[$key]' value='$value' class='regular-text' />";
}

// Helper function for number inputs
function ds_age_gate_number_input($key, $default = '', $min = null, $max = null) {
    $options = get_option('ds_age_gate_settings', []);
    $value = esc_attr($options[$key] ?? $default);
    $min_attr = $min !== null ? "min='$min'" : '';
    $max_attr = $max !== null ? "max='$max'" : '';
    echo "<input type='number' name='ds_age_gate_settings[$key]' value='$value' class='small-text' $min_attr $max_attr />";
}

// Settings page HTML
function ds_age_gate_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    $options = get_option('ds_age_gate_settings', []);
    ?>
    <div class="wrap">
        <h1>DS Age Verification Settings</h1>
        <p>Configure the age verification popup and cookie preferences.</p>

        <form method="post" action="options.php">
            <?php settings_fields('ds_age_gate_settings_group'); ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="title">Title</label>
                        </th>
                        <td>
                            <?php ds_age_gate_text_input('title', 'Age Verification'); ?>
                            <p class="description">Main heading shown in the popup.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="subtitle">Subtitle</label>
                        </th>
                        <td>
                            <?php ds_age_gate_text_input('subtitle', 'You must be 21 or older to enter this site.'); ?>
                            <p class="description">Message shown below the title.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="cookie_duration">Cookie Duration</label>
                        </th>
                        <td>
                            <?php ds_age_gate_number_input('cookie_duration', 30, 1, 365); ?> days
                            <p class="description">How long the "Remember me" option will last (1-365 days). Default: 30 days.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="cookie_policy_page">Cookie Policy Page</label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_pages([
                                'name' => 'ds_age_gate_settings[cookie_policy_page]',
                                'id' => 'cookie_policy_page',
                                'selected' => intval($options['cookie_policy_page'] ?? 0),
                                'show_option_none' => '-- No Policy Page --',
                                'option_none_value' => 0
                            ]);
                            ?>
                            <p class="description">Optional: Link to your cookie policy page. The age gate will not show on this page.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="decline_url">Decline Redirect URL</label>
                        </th>
                        <td>
                            <?php ds_age_gate_text_input('decline_url', 'https://www.responsibility.org/'); ?>
                            <p class="description">Where to redirect users who indicate they're under 21. Default: Responsibility.org</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h2>Testing</h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Clear Cookie</th>
                        <td>
                            <button type="button" class="button" onclick="document.cookie='age_verified=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC'; alert('Age verification cookie cleared. Refresh your site to test.');">
                                Clear Age Verification Cookie
                            </button>
                            <p class="description">Use this to test the age gate without logging out. You'll need to refresh your site after clicking.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <hr>

        <h2>How It Works</h2>
        <p><strong>Age Verification:</strong> The popup asks visitors to confirm they're 21 or older. This is a best practice for alcohol-related websites.</p>
        
        <p><strong>Cookie Preference:</strong> After confirming their age, visitors choose how long they'd like to be remembered:</p>
        <ul style="list-style: disc; margin-left: 20px;">
            <li><strong>Remember me:</strong> Sets a cookie that lasts for the number of days you configure above.</li>
            <li><strong>This session only:</strong> Sets a session cookie that expires when they close their browser.</li>
        </ul>

        <p><strong>Cookie Type:</strong> The age verification cookie is considered "strictly necessary" for site functionality and doesn't require GDPR consent. We're simply informing users and giving them control over duration as a courtesy.</p>

        <p><strong>Logged-in Users:</strong> The age gate is hidden for logged-in WordPress users.</p>
    </div>

    <style>
        .wrap h2 { margin-top: 30px; }
        .wrap hr { margin: 30px 0; }
    </style>
    <?php
}
