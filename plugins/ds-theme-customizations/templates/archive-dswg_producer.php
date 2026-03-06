<?php
/**
 * Producer Archive Template
 *
 * Displays all producers at /producers/ with country dropdown filter
 * and AJAX-powered text search. Filter results replace the grid without
 * a page reload.
 *
 * Loaded by ds-theme-customizations template loader (load_custom_templates).
 * Located: ds-theme-customizations/templates/archive-dswg_producer.php
 *
 * Hero image and intro text are set in Wine Producers > Settings.
 * Options: dswg_archive_hero_id, dswg_archive_intro_text
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Get all countries that have at least one published producer
$countries = get_terms( [
    'taxonomy'   => 'dswg_country',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
] );

// Archive hero + intro text from settings
$hero_id    = (int) get_option( 'dswg_archive_hero_id', 0 );
$intro_text = get_option( 'dswg_archive_intro_text', '' );
$hero_url   = $hero_id ? wp_get_attachment_image_url( $hero_id, 'full' ) : '';

// Build inline style for hero background
$hero_style = $hero_url
    ? ' style="background-image: url(\'' . esc_url( $hero_url ) . '\');"'
    : '';

?>
<div class="producer-archive">

    <!-- Page Header + Filters -->
    <div class="producer-archive__header fullwidth"<?php echo $hero_style; ?>>
        <div class="producer-archive__overlay"></div>
        <div class="producer-archive__header-inner">

            <div class="section-header">
                <span class="section-header__label"><?php esc_html_e( 'Integrity Wines', 'ds-wineguy' ); ?></span>
                <h1 class="section-header__title"><?php esc_html_e( 'Our Producers', 'ds-wineguy' ); ?></h1>
                <?php if ( $intro_text ) : ?>
                    <p class="section-header__desc"><?php echo esc_html( $intro_text ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Filter Bar -->
            <div class="producer-filter">
                <div class="producer-filter__inner">

                    <div class="producer-filter__field">
                        <label for="producer-search" class="producer-filter__label">
                            <?php esc_html_e( 'Search', 'ds-wineguy' ); ?>
                        </label>
                        <input type="text"
                               id="producer-search"
                               class="producer-filter__input"
                               placeholder="<?php esc_attr_e( 'Producer name, region, location…', 'ds-wineguy' ); ?>"
                               autocomplete="off">
                    </div>

                    <div class="producer-filter__field">
                        <label for="producer-country" class="producer-filter__label">
                            <?php esc_html_e( 'Country', 'ds-wineguy' ); ?>
                        </label>
                        <select id="producer-country" class="producer-filter__select">
                            <option value=""><?php esc_html_e( 'All Countries', 'ds-wineguy' ); ?></option>
                            <?php if ( $countries && ! is_wp_error( $countries ) ) : ?>
                                <?php foreach ( $countries as $country ) : ?>
                                    <option value="<?php echo esc_attr( $country->slug ); ?>">
                                        <?php echo esc_html( $country->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="producer-filter__count" id="producer-count" aria-live="polite"></div>

                </div>
            </div>

        </div><!-- .producer-archive__header-inner -->
    </div><!-- .producer-archive__header -->

    <!-- Results -->
    <div class="producer-archive__results section">
        <div id="producer-grid-container">

            <?php
            // Initial load — all producers, alphabetical
            echo dswg_render_producer_grid( [ 'orderby' => 'title', 'order' => 'ASC' ] );
            ?>

        </div>
    </div>

    <!-- Loading Indicator (hidden by default) -->
    <div class="producer-archive__loading" id="producer-loading" hidden aria-hidden="true">
        <span class="producer-archive__spinner"></span>
        <span class="screen-reader-text"><?php esc_html_e( 'Loading producers…', 'ds-wineguy' ); ?></span>
    </div>

</div><!-- .producer-archive -->

<script>
/**
 * Producer archive filter — AJAX search + country dropdown.
 * Runs after DOM ready, uses dswgData (localized in ds-wineguy.php).
 */
(function() {
    'use strict';

    const searchInput   = document.getElementById('producer-search');
    const countrySelect = document.getElementById('producer-country');
    const gridContainer = document.getElementById('producer-grid-container');
    const loadingEl     = document.getElementById('producer-loading');
    const countEl       = document.getElementById('producer-count');

    if ( ! searchInput || ! countrySelect || ! gridContainer ) return;

    let debounceTimer = null;

    function updateCount( count ) {
        if ( ! countEl ) return;
        if ( count === null ) {
            countEl.textContent = '';
            return;
        }
        countEl.textContent = count === 1
            ? '1 producer'
            : count + ' producers';
    }

    function fetchProducers() {
        const search  = searchInput.value.trim();
        const country = countrySelect.value;

        loadingEl.hidden = false;
        loadingEl.removeAttribute( 'aria-hidden' );
        gridContainer.style.opacity = '0.4';

        const formData = new FormData();
        formData.append( 'action',  'dswg_filter_producers' );
        formData.append( 'nonce',   dswgData.nonce );
        formData.append( 'country', country );
        formData.append( 'search',  search );

        // AbortController lets us cancel a hung request after 15 seconds
        const controller = new AbortController();
        const timeoutId  = setTimeout( function() { controller.abort(); }, 15000 );

        fetch( dswgData.ajaxurl, {
            method: 'POST',
            body:   formData,
            signal: controller.signal,
        } )
        .then( function( r ) {
            if ( ! r.ok ) { throw new Error( 'Server error: ' + r.status ); }
            return r.json();
        } )
        .then( function( data ) {
            if ( data && data.success ) {
                gridContainer.innerHTML = data.data.html;
                updateCount( data.data.count );
            } else {
                gridContainer.innerHTML = '<p class="producer-grid__empty">Something went wrong. Please try again.</p>';
                updateCount( null );
            }
        } )
        .catch( function( err ) {
            var msg = err.name === 'AbortError'
                ? 'Request timed out — please try again.'
                : 'Something went wrong. Please try again.';
            gridContainer.innerHTML = '<p class="producer-grid__empty">' + msg + '</p>';
            updateCount( null );
        } )
        .finally( function() {
            clearTimeout( timeoutId );
            loadingEl.hidden = true;
            loadingEl.setAttribute( 'aria-hidden', 'true' );
            gridContainer.style.opacity = '1';
        } );
    }

    // Search: debounced 300ms so we don't fire on every keystroke
    searchInput.addEventListener( 'input', function() {
        clearTimeout( debounceTimer );
        debounceTimer = setTimeout( fetchProducers, 300 );
    } );

    // Country dropdown: fire immediately on change
    countrySelect.addEventListener( 'change', fetchProducers );

}());
</script>

<?php get_footer(); ?>
