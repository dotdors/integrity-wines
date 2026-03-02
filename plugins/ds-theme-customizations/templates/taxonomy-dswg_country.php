<?php
/**
 * Country Taxonomy Archive Template
 *
 * Displayed at /country/{slug}/ — e.g. /country/france/
 * Layout: split panel hero (image left, country name + description right),
 * followed by the producer card grid.
 *
 * Loaded by ds-theme-customizations template loader (load_custom_templates).
 * Located: ds-theme-customizations/templates/taxonomy-dswg_country.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$term        = get_queried_object();
$map_id      = get_term_meta( $term->term_id, 'dswg_country_map_id', true );
$map_url     = $map_id ? wp_get_attachment_image_url( $map_id, 'large' ) : '';
$description = $term->description;

?>
<!-- Split hero: image left | title + description right -->
    <div class="split-hero">

        <div class="split-hero__image">
            <?php if ( $map_url ) : ?>
                <img src="<?php echo esc_url( $map_url ); ?>"
                     alt="<?php echo esc_attr( sprintf( __( 'Map of wine regions in %s', 'ds-wineguy' ), $term->name ) ); ?>"
                     loading="eager">
            <?php else : ?>
                <div class="split-hero__image-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>

        <div class="split-hero__panel">
            <span class="split-hero__label"><?php esc_html_e( 'Our Producers', 'ds-wineguy' ); ?></span>
            <h1 class="split-hero__title"><?php echo esc_html( $term->name ); ?></h1>
            <?php if ( $description ) : ?>
                <div class="split-hero__desc">
                    <?php echo wp_kses_post( wpautop( $description ) ); ?>
                </div>
            <?php endif; ?>
        </div>

    </div><!-- .split-hero -->

    <!-- Producer grid — straight in, no section header -->
    <div class="country-archive__producers section">
        <div class="container">
            <?php
            echo dswg_render_producer_grid( [
                'country' => $term->slug,
                'orderby' => 'title',
                'order'   => 'ASC',
            ] );
            ?>
        </div>
    </div>

    <!-- Back link -->
    <div class="section section--narrow">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'dswg_producer' ) ); ?>" class="button button--secondary">
            &larr; <?php esc_html_e( 'All Producers', 'ds-wineguy' ); ?>
        </a>
    </div>

<?php get_footer(); ?>
