<?php
/**
 * Custom Functions for Integrity Wines
 * 
 * Add custom PHP functions here as needed
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enable SVG uploads for producer logos
 * Adds SVG to allowed mime types
 */
function integrity_enable_svg_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'integrity_enable_svg_uploads');

/**
 * Fix SVG display in media library
 * WordPress doesn't display SVG thumbnails by default
 */
function integrity_fix_svg_display($response, $attachment, $meta) {
    if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
        $response['image'] = [
            'src' => $response['url'],
            'width' => 400,
            'height' => 400,
        ];
        $response['thumb'] = [
            'src' => $response['url'],
            'width' => 150,
            'height' => 150,
        ];
        
        // Also set sizes for consistent handling
        $response['sizes'] = [
            'full' => [
                'url' => $response['url'],
                'width' => 400,
                'height' => 400,
                'orientation' => 'landscape',
            ],
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'integrity_fix_svg_display', 10, 3);

/**
 * Add SVG dimensions
 * Allows WordPress to treat SVG like other images
 */
function integrity_svg_dimensions($dimensions, $image_src, $image_meta, $attachment_id) {
    // If this is an SVG, return default dimensions
    if (strpos($image_src, '.svg') !== false) {
        return [
            400, // width
            400, // height
            false // not resized
        ];
    }
    return $dimensions;
}
add_filter('wp_image_src_get_dimensions', 'integrity_svg_dimensions', 10, 4);

/**
 * Security: Sanitize SVG on upload
 * Basic sanitization - strips JavaScript and event handlers
 * For production, consider using the "Safe SVG" plugin for more robust sanitization
 */
function integrity_sanitize_svg_on_upload($file) {
    // Only process SVG files
    if ($file['type'] !== 'image/svg+xml') {
        return $file;
    }
    
    $svg_content = file_get_contents($file['tmp_name']);
    
    // Remove script tags and event handlers (basic security)
    $patterns = [
        '/<script\b[^>]*>.*?<\/script>/is',  // <script> tags
        '/on\w+\s*=\s*["\'][^"\']*["\']/i',  // Event handlers (onclick, onload, etc)
        '/javascript:/i',                     // javascript: protocol
    ];
    
    foreach ($patterns as $pattern) {
        $svg_content = preg_replace($pattern, '', $svg_content);
    }
    
    // Save sanitized content back
    file_put_contents($file['tmp_name'], $svg_content);
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'integrity_sanitize_svg_on_upload');
