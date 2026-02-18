<?php
/**
 * Importer for Producers and Wines
 * 
 * Allows uploading spreadsheets to bulk import data
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add importer page to admin menu
 */
function dswg_add_importer_page() {
    add_submenu_page(
        'edit.php?post_type=dswg_producer',
        __('Import Data', 'ds-wineguy'),
        __('Import', 'ds-wineguy'),
        'manage_options',
        'dswg-import',
        'dswg_render_importer_page'
    );
}
add_action('admin_menu', 'dswg_add_importer_page');

/**
 * Render importer page
 */
function dswg_render_importer_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php
        // Handle file upload
        if (isset($_POST['dswg_import_submit']) && check_admin_referer('dswg_import_nonce')) {
            dswg_process_import();
        }
        ?>
        
        <div class="card" style="max-width: 800px;">
            <h2><?php _e('Import Producers and Wines from Spreadsheet', 'ds-wineguy'); ?></h2>
            
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('dswg_import_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="dswg_import_type"><?php _e('Import Type', 'ds-wineguy'); ?></label>
                        </th>
                        <td>
                            <select name="dswg_import_type" id="dswg_import_type" required>
                                <option value=""><?php _e('Select...', 'ds-wineguy'); ?></option>
                                <option value="producers"><?php _e('Producers', 'ds-wineguy'); ?></option>
                                <option value="wines"><?php _e('Wines', 'ds-wineguy'); ?></option>
                            </select>
                            <p class="description"><?php _e('What are you importing?', 'ds-wineguy'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="dswg_import_file"><?php _e('Upload File', 'ds-wineguy'); ?></label>
                        </th>
                        <td>
                            <input type="file" name="dswg_import_file" id="dswg_import_file" accept=".xlsx,.xls,.csv" required />
                            <p class="description"><?php _e('Upload an Excel (.xlsx, .xls) or CSV file', 'ds-wineguy'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Import Data', 'ds-wineguy'), 'primary', 'dswg_import_submit'); ?>
            </form>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2><?php _e('Spreadsheet Format Requirements', 'ds-wineguy'); ?></h2>
            
            <h3><?php _e('Producers Spreadsheet Columns:', 'ds-wineguy'); ?></h3>
            <ul>
                <li><strong>Producer Name</strong> (required) - Name of the producer</li>
                <li><strong>Country</strong> (required) - France, Italy, Spain, Austria, or Slovenia</li>
                <li><strong>Region</strong> - Wine region/appellation</li>
                <li><strong>Description</strong> - Full bio/description</li>
                <li><strong>Website</strong> - Website URL</li>
                <li><strong>Email</strong> - Contact email</li>
                <li><strong>Phone</strong> - Contact phone</li>
                <li><strong>Instagram</strong> - Instagram URL</li>
                <li><strong>Facebook</strong> - Facebook URL</li>
                <li><strong>Twitter</strong> - Twitter/X URL</li>
                <li><strong>Address</strong> - Full address</li>
                <li><strong>Latitude</strong> - GPS latitude</li>
                <li><strong>Longitude</strong> - GPS longitude</li>
            </ul>
            
            <h3><?php _e('Wines Spreadsheet Columns:', 'ds-wineguy'); ?></h3>
            <ul>
                <li><strong>Wine Name</strong> (required) - Name of the wine</li>
                <li><strong>Producer</strong> (required) - Producer name (must match existing producer)</li>
                <li><strong>Vintage</strong> - Year (or leave blank for NV)</li>
                <li><strong>Description</strong> - Tasting notes</li>
                <li><strong>Varietal</strong> - Grape varieties</li>
                <li><strong>Alcohol</strong> - Alcohol percentage</li>
                <li><strong>Wine Type</strong> - Red Wine, White Wine, Rosé, Sparkling, etc.</li>
            </ul>
            
            <p class="description">
                <?php _e('Note: Images and files must be added manually after import.', 'ds-wineguy'); ?>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Process import
 */
function dswg_process_import() {
    // Check file upload
    if (!isset($_FILES['dswg_import_file']) || $_FILES['dswg_import_file']['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="notice notice-error"><p>' . __('Error uploading file', 'ds-wineguy') . '</p></div>';
        return;
    }
    
    $import_type = sanitize_text_field($_POST['dswg_import_type']);
    $file = $_FILES['dswg_import_file'];
    $file_path = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if we have required libraries
    if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
        // Try to load PhpSpreadsheet if available
        $composer_autoload = ABSPATH . 'vendor/autoload.php';
        if (file_exists($composer_autoload)) {
            require_once $composer_autoload;
        } else {
            echo '<div class="notice notice-error"><p>' . __('PhpSpreadsheet library not found. CSV import only.', 'ds-wineguy') . '</p></div>';
            
            // Fall back to CSV processing
            if ($file_ext === 'csv') {
                dswg_process_csv_import($file_path, $import_type);
                return;
            } else {
                echo '<div class="notice notice-error"><p>' . __('Please upload a CSV file or install PhpSpreadsheet library for Excel support.', 'ds-wineguy') . '</p></div>';
                return;
            }
        }
    }
    
    try {
        // Load spreadsheet
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        // Process based on type
        if ($import_type === 'producers') {
            $results = dswg_import_producers($data);
        } elseif ($import_type === 'wines') {
            $results = dswg_import_wines($data);
        } else {
            echo '<div class="notice notice-error"><p>' . __('Invalid import type', 'ds-wineguy') . '</p></div>';
            return;
        }
        
        // Show results
        echo '<div class="notice notice-success"><p>';
        printf(__('Import complete! Created %d items. Skipped %d rows.', 'ds-wineguy'), $results['created'], $results['skipped']);
        echo '</p></div>';
        
        if (!empty($results['errors'])) {
            echo '<div class="notice notice-warning"><p><strong>' . __('Errors:', 'ds-wineguy') . '</strong></p><ul>';
            foreach ($results['errors'] as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="notice notice-error"><p>' . __('Error processing file: ', 'ds-wineguy') . esc_html($e->getMessage()) . '</p></div>';
    }
}

/**
 * Process CSV import (fallback when PhpSpreadsheet not available)
 */
function dswg_process_csv_import($file_path, $import_type) {
    $data = [];
    
    if (($handle = fopen($file_path, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        fclose($handle);
    }
    
    if (empty($data)) {
        echo '<div class="notice notice-error"><p>' . __('No data found in CSV file', 'ds-wineguy') . '</p></div>';
        return;
    }
    
    if ($import_type === 'producers') {
        $results = dswg_import_producers($data);
    } elseif ($import_type === 'wines') {
        $results = dswg_import_wines($data);
    }
    
    echo '<div class="notice notice-success"><p>';
    printf(__('Import complete! Created %d items. Skipped %d rows.', 'ds-wineguy'), $results['created'], $results['skipped']);
    echo '</p></div>';
}

/**
 * Import producers from spreadsheet data
 */
function dswg_import_producers($data) {
    $results = ['created' => 0, 'skipped' => 0, 'errors' => []];
    
    // Get header row
    $headers = array_shift($data);
    $headers = array_map('trim', $headers);
    
    foreach ($data as $row_num => $row) {
        // Skip empty rows
        if (empty(array_filter($row))) {
            $results['skipped']++;
            continue;
        }
        
        // Map row to headers
        $producer_data = array_combine($headers, $row);
        
        // Required fields
        if (empty($producer_data['Producer Name'])) {
            $results['errors'][] = sprintf(__('Row %d: Missing producer name', 'ds-wineguy'), $row_num + 2);
            $results['skipped']++;
            continue;
        }
        
        // Create producer
        $post_data = [
            'post_title' => sanitize_text_field($producer_data['Producer Name']),
            'post_content' => !empty($producer_data['Description']) ? wp_kses_post($producer_data['Description']) : '',
            'post_type' => 'dswg_producer',
            'post_status' => 'publish',
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            $results['errors'][] = sprintf(__('Row %d: Error creating producer', 'ds-wineguy'), $row_num + 2);
            $results['skipped']++;
            continue;
        }
        
        // Add meta fields
        if (!empty($producer_data['Display Location'])) {
            update_post_meta($post_id, 'dswg_location', sanitize_text_field($producer_data['Display Location']));
        }
        if (!empty($producer_data['Short Description'])) {
            update_post_meta($post_id, 'dswg_short_desc', sanitize_textarea_field($producer_data['Short Description']));
        }
        if (!empty($producer_data['Key Highlights'])) {
            update_post_meta($post_id, 'dswg_highlights', sanitize_textarea_field($producer_data['Key Highlights']));
        }
        if (!empty($producer_data['Region'])) {
            update_post_meta($post_id, 'dswg_region', sanitize_text_field($producer_data['Region']));
        }
        if (!empty($producer_data['Website'])) {
            update_post_meta($post_id, 'dswg_website', esc_url_raw($producer_data['Website']));
        }
        if (!empty($producer_data['Email'])) {
            update_post_meta($post_id, 'dswg_contact_email', sanitize_email($producer_data['Email']));
        }
        if (!empty($producer_data['Phone'])) {
            update_post_meta($post_id, 'dswg_contact_phone', sanitize_text_field($producer_data['Phone']));
        }
        if (!empty($producer_data['Instagram'])) {
            update_post_meta($post_id, 'dswg_instagram', esc_url_raw($producer_data['Instagram']));
        }
        if (!empty($producer_data['Facebook'])) {
            update_post_meta($post_id, 'dswg_facebook', esc_url_raw($producer_data['Facebook']));
        }
        if (!empty($producer_data['Twitter'])) {
            update_post_meta($post_id, 'dswg_twitter', esc_url_raw($producer_data['Twitter']));
        }
        if (!empty($producer_data['Address'])) {
            update_post_meta($post_id, 'dswg_address', sanitize_textarea_field($producer_data['Address']));
        }
        if (!empty($producer_data['Latitude'])) {
            update_post_meta($post_id, 'dswg_latitude', sanitize_text_field($producer_data['Latitude']));
        }
        if (!empty($producer_data['Longitude'])) {
            update_post_meta($post_id, 'dswg_longitude', sanitize_text_field($producer_data['Longitude']));
        }
        
        // Assign country
        if (!empty($producer_data['Country'])) {
            $term = term_exists($producer_data['Country'], 'dswg_country');
            if ($term) {
                wp_set_object_terms($post_id, (int)$term['term_id'], 'dswg_country');
            }
        }
        
        $results['created']++;
    }
    
    return $results;
}

/**
 * Import wines from spreadsheet data
 */
function dswg_import_wines($data) {
    $results = ['created' => 0, 'skipped' => 0, 'errors' => []];
    
    // Get header row
    $headers = array_shift($data);
    $headers = array_map('trim', $headers);
    
    foreach ($data as $row_num => $row) {
        // Skip empty rows
        if (empty(array_filter($row))) {
            $results['skipped']++;
            continue;
        }
        
        // Map row to headers
        $wine_data = array_combine($headers, $row);
        
        // Required fields
        if (empty($wine_data['Wine Name'])) {
            $results['errors'][] = sprintf(__('Row %d: Missing wine name', 'ds-wineguy'), $row_num + 2);
            $results['skipped']++;
            continue;
        }
        
        if (empty($wine_data['Producer'])) {
            $results['errors'][] = sprintf(__('Row %d: Missing producer', 'ds-wineguy'), $row_num + 2);
            $results['skipped']++;
            continue;
        }
        
        // Find producer
        $producer = get_page_by_title($wine_data['Producer'], OBJECT, 'dswg_producer');
        if (!$producer) {
            $results['errors'][] = sprintf(__('Row %d: Producer "%s" not found', 'ds-wineguy'), $row_num + 2, $wine_data['Producer']);
            $results['skipped']++;
            continue;
        }
        
        // Create wine
        $post_data = [
            'post_title' => sanitize_text_field($wine_data['Wine Name']),
            'post_content' => !empty($wine_data['Description']) ? wp_kses_post($wine_data['Description']) : '',
            'post_type' => 'dswg_wine',
            'post_status' => 'publish',
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            $results['errors'][] = sprintf(__('Row %d: Error creating wine', 'ds-wineguy'), $row_num + 2);
            $results['skipped']++;
            continue;
        }
        
        // Link to producer
        update_post_meta($post_id, 'dswg_producer_id', $producer->ID);
        
        // Add meta fields
        if (!empty($wine_data['Vintage'])) {
            update_post_meta($post_id, 'dswg_vintage', sanitize_text_field($wine_data['Vintage']));
        }
        if (!empty($wine_data['Varietal'])) {
            update_post_meta($post_id, 'dswg_varietal', sanitize_text_field($wine_data['Varietal']));
        }
        if (!empty($wine_data['Alcohol'])) {
            update_post_meta($post_id, 'dswg_alcohol', sanitize_text_field($wine_data['Alcohol']));
        }
        
        // Assign wine type
        if (!empty($wine_data['Wine Type'])) {
            $term = term_exists($wine_data['Wine Type'], 'dswg_wine_type');
            if ($term) {
                wp_set_object_terms($post_id, (int)$term['term_id'], 'dswg_wine_type');
            }
        }
        
        $results['created']++;
    }
    
    return $results;
}
