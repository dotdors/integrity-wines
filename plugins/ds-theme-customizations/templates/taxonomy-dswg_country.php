<?php
/**
 * Country Taxonomy Archive Template
 *
 * Displayed at /country/{slug}/ — e.g. /country/france/
 * Layout:
 *   1. Split-hero: editorial photo left, country name + description right
 *   2. Producer card grid
 *   3. Regional map image (contained, editorial)
 *   4. Back link
 *
 * Loaded by ds-theme-customizations template loader (load_custom_templates).
 * Located: ds-theme-customizations/templates/taxonomy-dswg_country.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$term        = get_queried_object();
$map_id      = get_term_meta( $term->term_id, 'dswg_country_map_id',   true );
$photo_id    = get_term_meta( $term->term_id, 'dswg_country_photo_id', true );
$map_url     = $map_id   ? wp_get_attachment_image_url( $map_id,   'full' ) : '';
$photo_url   = $photo_id ? wp_get_attachment_image_url( $photo_id, 'large' ) : '';
$description = $term->description;

?>
<!--
    .country-archive-wrapper — flex column container.
    Default: hero first, producer grid below.
    To flip (grid first, hero below): add to your LESS/CSS:
        .country-archive-wrapper { flex-direction: column-reverse; }
-->
<div class="country-archive-wrapper">

    <!-- Split hero: editorial photo left | title + description right -->
    <div class="split-hero section--alt">

        <div class="split-hero__image">
            <?php if ( $photo_url ) : ?>
                <img src="<?php echo esc_url( $photo_url ); ?>"
                     alt="<?php echo esc_attr( $term->name ); ?>"
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

    <!-- Producer grid -->
    <div class="country-archive__producers section">
        <?php
        echo dswg_render_producer_grid( [
            'country' => $term->slug,
            'orderby' => 'title',
            'order'   => 'ASC',
        ] );
        ?>
    </div>

    <?php if ( $map_url ) : ?>
    <!-- Regional map — full width -->
    <div class="country-archive__map section">
        <img src="<?php echo esc_url( $map_url ); ?>"
             alt="<?php echo esc_attr( sprintf( __( 'Wine regions map of %s', 'ds-wineguy' ), $term->name ) ); ?>"
             loading="lazy"
             class="country-archive__map-img">
    </div>
    <?php endif; ?>

</div><!-- .country-archive-wrapper -->

<!-- Back link -->
<div class="country-archive__back section section--narrow">
    <a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>" class="button button--secondary">
        <?php esc_html_e( 'Explore Our Countries', 'ds-wineguy' ); ?>
    </a>
</div>

<?php get_footer(); ?>

