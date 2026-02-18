<?php
/**
 * Meta Boxes for Producers and Wines
 * 
 * Native WordPress meta boxes (no ACF)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register meta boxes
 */
function dswg_register_meta_boxes() {
    // Producer meta boxes
    add_meta_box(
        'dswg_producer_details',
        __('Producer Details', 'ds-wineguy'),
        'dswg_producer_details_callback',
        'dswg_producer',
        'normal',
        'high'
    );
    
    add_meta_box(
        'dswg_producer_contact',
        __('Contact & Social Media', 'ds-wineguy'),
        'dswg_producer_contact_callback',
        'dswg_producer',
        'normal',
        'high'
    );
    
    add_meta_box(
        'dswg_producer_location',
        __('Location & Coordinates', 'ds-wineguy'),
        'dswg_producer_location_callback',
        'dswg_producer',
        'side',
        'default'
    );
    
    add_meta_box(
        'dswg_producer_logo',
        __('Producer Logo', 'ds-wineguy'),
        'dswg_producer_logo_callback',
        'dswg_producer',
        'normal',        // Changed from 'side' to 'normal'
        'default'
    );
    
    add_meta_box(
        'dswg_producer_gallery',
        __('Image Gallery', 'ds-wineguy'),
        'dswg_producer_gallery_callback',
        'dswg_producer',
        'normal',
        'default'
    );
    
    add_meta_box(
        'dswg_producer_files',
        __('Documents & Files', 'ds-wineguy'),
        'dswg_producer_files_callback',
        'dswg_producer',
        'normal',
        'default'
    );
    
    // Wine meta boxes
    add_meta_box(
        'dswg_wine_details',
        __('Wine Details', 'ds-wineguy'),
        'dswg_wine_details_callback',
        'dswg_wine',
        'normal',
        'high'
    );
    
    add_meta_box(
        'dswg_wine_images',
        __('Wine Images', 'ds-wineguy'),
        'dswg_wine_images_callback',
        'dswg_wine',
        'side',
        'default'
    );
    
    add_meta_box(
        'dswg_wine_files',
        __('Tech Sheets & Marketing Materials', 'ds-wineguy'),
        'dswg_wine_files_callback',
        'dswg_wine',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'dswg_register_meta_boxes');

/**
 * Producer Details Meta Box Callback
 */
function dswg_producer_details_callback($post) {
    wp_nonce_field('dswg_save_producer_meta', 'dswg_producer_nonce');
    
    $location    = get_post_meta($post->ID, 'dswg_location', true);
    $region      = get_post_meta($post->ID, 'dswg_region', true);
    $short_desc  = get_post_meta($post->ID, 'dswg_short_desc', true);
    $highlights  = get_post_meta($post->ID, 'dswg_highlights', true);
    $website     = get_post_meta($post->ID, 'dswg_website', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dswg_location"><?php _e('Display Location', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_location" name="dswg_location" value="<?php echo esc_attr($location); ?>" class="regular-text" />
                <p class="description"><?php _e('Full display location shown on producer page, e.g. "Ribera del Duero, Spain"', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_region"><?php _e('Region / Appellation', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_region" name="dswg_region" value="<?php echo esc_attr($region); ?>" class="regular-text" />
                <p class="description"><?php _e('Wine region or appellation for taxonomy use (e.g., Burgundy, Tuscany)', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_short_desc"><?php _e('Short Description', 'ds-wineguy'); ?></label></th>
            <td>
                <textarea id="dswg_short_desc" name="dswg_short_desc" class="large-text" rows="3"><?php echo esc_textarea($short_desc); ?></textarea>
                <p class="description"><?php _e('2-3 sentence intro shown at the top of the producer page, above The Story. Keep it punchy.', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_highlights"><?php _e('Key Highlights', 'ds-wineguy'); ?></label></th>
            <td>
                <textarea id="dswg_highlights" name="dswg_highlights" class="large-text" rows="5"><?php echo esc_textarea($highlights); ?></textarea>
                <p class="description"><?php _e('One highlight per line. Displayed as bullet points (e.g. "Certified organic since 2005", "5th generation family estate").', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_website"><?php _e('Website URL', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="url" id="dswg_website" name="dswg_website" value="<?php echo esc_url($website); ?>" class="regular-text" />
                <p class="description"><?php _e('Producer\'s website (include https://)', 'ds-wineguy'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Producer Contact & Social Media Meta Box Callback
 */
function dswg_producer_contact_callback($post) {
    $contact_email = get_post_meta($post->ID, 'dswg_contact_email', true);
    $contact_phone = get_post_meta($post->ID, 'dswg_contact_phone', true);
    $instagram = get_post_meta($post->ID, 'dswg_instagram', true);
    $facebook = get_post_meta($post->ID, 'dswg_facebook', true);
    $twitter = get_post_meta($post->ID, 'dswg_twitter', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dswg_contact_email"><?php _e('Contact Email', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="email" id="dswg_contact_email" name="dswg_contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="dswg_contact_phone"><?php _e('Contact Phone', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="tel" id="dswg_contact_phone" name="dswg_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th colspan="2"><h3><?php _e('Social Media', 'ds-wineguy'); ?></h3></th>
        </tr>
        <tr>
            <th><label for="dswg_instagram"><?php _e('Instagram', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="url" id="dswg_instagram" name="dswg_instagram" value="<?php echo esc_url($instagram); ?>" class="regular-text" placeholder="https://instagram.com/username" />
            </td>
        </tr>
        <tr>
            <th><label for="dswg_facebook"><?php _e('Facebook', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="url" id="dswg_facebook" name="dswg_facebook" value="<?php echo esc_url($facebook); ?>" class="regular-text" placeholder="https://facebook.com/page" />
            </td>
        </tr>
        <tr>
            <th><label for="dswg_twitter"><?php _e('Twitter/X', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="url" id="dswg_twitter" name="dswg_twitter" value="<?php echo esc_url($twitter); ?>" class="regular-text" placeholder="https://twitter.com/username" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Producer Location Meta Box Callback
 */
function dswg_producer_location_callback($post) {
    $address = get_post_meta($post->ID, 'dswg_address', true);
    $latitude = get_post_meta($post->ID, 'dswg_latitude', true);
    $longitude = get_post_meta($post->ID, 'dswg_longitude', true);
    
    ?>
    <p>
        <label for="dswg_address"><?php _e('Full Address', 'ds-wineguy'); ?></label><br>
        <textarea id="dswg_address" name="dswg_address" class="widefat" rows="3" placeholder="123 Vineyard Road, Burgundy, France"><?php echo esc_textarea($address); ?></textarea>
    </p>
    <p>
        <button type="button" id="dswg_geocode_btn" class="button button-secondary">
            <?php _e('Get Coordinates from Address', 'ds-wineguy'); ?>
        </button>
        <span id="dswg_geocode_status"></span>
    </p>
    <hr style="margin: 15px 0;">
    <p>
        <label for="dswg_latitude"><?php _e('Latitude', 'ds-wineguy'); ?></label><br>
        <input type="text" id="dswg_latitude" name="dswg_latitude" value="<?php echo esc_attr($latitude); ?>" class="widefat" placeholder="45.4642" />
    </p>
    <p>
        <label for="dswg_longitude"><?php _e('Longitude', 'ds-wineguy'); ?></label><br>
        <input type="text" id="dswg_longitude" name="dswg_longitude" value="<?php echo esc_attr($longitude); ?>" class="widefat" placeholder="9.1900" />
    </p>
    <p class="description">
        <?php _e('GPS coordinates for future interactive map feature', 'ds-wineguy'); ?>
    </p>
    <?php
}

/**
 * Producer Gallery Meta Box Callback
 */
function dswg_producer_gallery_callback($post) {
    $gallery_ids = get_post_meta($post->ID, 'dswg_gallery_ids', true);
    ?>
    <div class="dswg-gallery-container">
        <div class="dswg-gallery-images">
            <?php
            if ($gallery_ids) {
                $ids = explode(',', $gallery_ids);
                foreach ($ids as $id) {
                    if ($id) {
                        $image = wp_get_attachment_image($id, 'thumbnail');
                        echo '<div class="dswg-gallery-image" data-id="' . esc_attr($id) . '">';
                        echo $image;
                        echo '<button type="button" class="dswg-remove-image">×</button>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" id="dswg_gallery_ids" name="dswg_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>" />
        <p>
            <button type="button" class="button dswg-add-images"><?php _e('Add Images to Gallery', 'ds-wineguy'); ?></button>
        </p>
        <p class="description">
            <?php _e('Upload multiple images to create a gallery for this producer', 'ds-wineguy'); ?>
        </p>
    </div>
    <style>
        .dswg-gallery-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .dswg-gallery-image {
            position: relative;
            display: inline-block;
        }
        .dswg-gallery-image img {
            display: block;
            max-width: 150px;
            height: auto;
        }
        .dswg-remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #dc3232;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        .dswg-remove-image:hover {
            background: #a00;
        }
    </style>
    <?php
}

/**
 * Producer Logo Meta Box Callback
 */
function dswg_producer_logo_callback($post) {
    $logo_id = get_post_meta($post->ID, 'dswg_producer_logo', true);
    
    ?>
    <p class="description"><?php _e('Upload the producer logo (PNG or SVG with transparent background). Will display as white overlay on hero images.', 'ds-wineguy'); ?></p>
    
    <table class="form-table">
        <tr>
            <th><label for="dswg_producer_logo"><?php _e('Logo Attachment ID', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_producer_logo" name="dswg_producer_logo" value="<?php echo esc_attr($logo_id); ?>" class="regular-text" />
                <p class="description"><?php _e('Enter the attachment ID of the logo image, or use the button below.', 'ds-wineguy'); ?></p>
            </td>
        </tr>
    </table>
    
    <!-- Media Upload UI (JavaScript handles this) -->
    <div class="dswg-logo-preview" style="margin: 15px 0;">
        <?php if ($logo_id) : ?>
            <?php echo wp_get_attachment_image($logo_id, 'thumbnail'); ?>
        <?php endif; ?>
    </div>
    
    <p>
        <button type="button" class="button dswg-upload-producer-logo"><?php _e('Select Logo from Media Library', 'ds-wineguy'); ?></button>
        <?php if ($logo_id) : ?>
            <button type="button" class="button dswg-remove-producer-logo"><?php _e('Remove Logo', 'ds-wineguy'); ?></button>
        <?php endif; ?>
    </p>
    <?php
}

/**
 * Producer Files Meta Box Callback
 */
function dswg_producer_files_callback($post) {
    $files_string = get_post_meta($post->ID, 'dswg_producer_files', true);
    $files = $files_string ? explode(',', $files_string) : [];
    
    ?>
    <div class="dswg-files-container">
        <div class="dswg-producer-files-list">
            <?php foreach ($files as $file_id) : ?>
                <?php
                if (!$file_id) continue;
                $file_url = wp_get_attachment_url($file_id);
                $file_name = basename(get_attached_file($file_id));
                ?>
                <div class="dswg-file-item" data-id="<?php echo esc_attr($file_id); ?>">
                    <span class="dashicons dashicons-media-document"></span>
                    <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php echo esc_html($file_name); ?></a>
                    <button type="button" class="button-link dswg-remove-producer-file">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="dswg_producer_files" name="dswg_producer_files" value="<?php echo esc_attr($files_string); ?>" />
        <p>
            <button type="button" class="button dswg-add-producer-files"><?php _e('Add Files', 'ds-wineguy'); ?></button>
        </p>
        <p class="description">
            <?php _e('Upload certifications, fact sheets, or other producer documents (PDFs and images)', 'ds-wineguy'); ?>
        </p>
    </div>
    <?php
}

/**
 * Wine Details Meta Box Callback
 */
function dswg_wine_details_callback($post) {
    wp_nonce_field('dswg_save_wine_meta', 'dswg_wine_nonce');
    
    $producer_id = get_post_meta($post->ID, 'dswg_producer_id', true);
    $vintage = get_post_meta($post->ID, 'dswg_vintage', true);
    $varietal = get_post_meta($post->ID, 'dswg_varietal', true);
    $alcohol = get_post_meta($post->ID, 'dswg_alcohol', true);
    
    // Get all producers for dropdown
    $producers = get_posts([
        'post_type' => 'dswg_producer',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="dswg_producer_id"><?php _e('Producer', 'ds-wineguy'); ?> <span class="required">*</span></label></th>
            <td>
                <select id="dswg_producer_id" name="dswg_producer_id" class="widefat" required>
                    <option value=""><?php _e('Select a producer...', 'ds-wineguy'); ?></option>
                    <?php foreach ($producers as $producer) : ?>
                        <option value="<?php echo esc_attr($producer->ID); ?>" <?php selected($producer_id, $producer->ID); ?>>
                            <?php echo esc_html($producer->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Which producer makes this wine?', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_vintage"><?php _e('Vintage', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_vintage" name="dswg_vintage" value="<?php echo esc_attr($vintage); ?>" class="regular-text" placeholder="2023" />
                <p class="description"><?php _e('Year (leave blank for NV)', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_varietal"><?php _e('Varietal/Blend', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_varietal" name="dswg_varietal" value="<?php echo esc_attr($varietal); ?>" class="regular-text" placeholder="Pinot Noir, Chardonnay Blend, etc." />
                <p class="description"><?php _e('Grape varieties used', 'ds-wineguy'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="dswg_alcohol"><?php _e('Alcohol %', 'ds-wineguy'); ?></label></th>
            <td>
                <input type="text" id="dswg_alcohol" name="dswg_alcohol" value="<?php echo esc_attr($alcohol); ?>" class="small-text" placeholder="13.5" />
                <p class="description"><?php _e('Alcohol by volume', 'ds-wineguy'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Wine Images Meta Box Callback
 */
function dswg_wine_images_callback($post) {
    $logo_id = get_post_meta($post->ID, 'dswg_wine_logo', true);
    
    ?>
    <p><strong><?php _e('Bottle Image', 'ds-wineguy'); ?></strong></p>
    <p class="description"><?php _e('Use the "Featured Image" box to set the bottle image', 'ds-wineguy'); ?></p>
    
    <hr style="margin: 15px 0;">
    
    <p><strong><?php _e('Wine Logo', 'ds-wineguy'); ?></strong></p>
    <div class="dswg-logo-preview">
        <?php if ($logo_id) : ?>
            <?php echo wp_get_attachment_image($logo_id, 'thumbnail'); ?>
        <?php endif; ?>
    </div>
    <input type="hidden" id="dswg_wine_logo" name="dswg_wine_logo" value="<?php echo esc_attr($logo_id); ?>" />
    <p>
        <button type="button" class="button dswg-upload-logo"><?php _e('Upload Logo', 'ds-wineguy'); ?></button>
        <?php if ($logo_id) : ?>
            <button type="button" class="button dswg-remove-logo"><?php _e('Remove Logo', 'ds-wineguy'); ?></button>
        <?php endif; ?>
    </p>
    <p class="description"><?php _e('Producer logo or wine label logo', 'ds-wineguy'); ?></p>
    <?php
}

/**
 * Wine Files Meta Box Callback
 */
function dswg_wine_files_callback($post) {
    $files = get_post_meta($post->ID, 'dswg_wine_files', true);
    if (!is_array($files)) {
        $files = [];
    }
    
    ?>
    <div class="dswg-files-container">
        <div class="dswg-files-list">
            <?php foreach ($files as $file_id) : ?>
                <?php
                $file_url = wp_get_attachment_url($file_id);
                $file_name = basename(get_attached_file($file_id));
                ?>
                <div class="dswg-file-item" data-id="<?php echo esc_attr($file_id); ?>">
                    <span class="dashicons dashicons-media-document"></span>
                    <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php echo esc_html($file_name); ?></a>
                    <button type="button" class="button-link dswg-remove-file">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="dswg_wine_files" name="dswg_wine_files" value="<?php echo esc_attr(implode(',', $files)); ?>" />
        <p>
            <button type="button" class="button dswg-add-files"><?php _e('Add Files', 'ds-wineguy'); ?></button>
        </p>
        <p class="description">
            <?php _e('Upload tech sheets, marketing materials, or other PDFs (PDFs and images only)', 'ds-wineguy'); ?>
        </p>
    </div>
    <style>
        .dswg-file-item {
            padding: 8px;
            margin-bottom: 8px;
            background: #f0f0f1;
            border-radius: 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dswg-file-item .dashicons {
            color: #2271b1;
        }
        .dswg-file-item a {
            flex: 1;
            text-decoration: none;
        }
        .dswg-remove-file {
            color: #d63638;
        }
        .dswg-remove-file:hover {
            color: #a00;
        }
    </style>
    <?php
}

/**
 * Save Producer Meta
 */
function dswg_save_producer_meta($post_id) {
    // Check nonce
    if (!isset($_POST['dswg_producer_nonce']) || !wp_verify_nonce($_POST['dswg_producer_nonce'], 'dswg_save_producer_meta')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // DEBUG - log what's in POST for logo
    error_log('DSWG Save Debug - Post ID: ' . $post_id);
    error_log('Logo in POST: ' . (isset($_POST['dswg_producer_logo']) ? $_POST['dswg_producer_logo'] : 'NOT SET'));
    
    // Save fields with appropriate sanitization
    
    // URL fields
    $url_fields = ['dswg_website', 'dswg_instagram', 'dswg_facebook', 'dswg_twitter'];
    foreach ($url_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, esc_url_raw($_POST[$field]));
        }
    }
    
    // Email field
    if (isset($_POST['dswg_contact_email'])) {
        update_post_meta($post_id, 'dswg_contact_email', sanitize_email($_POST['dswg_contact_email']));
    }
    
    // Text fields
    $text_fields = [
        'dswg_location',
        'dswg_region',
        'dswg_contact_phone',
        'dswg_address',
        'dswg_latitude',
        'dswg_longitude',
        'dswg_gallery_ids',
        'dswg_producer_logo',
        'dswg_producer_files',
    ];
    
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, $field, $value);
            
            // Extra logging for logo
            if ($field === 'dswg_producer_logo') {
                error_log('Saving logo - Value: ' . $value . ' | Result: ' . (update_post_meta($post_id, $field, $value) ? 'success' : 'failed or no change'));
            }
        }
    }
    
    // Textarea fields need sanitize_textarea_field
    $textarea_fields = ['dswg_short_desc', 'dswg_highlights'];
    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
        }
    }
}
add_action('save_post_dswg_producer', 'dswg_save_producer_meta');

/**
 * Save Wine Meta
 */
function dswg_save_wine_meta($post_id) {
    // Check nonce
    if (!isset($_POST['dswg_wine_nonce']) || !wp_verify_nonce($_POST['dswg_wine_nonce'], 'dswg_save_wine_meta')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save fields
    $fields = [
        'dswg_producer_id',
        'dswg_vintage',
        'dswg_varietal',
        'dswg_alcohol',
        'dswg_wine_logo',
        'dswg_wine_files',
    ];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_dswg_wine', 'dswg_save_wine_meta');
