<?php
/**
 * Search and Filter Functionality
 *
 * Handles AJAX-powered filtering for the producer archive and index pages.
 *
 * The front-end JS (wineguy.js) POSTs to admin-ajax.php with:
 *   action  = dswg_filter_producers
 *   nonce   = dswgData.nonce
 *   country = dswg_country slug (optional)
 *   search  = text string (optional)
 *
 * The handler returns JSON: { html: '...card grid html...', count: N }
 *
 * Located: ds-wineguy/includes/search-filter.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Extend WP_Query search to also match producer-specific meta fields.
 *
 * When a search string is present, WP_Query's built-in 's' parameter
 * only searches post_title and post_content. This filter adds a JOIN
 * and WHERE clause so the search also matches:
 *   - dswg_location  (e.g. "Ribera del Duero, Spain")
 *   - dswg_short_desc
 *   - dswg_region (text meta, not the taxonomy term)
 *
 * Only activates when the custom query var 'dswg_meta_search' is set,
 * so this never runs on unrelated queries.
 */
add_filter( 'posts_join', 'dswg_search_join', 10, 2 );
function dswg_search_join( $join, $query ) {
    if ( empty( $query->get( 'dswg_meta_search' ) ) ) {
        return $join;
    }
    global $wpdb;
    $join .= " LEFT JOIN {$wpdb->postmeta} dswg_sm ON ({$wpdb->posts}.ID = dswg_sm.post_id) ";
    return $join;
}

add_filter( 'posts_where', 'dswg_search_where', 10, 2 );
function dswg_search_where( $where, $query ) {
    $search = $query->get( 'dswg_meta_search' );
    if ( empty( $search ) ) {
        return $where;
    }
    global $wpdb;
    $like = '%' . $wpdb->esc_like( $search ) . '%';

    $meta_where = $wpdb->prepare(
        " AND (
            {$wpdb->posts}.post_title LIKE %s
            OR (
                dswg_sm.meta_key IN ('dswg_location', 'dswg_short_desc', 'dswg_region')
                AND dswg_sm.meta_value LIKE %s
            )
        )",
        $like,
        $like
    );

    $where .= $meta_where;
    return $where;
}

add_filter( 'posts_distinct', 'dswg_search_distinct', 10, 2 );
function dswg_search_distinct( $distinct, $query ) {
    if ( empty( $query->get( 'dswg_meta_search' ) ) ) {
        return $distinct;
    }
    return 'DISTINCT';
}

/**
 * Build producer query args with meta-search support.
 *
 * Wraps dswg_build_producer_query_args() (from shortcodes.php) and adds
 * the dswg_meta_search query var so the JOIN/WHERE filters above activate.
 *
 * @param string $search   Raw search string.
 * @param string $country  Country slug.
 * @return array WP_Query args.
 */
function dswg_build_filter_query_args( $search, $country ) {
    $args = [
        'country' => $country,
        // Don't pass 'search' here — we handle it via dswg_meta_search below
    ];
    $query_args = dswg_build_producer_query_args( $args );

    if ( ! empty( $search ) ) {
        $query_args['dswg_meta_search'] = sanitize_text_field( $search );
    }

    return $query_args;
}

/**
 * AJAX handler — filter producers.
 *
 * Returns JSON: { html: string, count: int }
 */
function dswg_ajax_filter_producers() {
    check_ajax_referer( 'dswg_nonce', 'nonce' );

    $country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
    $search  = isset( $_POST['search'] )  ? sanitize_text_field( $_POST['search'] )  : '';

    $query_args = dswg_build_filter_query_args( $search, $country );
    $producers  = new WP_Query( $query_args );

    if ( ! $producers->have_posts() ) {
        wp_send_json_success( [
            'html'  => '<p class="producer-grid__empty">' . esc_html__( 'No producers found.', 'ds-wineguy' ) . '</p>',
            'count' => 0,
        ] );
        return;
    }

    ob_start();
    while ( $producers->have_posts() ) {
        $producers->the_post();
        $producer_id = get_the_ID();
        dswg_render_producer_card( $producer_id );
    }
    wp_reset_postdata();

    $cards_html = ob_get_clean();

    wp_send_json_success( [
        'html'  => '<div class="producer-grid">' . $cards_html . '</div>',
        'count' => $producers->found_posts,
    ] );
}
add_action( 'wp_ajax_dswg_filter_producers',        'dswg_ajax_filter_producers' );
add_action( 'wp_ajax_nopriv_dswg_filter_producers', 'dswg_ajax_filter_producers' );
