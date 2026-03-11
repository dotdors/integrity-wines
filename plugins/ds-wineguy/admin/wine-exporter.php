<?php
/**
 * Wine CSV Exporter
 *
 * Exports all wine posts to a CSV matching the import format, plus
 * image/file status columns so Pat can confirm what's been uploaded.
 *
 * Columns exported:
 *   Wine Name, Producer, Inv Number, Vintage, Varietal, Alcohol,
 *   Wine Type, Description, Active,
 *   Bottle Image, Label — Front, Label — Back, Files
 *
 * Sorted by Inv Number ascending.
 * Delivered as a direct file download — no temp file needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register submenu page
 */
function dswg_add_wine_exporter_page() {
    add_submenu_page(
        'edit.php?post_type=dswg_producer',
        __( 'Export Wines', 'ds-wineguy' ),
        __( 'Export Wines', 'ds-wineguy' ),
        'manage_options',
        'dswg-export-wines',
        'dswg_render_wine_exporter_page'
    );
}
add_action( 'admin_menu', 'dswg_add_wine_exporter_page' );

/**
 * Handle the CSV download via admin-post.php
 * WordPress routes action=dswg_export_wines here before any output.
 */
function dswg_handle_wine_export() {
    if ( ! check_admin_referer( 'dswg_wine_export' ) ) {
        wp_die( 'Invalid request.' );
    }
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Insufficient permissions.' );
    }

    $filename = 'integrity-wines-export-' . date( 'Y-m-d' ) . '.csv';

    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    // UTF-8 BOM so Excel opens it correctly
    echo "\xEF\xBB\xBF";

    $out = fopen( 'php://output', 'w' );

    // Header row — matches import column names exactly
    fputcsv( $out, [
        'Wine Name',
        'Producer',
        'Inv Number',
        'Vintage',
        'Varietal',
        'Alcohol',
        'Wine Type',
        'Description',
        'Active',
        'Bottle Image',
        'Label — Front',
        'Label — Back',
        'Files',
    ] );

    // Fetch all wines, ordered by inventory number meta
    $wines = get_posts( [
        'post_type'      => 'dswg_wine',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
        'meta_key'       => 'dswg_inventory_no',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    ] );

    // Build producer ID → title cache
    $producer_cache = [];

    foreach ( $wines as $wine_id ) {
        // Producer
        $producer_id    = (int) get_post_meta( $wine_id, 'dswg_producer_id', true );
        if ( ! isset( $producer_cache[ $producer_id ] ) ) {
            $producer_cache[ $producer_id ] = $producer_id ? get_the_title( $producer_id ) : '';
        }
        $producer_name = $producer_cache[ $producer_id ];

        // Basic meta
        $inv_no    = get_post_meta( $wine_id, 'dswg_inventory_no', true );
        $vintage   = get_post_meta( $wine_id, 'dswg_vintage',      true );
        $varietal  = get_post_meta( $wine_id, 'dswg_varietal',     true );
        $alcohol   = get_post_meta( $wine_id, 'dswg_alcohol',      true );
        $active    = get_post_meta( $wine_id, 'dswg_wine_active',  true ) ? 'Y' : 'N';

        // Wine type taxonomy
        $terms     = get_the_terms( $wine_id, 'dswg_wine_type' );
        $wine_type = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

        // Description (post content)
        $post        = get_post( $wine_id );
        $description = $post ? $post->post_content : '';

        // Image / file status
        $has_bottle      = has_post_thumbnail( $wine_id ) ? 'Y' : 'N';
        $has_label_front = get_post_meta( $wine_id, 'dswg_wine_logo',       true ) ? 'Y' : 'N';
        $has_label_back  = get_post_meta( $wine_id, 'dswg_wine_label_back', true ) ? 'Y' : 'N';

        // Files — list attachment filenames, comma-separated
        $files_str   = get_post_meta( $wine_id, 'dswg_wine_files', true );
        $file_ids    = $files_str ? array_filter( explode( ',', $files_str ) ) : [];
        $file_names  = [];
        foreach ( $file_ids as $fid ) {
            $attached = get_attached_file( (int) $fid );
            if ( $attached ) {
                $file_names[] = basename( $attached );
            }
        }
        $files_cell = implode( ', ', $file_names );

        fputcsv( $out, [
            get_the_title( $wine_id ),
            $producer_name,
            $inv_no,
            $vintage,
            $varietal,
            $alcohol,
            $wine_type,
            $description,
            $active,
            $has_bottle,
            $has_label_front,
            $has_label_back,
            $files_cell,
        ] );
    }

    fclose( $out );
    exit;
}
add_action( 'admin_post_dswg_export_wines', 'dswg_handle_wine_export' );

/**
 * Render the exporter page
 */
function dswg_render_wine_exporter_page() {
    $export_url = wp_nonce_url(
        admin_url( 'admin-post.php?action=dswg_export_wines' ),
        'dswg_wine_export'
    );

    // Count wines for info
    $total = wp_count_posts( 'dswg_wine' );
    $count = isset( $total->publish ) ? (int) $total->publish : 0;
    ?>
    <div class="wrap">
        <h1><?php _e( 'Export Wines', 'ds-wineguy' ); ?></h1>

        <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
            <h2 style="margin-top: 0;"><?php _e( 'Download Wine Data', 'ds-wineguy' ); ?></h2>
            <p><?php printf(
                __( 'Exports all %d published wines to a CSV file. Columns match the import format, with additional Y/N columns for bottle image, front label, back label, and a list of attached file names.', 'ds-wineguy' ),
                $count
            ); ?></p>
            <p><?php _e( 'Wines are sorted by inventory number. Open in Excel or Google Sheets.', 'ds-wineguy' ); ?></p>
            <a href="<?php echo esc_url( $export_url ); ?>" class="button button-primary" style="margin-top: 8px;">
                ⬇ <?php _e( 'Download CSV', 'ds-wineguy' ); ?>
            </a>
        </div>
    </div>
    <?php
}
