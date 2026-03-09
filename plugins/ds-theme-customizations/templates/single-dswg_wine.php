<?php
/**
 * Template for single wine pages
 *
 * Loaded by ds-theme-customizations plugin's template loader
 * when viewing a single dswg_wine post.
 *
 * Layout:
 *   Three-column: bottle image | wine details | sidebar (label + downloads)
 *   Bottom: "More from [Producer]" wine grid
 */

/**
 * Inline an SVG icon from the plugin's assets/images directory.
 * Using currentColor so CSS controls fill via `color` property.
 */
function dsp_inline_svg( $filename ) {
    $path = plugin_dir_path( __FILE__ ) . '../assets/images/' . $filename;
    if ( file_exists( $path ) ) {
        echo file_get_contents( $path ); // phpcs:ignore
    }
}

get_header();

while ( have_posts() ) : the_post();

    $wine_id     = get_the_ID();
    $vintage     = get_post_meta( $wine_id, 'dswg_vintage',     true );
    $varietal    = get_post_meta( $wine_id, 'dswg_varietal',    true );
    $alcohol     = get_post_meta( $wine_id, 'dswg_alcohol',     true );
    $label_id    = get_post_meta( $wine_id, 'dswg_wine_logo',   true );
    $files_str   = get_post_meta( $wine_id, 'dswg_wine_files',  true );
    $producer_id = get_post_meta( $wine_id, 'dswg_producer_id', true );
    $bottle_url  = get_the_post_thumbnail_url( $wine_id, 'dswg-bottle-large' );
    $bottle_full = get_the_post_thumbnail_url( $wine_id, 'full' ); // for download
    $wine_types  = get_the_terms( $wine_id, 'dswg_wine_type' );
    $wine_type   = ( $wine_types && ! is_wp_error( $wine_types ) ) ? $wine_types[0] : null;

    // Producer data
    $producer        = $producer_id ? get_post( $producer_id ) : null;
    $producer_logo   = $producer_id ? get_post_meta( $producer_id, 'dswg_producer_logo', true ) : null;
    $producer_loc    = $producer_id ? get_post_meta( $producer_id, 'dswg_location', true ) : null;

    // Tech sheet files
    $files = [];
    if ( $files_str ) {
        foreach ( array_filter( array_map( 'trim', explode( ',', $files_str ) ) ) as $fid ) {
            $url  = wp_get_attachment_url( $fid );
            $name = get_the_title( $fid );
            if ( $url ) {
                $files[] = [ 'url' => $url, 'name' => $name ?: 'Download' ];
            }
        }
    }

    $label_url   = $label_id ? wp_get_attachment_url( $label_id ) : null;
    $has_sidebar = $wine_type || $producer || $producer_loc || $alcohol || $label_id || $bottle_full || ! empty( $files );
    $placeholder_url = WP_PLUGIN_URL . '/ds-wineguy/assets/images/wineplaceholder.png';
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class( 'wine-single' ); ?>>

        <div class="section">
            <div class="container container--narrow">

                <div class="wine-single__layout<?php echo $has_sidebar ? '' : ' wine-single__layout--no-sidebar'; ?>">

                    <!-- BOTTLE IMAGE -->
                    <div class="wine-single__bottle">
                        <?php if ( $bottle_url ) : ?>
                            <img src="<?php echo esc_url( $bottle_url ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                                 class="wine-single__bottle-img">
                        <?php else : ?>
                            <img src="<?php echo esc_url( $placeholder_url ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                                 class="wine-single__bottle-img wine-single__bottle-img--placeholder">
                        <?php endif; ?>
                    </div>

                    <!-- WINE DETAILS -->
                    <div class="wine-single__details">

                        <?php if ( $producer_logo ) : ?>
                            <div class="wine-single__producer-logo-wrap">
                                <?php echo wp_get_attachment_image(
                                    $producer_logo, 'full', false,
                                    [ 'class' => 'wine-single__producer-logo' ]
                                ); ?>
                            </div>
                        <?php endif; ?>

                        <h1 class="wine-single__title"><?php the_title(); ?></h1>

                        <?php if ( $vintage ) : ?>
                            <p class="wine-single__vintage-subtitle"><?php echo esc_html( $vintage ); ?></p>
                        <?php endif; ?>

                        <?php if ( $varietal ) : ?>
                        <div class="wine-single__field">
                            <span class="wine-single__eyebrow"><?php _e( 'Varietal / Blend', 'ds-wineguy' ); ?></span>
                            <p class="wine-single__field-value"><?php echo esc_html( $varietal ); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if ( get_the_content() ) : ?>
                        <div class="wine-single__field">
                            <span class="wine-single__eyebrow"><?php _e( 'Tasting Notes', 'ds-wineguy' ); ?></span>
                            <div class="wine-single__description">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div><!-- .wine-single__details -->

                    <!-- SIDEBAR: wine type, producer, location, downloads, label -->
                    <?php if ( $wine_type || $producer || $producer_loc || $alcohol || $bottle_full || $label_url || ! empty( $files ) || $label_id ) : ?>
                    <div class="wine-single__sidebar">

                        <?php if ( $wine_type || $producer || $producer_loc || $alcohol ) : ?>
                        <div class="wine-single__meta">
                            <?php if ( $wine_type ) : ?>
                                <span class="wine-single__meta-type"><?php echo esc_html( $wine_type->name ); ?></span>
                            <?php endif; ?>
                            <?php if ( $producer ) : ?>
                                <p class="wine-single__meta-producer">
                                    <a href="<?php echo esc_url( get_permalink( $producer_id ) ); ?>">
                                        <?php echo esc_html( $producer->post_title ); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if ( $producer_loc ) : ?>
                                <p class="wine-single__meta-location"><?php echo esc_html( $producer_loc ); ?></p>
                            <?php endif; ?>
                            <?php if ( $alcohol ) : ?>
                                <p class="wine-single__meta-location"><?php echo esc_html( $alcohol ); ?>% ABV</p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( $bottle_full || $label_url || ! empty( $files ) ) : ?>
                        <div class="wine-single__downloads">
                            <p class="wine-single__downloads-heading"><?php _e( 'Download', 'ds-wineguy' ); ?></p>
                            <ul class="wine-single__download-list">

                                <?php if ( $bottle_full ) : ?>
                                <li class="wine-single__download-item">
                                    <a href="<?php echo esc_url( $bottle_full ); ?>"
                                       class="wine-single__download-link"
                                       target="_blank" rel="noopener"
                                       download>
                                        <span class="wine-single__download-icon"><?php dsp_inline_svg( 'icon-bottle.svg' ); ?></span>
                                        <?php _e( 'Bottle Image', 'ds-wineguy' ); ?>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php if ( $label_url ) : ?>
                                <li class="wine-single__download-item">
                                    <a href="<?php echo esc_url( $label_url ); ?>"
                                       class="wine-single__download-link"
                                       target="_blank" rel="noopener"
                                       download>
                                        <span class="wine-single__download-icon"><?php dsp_inline_svg( 'icon-label.svg' ); ?></span>
                                        <?php _e( 'Label Image', 'ds-wineguy' ); ?>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php foreach ( $files as $file ) : ?>
                                <li class="wine-single__download-item">
                                    <a href="<?php echo esc_url( $file['url'] ); ?>"
                                       class="wine-single__download-link"
                                       target="_blank" rel="noopener">
                                        <span class="wine-single__download-icon"><?php dsp_inline_svg( 'icon-document.svg' ); ?></span>
                                        <?php echo esc_html( $file['name'] ); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>

                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php if ( $label_id ) : ?>
                        <div class="wine-single__label-image">
                            <?php echo wp_get_attachment_image( $label_id, 'dswg-logo-thumb', false, [
                                'class' => 'wine-single__label-img',
                            ] ); ?>
                        </div>
                        <?php endif; ?>

                    </div><!-- .wine-single__sidebar -->
                    <?php endif; ?>

                </div><!-- .wine-single__layout -->

            </div><!-- .container -->
        </div><!-- .section -->

        <!-- MORE FROM PRODUCER -->
        <?php if ( $producer ) :
            $more_wines = get_posts([
                'post_type'      => 'dswg_wine',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post__not_in'   => [ $wine_id ],
                'meta_query'     => [
                    'relation' => 'AND',
                    [
                        'key'   => 'dswg_producer_id',
                        'value' => $producer_id,
                    ],
                    [
                        'relation' => 'OR',
                        [
                            'key'     => 'dswg_wine_active',
                            'value'   => '1',
                            'compare' => '=',
                        ],
                        [
                            'key'     => 'dswg_wine_active',
                            'compare' => 'NOT EXISTS',
                        ],
                    ],
                ],
            ]);
        ?>
        <?php if ( ! empty( $more_wines ) ) : ?>
        <section class="section wine-single__more">
            <div class="container container--narrow">

                <div class="section-header">
                    <h2 class="section-header__title">
                        <?php printf(
                            /* translators: %s: producer name */
                            esc_html__( 'More from %s', 'ds-wineguy' ),
                            esc_html( $producer->post_title )
                        ); ?>
                    </h2>
                </div>

                <div class="wine-grid">
                    <?php foreach ( $more_wines as $more_wine ) :
                        $mw_id      = $more_wine->ID;
                        $mw_vintage = get_post_meta( $mw_id, 'dswg_vintage', true );
                        $mw_types   = get_the_terms( $mw_id, 'dswg_wine_type' );
                        $mw_type    = ( $mw_types && ! is_wp_error( $mw_types ) ) ? $mw_types[0] : null;
                        $mw_bottle  = get_the_post_thumbnail_url( $mw_id, 'dswg-bottle-large' );

                        $mw_subtitle_parts = array_filter( [ $mw_vintage, $mw_type ? $mw_type->name : '' ] );
                        $mw_subtitle = implode( ' · ', $mw_subtitle_parts );
                    ?>
                    <article class="wine-card">
                        <a href="<?php echo esc_url( get_permalink( $mw_id ) ); ?>" class="wine-card__toggle">

                            <div class="wine-card__bottle">
                                <img src="<?php echo esc_url( $mw_bottle ?: $placeholder_url ); ?>"
                                     alt="<?php echo esc_attr( $more_wine->post_title ); ?>"
                                     class="wine-card__bottle-img<?php echo ! $mw_bottle ? ' wine-card__bottle-img--placeholder' : ''; ?>">
                            </div>

                            <div class="wine-card__info">
                                <h3 class="wine-card__title"><?php echo esc_html( $more_wine->post_title ); ?></h3>
                                <?php if ( $mw_subtitle ) : ?>
                                    <p class="wine-card__subtitle"><?php echo esc_html( $mw_subtitle ); ?></p>
                                <?php endif; ?>
                            </div>

                        </a>
                    </article>
                    <?php endforeach; ?>
                </div><!-- .wine-grid -->

                <p class="wine-single__back-link">
                    <a href="<?php echo esc_url( get_permalink( $producer_id ) ); ?>" class="button button--secondary">
                        <?php printf(
                            esc_html__( '← Explore %s', 'ds-wineguy' ),
                            esc_html( $producer->post_title )
                        ); ?>
                    </a>
                </p>

            </div>
        </section>
        <?php endif; ?>
        <?php endif; ?>

    </article>

<?php endwhile; ?>

<?php get_footer(); ?>
