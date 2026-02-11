<?php
/**
 * Template Functions
 * 
 * Helper functions for displaying producers and wines on the frontend
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get producer wines
 * 
 * @param int $producer_id Producer post ID
 * @return array Array of wine post objects
 */
function dswg_get_producer_wines($producer_id) {
    $args = [
        'post_type' => 'dswg_wine',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'dswg_producer_id',
                'value' => $producer_id,
            ]
        ],
    ];
    
    return get_posts($args);
}

/**
 * Display producer contact information
 * 
 * @param int $post_id Producer post ID
 */
function dswg_display_producer_contact($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $email = get_post_meta($post_id, 'dswg_contact_email', true);
    $phone = get_post_meta($post_id, 'dswg_contact_phone', true);
    $website = get_post_meta($post_id, 'dswg_website', true);
    
    if (!$email && !$phone && !$website) {
        return;
    }
    
    echo '<div class="producer-contact">';
    
    if ($website) {
        echo '<p class="producer-website"><a href="' . esc_url($website) . '" target="_blank" rel="noopener">' . __('Visit Website', 'ds-wineguy') . '</a></p>';
    }
    
    if ($email) {
        echo '<p class="producer-email"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
    }
    
    if ($phone) {
        echo '<p class="producer-phone">' . esc_html($phone) . '</p>';
    }
    
    echo '</div>';
}

/**
 * Display producer social media links
 * 
 * @param int $post_id Producer post ID
 */
function dswg_display_producer_social($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $instagram = get_post_meta($post_id, 'dswg_instagram', true);
    $facebook = get_post_meta($post_id, 'dswg_facebook', true);
    $twitter = get_post_meta($post_id, 'dswg_twitter', true);
    
    if (!$instagram && !$facebook && !$twitter) {
        return;
    }
    
    echo '<div class="producer-social">';
    
    if ($instagram) {
        echo '<a href="' . esc_url($instagram) . '" target="_blank" rel="noopener" class="social-instagram">Instagram</a>';
    }
    
    if ($facebook) {
        echo '<a href="' . esc_url($facebook) . '" target="_blank" rel="noopener" class="social-facebook">Facebook</a>';
    }
    
    if ($twitter) {
        echo '<a href="' . esc_url($twitter) . '" target="_blank" rel="noopener" class="social-twitter">Twitter</a>';
    }
    
    echo '</div>';
}

/**
 * Display producer gallery
 * 
 * @param int $post_id Producer post ID
 */
function dswg_display_producer_gallery($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery_ids = get_post_meta($post_id, 'dswg_gallery_ids', true);
    
    if (!$gallery_ids) {
        return;
    }
    
    $ids = explode(',', $gallery_ids);
    
    if (empty($ids)) {
        return;
    }
    
    echo '<div class="producer-gallery">';
    
    foreach ($ids as $id) {
        if ($id) {
            $image_url = wp_get_attachment_image_url($id, 'dswg-producer-large');
            $thumb_url = wp_get_attachment_image_url($id, 'dswg-producer-thumb');
            
            if ($image_url) {
                echo '<a href="' . esc_url($image_url) . '" class="gallery-item">';
                echo '<img src="' . esc_url($thumb_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '" />';
                echo '</a>';
            }
        }
    }
    
    echo '</div>';
}

/**
 * Get wine producer
 * 
 * @param int $wine_id Wine post ID
 * @return WP_Post|null Producer post object or null
 */
function dswg_get_wine_producer($wine_id = null) {
    if (!$wine_id) {
        $wine_id = get_the_ID();
    }
    
    $producer_id = get_post_meta($wine_id, 'dswg_producer_id', true);
    
    if (!$producer_id) {
        return null;
    }
    
    return get_post($producer_id);
}

/**
 * Display wine files (tech sheets, marketing materials)
 * 
 * @param int $post_id Wine post ID
 */
function dswg_display_wine_files($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $files = get_post_meta($post_id, 'dswg_wine_files', true);
    
    if (!$files) {
        return;
    }
    
    $file_ids = explode(',', $files);
    
    if (empty($file_ids)) {
        return;
    }
    
    echo '<div class="wine-files">';
    echo '<h3>' . __('Downloads', 'ds-wineguy') . '</h3>';
    echo '<ul class="wine-files-list">';
    
    foreach ($file_ids as $file_id) {
        if ($file_id) {
            $file_url = wp_get_attachment_url($file_id);
            $file_name = basename(get_attached_file($file_id));
            
            if ($file_url) {
                echo '<li><a href="' . esc_url($file_url) . '" target="_blank" download>' . esc_html($file_name) . '</a></li>';
            }
        }
    }
    
    echo '</ul>';
    echo '</div>';
}

/**
 * Display wine details
 * 
 * @param int $post_id Wine post ID
 */
function dswg_display_wine_details($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $vintage = get_post_meta($post_id, 'dswg_vintage', true);
    $varietal = get_post_meta($post_id, 'dswg_varietal', true);
    $alcohol = get_post_meta($post_id, 'dswg_alcohol', true);
    
    echo '<div class="wine-details">';
    
    if ($vintage) {
        echo '<p class="wine-vintage"><strong>' . __('Vintage:', 'ds-wineguy') . '</strong> ' . esc_html($vintage) . '</p>';
    }
    
    if ($varietal) {
        echo '<p class="wine-varietal"><strong>' . __('Varietal:', 'ds-wineguy') . '</strong> ' . esc_html($varietal) . '</p>';
    }
    
    if ($alcohol) {
        echo '<p class="wine-alcohol"><strong>' . __('Alcohol:', 'ds-wineguy') . '</strong> ' . esc_html($alcohol) . '%</p>';
    }
    
    // Display wine type taxonomy
    $wine_types = get_the_terms($post_id, 'dswg_wine_type');
    if ($wine_types && !is_wp_error($wine_types)) {
        $types = wp_list_pluck($wine_types, 'name');
        echo '<p class="wine-type"><strong>' . __('Type:', 'ds-wineguy') . '</strong> ' . esc_html(implode(', ', $types)) . '</p>';
    }
    
    echo '</div>';
}
