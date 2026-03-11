<?php
/**
 * Shortcodes
 *
 * Registers shortcodes and the shared card render helper.
 *
 * [producer_grid] — Renders a grid of producer cards.
 *
 * Attributes:
 *   country  = dswg_country term slug, e.g. country="france"
 *   region   = dswg_region term slug
 *   limit    = number of producers, default -1 (all)
 *   orderby  = title|rand|date, default "title"
 *   order    = ASC|DESC, default "ASC"
 *
 * Examples:
 *   [producer_grid]
 *   [producer_grid country="france"]
 *   [producer_grid country="italy" limit="6" orderby="rand"]
 *
 * Located: ds-wineguy/includes/shortcodes.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render a single producer card.
 *
 * Use this helper instead of including the partial directly
 * so the card markup is always output consistently.
 *
 * @param int $producer_id  Post ID of a dswg_producer post.
 * @return void  Outputs HTML directly (designed for use inside a loop or grid).
 */
function dswg_render_producer_card( $producer_id ) {
    $producer_id = (int) $producer_id;
    if ( ! $producer_id ) {
        return;
    }
    include DSWG_PLUGIN_DIR . 'templates/partials/producer-card.php';
}

/**
 * Build a WP_Query args array for producers.
 *
 * Shared by the shortcode, the AJAX handler, and the archive template
 * so filtering logic lives in one place.
 *
 * @param array $args {
 *   @type string $country  dswg_country slug.
 *   @type string $region   dswg_region slug.
 *   @type int    $limit    posts_per_page, -1 for all.
 *   @type string $orderby  WP_Query orderby value.
 *   @type string $order    ASC or DESC.
 *   @type string $search   Search string (matches post title / meta).
 * }
 * @return array WP_Query args.
 */
function dswg_build_producer_query_args( $args = [] ) {
    $defaults = [
        'country' => '',
        'region'  => '',
        'limit'   => -1,
        'orderby' => 'title',
        'order'   => 'ASC',
        'search'  => '',
    ];
    $args = wp_parse_args( $args, $defaults );

    $query_args = [
        'post_type'      => 'dswg_producer',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $args['limit'],
        'orderby'        => sanitize_key( $args['orderby'] ),
        'order'          => strtoupper( $args['order'] ) === 'DESC' ? 'DESC' : 'ASC',
    ];

    // Tax query — country and/or region
    $tax_query = [];

    if ( ! empty( $args['country'] ) ) {
        $tax_query[] = [
            'taxonomy' => 'dswg_country',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $args['country'] ),
        ];
    }

    if ( ! empty( $args['region'] ) ) {
        $tax_query[] = [
            'taxonomy' => 'dswg_region',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( $args['region'] ),
        ];
    }

    if ( count( $tax_query ) > 1 ) {
        $tax_query['relation'] = 'AND';
    }

    if ( ! empty( $tax_query ) ) {
        $query_args['tax_query'] = $tax_query;
    }

    // Text search — WP_Query 's' searches post_title and post_content.
    // We extend this via meta in the filter hook below.
    if ( ! empty( $args['search'] ) ) {
        $query_args['s'] = sanitize_text_field( $args['search'] );
    }

    return apply_filters( 'dswg_producer_query_args', $query_args, $args );
}

/**
 * Render a producer card grid.
 *
 * Returns the grid HTML as a string. Used by the shortcode handler and
 * the AJAX handler so output is identical in both contexts.
 *
 * @param array $args  Same args as dswg_build_producer_query_args().
 * @return string  HTML string.
 */
function dswg_render_producer_grid( $args = [] ) {
    $query_args = dswg_build_producer_query_args( $args );
    $producers  = new WP_Query( $query_args );

    if ( ! $producers->have_posts() ) {
        $no_results = '<p class="producer-grid__empty">' . esc_html__( 'No producers found.', 'ds-wineguy' ) . '</p>';
        return apply_filters( 'dswg_producer_grid_no_results', $no_results, $args );
    }

    ob_start();
    ?>
    <div class="producer-grid">
        <?php
        while ( $producers->have_posts() ) {
            $producers->the_post();
            $producer_id = get_the_ID();
            dswg_render_producer_card( $producer_id );
        }
        wp_reset_postdata();
        ?>
    </div><!-- .producer-grid -->
    <?php

    return ob_get_clean();
}

/**
 * [producer_grid] shortcode handler.
 *
 * @param array $atts  Shortcode attributes.
 * @return string  Grid HTML.
 */
function dswg_producer_grid_shortcode( $atts ) {
    $atts = shortcode_atts(
        [
            'country' => '',
            'region'  => '',
            'limit'   => -1,
            'orderby' => 'title',
            'order'   => 'ASC',
        ],
        $atts,
        'producer_grid'
    );

    return dswg_render_producer_grid( $atts );
}
add_shortcode( 'producer_grid', 'dswg_producer_grid_shortcode' );


// =============================================================================
// [country_grid] shortcode
// =============================================================================
//
// Renders a grid of country cards linking to each country's archive page.
// Designed for a standalone "Countries" WordPress Page.
//
// Usage:
//   [country_grid]
//
// Each card shows:
//   - Country photo (dswg_country_photo_id term meta) as full-bleed background
//   - Country name
//   - Producer count
//   - Wine count (across all producers in that country)
//
// No attributes needed — always shows all 5 countries, alphabetical.
//
// Template for the card is inline here (no separate partial needed).

/**
 * Render the country grid.
 *
 * @return string  HTML string.
 */
function dswg_render_country_grid() {
    $terms = get_terms( [
        'taxonomy'   => 'dswg_country',
        'hide_empty' => true,
        'orderby'    => 'count',
        'order'      => 'DESC',
    ] );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return '<p>' . esc_html__( 'No countries found.', 'ds-wineguy' ) . '</p>';
    }

    ob_start();
    ?>
    <div class="country-grid">
        <?php foreach ( $terms as $term ) :
            $photo_id  = get_term_meta( $term->term_id, 'dswg_country_photo_id', true );
            $photo_url = $photo_id ? wp_get_attachment_image_url( $photo_id, 'large' ) : '';
            $term_url  = get_term_link( $term );

            // Producer count — WordPress keeps this on the term object
            $producer_count = (int) $term->count;

            // Wine count — query wines whose producer is in this country
            $producer_ids = get_posts( [
                'post_type'      => 'dswg_producer',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [ [
                    'taxonomy' => 'dswg_country',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ] ],
            ] );

            $wine_count = 0;
            if ( ! empty( $producer_ids ) ) {
                $wine_query = new WP_Query( [
                    'post_type'      => 'dswg_wine',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'meta_query'     => [ [
                        'key'     => 'dswg_producer_id',
                        'value'   => $producer_ids,
                        'compare' => 'IN',
                    ] ],
                ] );
                $wine_count = $wine_query->found_posts;
                wp_reset_postdata();
            }

            $image_style = $photo_url
                ? 'background-image: url(' . esc_url( $photo_url ) . ');'
                : '';
        ?>
        <div class="country-card<?php echo $photo_url ? '' : ' country-card--no-image'; ?>">

            <div class="country-card__image"
                 style="<?php echo esc_attr( $image_style ); ?>"
                 role="img"
                 aria-label="<?php echo esc_attr( $term->name ); ?>">

                <div class="country-card__info">
                    <h2 class="country-card__name"><?php echo esc_html( $term->name ); ?></h2>
                    <span class="country-card__label">
                        <?php echo esc_html( sprintf(
                            _n( '%d producer', '%d producers', $producer_count, 'ds-wineguy' ),
                            $producer_count
                        ) ); ?>
                        <?php if ( $wine_count ) : ?>
                            &nbsp;&middot;&nbsp;
                            <?php echo esc_html( sprintf(
                                _n( '%d wine', '%d wines', $wine_count, 'ds-wineguy' ),
                                $wine_count
                            ) ); ?>
                        <?php endif; ?>
                    </span>
                </div>

            </div><!-- .country-card__image -->

            <a href="<?php echo esc_url( $term_url ); ?>"
               class="country-card__link"
               aria-label="<?php echo esc_attr( sprintf( __( 'Explore %s', 'ds-wineguy' ), $term->name ) ); ?>"></a>

        </div><!-- .country-card -->
        <?php endforeach; ?>
    </div><!-- .country-grid -->
    <?php
    return ob_get_clean();
}

/**
 * [country_grid] shortcode handler.
 *
 * @return string  Grid HTML.
 */
function dswg_country_grid_shortcode( $atts ) {
    return dswg_render_country_grid();
}
add_shortcode( 'country_grid', 'dswg_country_grid_shortcode' );

