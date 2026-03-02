<?php
/**
 * Producer Card Partial
 *
 * Shared card template used by the producer archive, country taxonomy pages,
 * the [producer_grid] shortcode, and AJAX filter responses.
 *
 * Usage:
 *   $producer_id = 123; // required — must be set before including
 *   include DSWG_PLUGIN_DIR . 'templates/partials/producer-card.php';
 *
 * Or via helper:
 *   dswg_render_producer_card( $producer_id );
 *
 * Located: ds-wineguy/templates/partials/producer-card.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// $producer_id must be set by the caller
if ( empty( $producer_id ) ) {
    return;
}

$title     = get_the_title( $producer_id );
$permalink = get_permalink( $producer_id );
$image_url = get_the_post_thumbnail_url( $producer_id, 'dswg-producer-thumb' );

// Fallback background when no featured image is set
$image_style = $image_url
    ? 'background-image: url(' . esc_url( $image_url ) . ');'
    : '';

$logo_id  = get_post_meta( $producer_id, 'dswg_producer_logo', true );
$logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';

// Country label
$countries    = get_the_terms( $producer_id, 'dswg_country' );
$country_name = ( $countries && ! is_wp_error( $countries ) ) ? $countries[0]->name : '';

?>
<div class="ds-producer-card<?php echo $image_url ? '' : ' ds-producer-card--no-image'; ?>">

    <div class="ds-producer-card__image"
         style="<?php echo esc_attr( $image_style ); ?>"
         role="img"
         aria-label="<?php echo esc_attr( $title ); ?> vineyard">

        <?php if ( $logo_url ) : ?>
            <div class="ds-producer-card__logo">
                <img src="<?php echo esc_url( $logo_url ); ?>"
                     alt="<?php echo esc_attr( $title ); ?> logo"
                     loading="lazy">
            </div>
        <?php endif; ?>

        <div class="ds-producer-card__info">
            <?php if ( $country_name ) : ?>
                <span class="ds-producer-card__country">
                    <?php echo esc_html( $country_name ); ?>
                </span>
            <?php endif; ?>
            <h3 class="ds-producer-card__name">
                <?php echo esc_html( $title ); ?>
            </h3>
        </div>

    </div><!-- .ds-producer-card__image -->

    <a href="<?php echo esc_url( $permalink ); ?>"
       class="ds-producer-card__link"
       aria-label="<?php echo esc_attr( $title ); ?>"></a>

</div><!-- .ds-producer-card -->
