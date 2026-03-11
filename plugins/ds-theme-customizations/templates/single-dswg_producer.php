<?php
/**
 * Template for single producer pages
 *
 * Loaded by ds-theme-customizations plugin's template loader
 * when viewing a single dswg_producer post.
 *
 * Hero layout (overlay vs below) is driven by the page's header style
 * setting (_dsp_header_style post meta, falling back to global default).
 * All content sections are constrained to --content-max-width via
 * .container--narrow, except the Connect section which runs full-width.
 */

get_header();

while (have_posts()) : the_post();

    // Hero layout: overlay = name/location inside hero glass bar
    //              below   = name/location in its own section below hero
    $header_style = get_post_meta(get_the_ID(), '_dsp_header_style', true);
    if (!$header_style) {
        $header_style = get_option('dsp_header_default_style', 'solid');
    }
    $hero_layout = ($header_style === 'overlay') ? 'overlay' : 'below';

    // Gather all meta up front
    $location      = get_post_meta(get_the_ID(), 'dswg_location', true);
    $short_desc    = get_post_meta(get_the_ID(), 'dswg_short_desc', true);
    $highlights    = get_post_meta(get_the_ID(), 'dswg_highlights', true);
    $logo_id       = get_post_meta(get_the_ID(), 'dswg_producer_logo', true);
    $gallery_ids   = get_post_meta(get_the_ID(), 'dswg_gallery_ids', true);
    $contact_email = get_post_meta(get_the_ID(), 'dswg_contact_email', true);
    $contact_phone = get_post_meta(get_the_ID(), 'dswg_contact_phone', true);
    $website       = get_post_meta(get_the_ID(), 'dswg_website', true);
    $instagram     = get_post_meta(get_the_ID(), 'dswg_instagram', true);
    $facebook      = get_post_meta(get_the_ID(), 'dswg_facebook', true);
    $twitter       = get_post_meta(get_the_ID(), 'dswg_twitter', true);
    $address       = get_post_meta(get_the_ID(), 'dswg_address', true);
    $latitude      = get_post_meta(get_the_ID(), 'dswg_latitude', true);
    $longitude     = get_post_meta(get_the_ID(), 'dswg_longitude', true);
    $producer_files_str = get_post_meta(get_the_ID(), 'dswg_producer_files', true);

    // Build producer files list
    $producer_files = [];
    if ($producer_files_str) {
        foreach (array_filter(array_map('trim', explode(',', $producer_files_str))) as $fid) {
            $furl  = wp_get_attachment_url($fid);
            $fname = get_the_title($fid);
            if ($furl) $producer_files[] = ['url' => $furl, 'name' => $fname ?: 'Download'];
        }
    }
    $hero_image    = get_the_post_thumbnail_url(get_the_ID(), 'full');

    // SVG icon helper — defined here so it's available everywhere in this template
    if (!function_exists('dsp_inline_svg_producer')) {
        function dsp_inline_svg_producer($filename) {
            $path = plugin_dir_path(__FILE__) . '../assets/images/' . $filename;
            if (file_exists($path)) {
                echo file_get_contents($path); // phpcs:ignore
            }
        }
    }

    $has_connect = $contact_email || $contact_phone || $website
                   || $instagram || $facebook || $twitter
                   || $address || ($latitude && $longitude)
                   || !empty($producer_files);
    ?>

    <!-- HERO -->
    <?php if ($hero_image || $logo_id) : ?>
    <div class="producer-hero <?php echo ($hero_layout === 'overlay') ? 'producer-hero--overlay' : ''; ?>">

        <?php if ($hero_image) : ?>
            <img src="<?php echo esc_url($hero_image); ?>"
                 alt="<?php echo esc_attr(get_the_title()); ?>"
                 class="producer-hero__image">
            <div class="producer-hero__overlay"></div>
        <?php else : ?>
            <div class="producer-hero__placeholder">
                <p>No featured image set</p>
            </div>
        <?php endif; ?>

        <?php if ($logo_id) : ?>
            <?php echo wp_get_attachment_image($logo_id, 'full', false, ['class' => 'producer-hero__logo']); ?>
        <?php endif; ?>

        <?php if ($hero_layout === 'overlay') : ?>
            <div class="producer-hero__identity">
                <div class="producer-hero__identity-left">
                    <h1 class="producer-hero__name"><?php the_title(); ?></h1>
                    <?php if ($location) : ?>
                        <p class="producer-hero__location"><?php echo esc_html($location); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- IDENTITY (solid header only) -->
    <?php if ($hero_layout === 'below') : ?>
    <div class="producer-identity">
        <div class="container container--narrow">
            <h1 class="producer-identity__name"><?php the_title(); ?></h1>
            <?php if ($location) : ?>
                <p class="producer-identity__location"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('producer-single'); ?>>

        <!-- Intro: short description + highlights -->
        <?php if ($short_desc || $highlights) : ?>
        <section class="section" id="producer-intro-section">
            <div class="container container--narrow">
                <div class="producer-intro-grid">

                    <?php if ($short_desc) : ?>
                    <div class="producer-intro">
                        <p class="lead"><?php echo wp_kses_post(wpautop($short_desc)); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($highlights) : ?>
                    <div class="producer-highlights">
                        <ul class="list-highlights">
                            <?php
                            $highlights_array = array_filter(array_map('trim', explode("\n", $highlights)));
                            foreach ($highlights_array as $highlight) : ?>
                                <li><?php echo esc_html($highlight); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- The Story -->
        <?php if (get_the_content()) : ?>
        <section class="section">
            <div class="container container--narrow">

                <div class="section-header">
                    <h2 class="section-header__title">The Story</h2>
                </div>

                <div class="story" id="producer-story">
                    <div class="story__body" id="producer-story-content">
                        <div class="story__grid">

                            <div class="story__text">
                                <?php the_content(); ?>
                            </div>

                            <?php if ($gallery_ids) :
                                $gallery_array = array_filter(explode(',', $gallery_ids));
                                if ($gallery_array) :
                                    $photo_count = count($gallery_array);
                                    $col_map = [1=>1, 2=>2, 3=>3, 4=>2, 5=>3, 6=>3, 7=>4, 8=>4, 9=>3];
                                    $cols = $col_map[$photo_count] ?? 4;
                                ?>
                            <div class="story__photos">
                                <div class="photo-grid photo-grid--single-column" style="--photo-cols: <?php echo $cols; ?>">
                                    <?php foreach ($gallery_array as $image_id) : ?>
                                        <div class="photo-grid__item">
                                            <?php echo wp_get_attachment_image(trim($image_id), 'dswg-producer-large'); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; endif; ?>

                        </div>
                    </div>

                    <button type="button" class="story__toggle" id="story-toggle" aria-expanded="false">
                        <span class="story__toggle-text">Read the full story</span>
                    </button>
                </div>

        </section>
        <?php endif; ?>

        <!-- The Wines -->
        <?php
        $wines = new WP_Query([
            'post_type'      => 'dswg_wine',
            'posts_per_page' => -1,
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'dswg_producer_id',
                    'value'   => get_the_ID(),
                    'compare' => '=',
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
            'orderby' => 'title',
            'order'   => 'ASC',
        ]);

        if ($wines->have_posts()) : ?>
        <section class="section">
            <div class="container container--narrow">

                <div class="section-header">
                    <h2 class="section-header__title">The Wines</h2>
                </div>

                <div class="wine-grid" id="wine-grid">
                    <?php
                    $placeholder_url = WP_PLUGIN_URL . '/ds-wineguy/assets/images/wineplaceholder.png';

                    while ($wines->have_posts()) : $wines->the_post();
                        $wine_id      = get_the_ID();
                        $vintage      = get_post_meta($wine_id, 'dswg_vintage',   true);
                        $varietal     = get_post_meta($wine_id, 'dswg_varietal',  true);
                        $alcohol      = get_post_meta($wine_id, 'dswg_alcohol',   true);
                        $wine_files   = get_post_meta($wine_id, 'dswg_wine_files', true);
                        $label_id     = get_post_meta($wine_id, 'dswg_wine_logo', true);
                        $excerpt      = get_the_excerpt();
                        $bottle_url   = get_the_post_thumbnail_url($wine_id, 'dswg-bottle-large');
                        $bottle_full  = get_the_post_thumbnail_url($wine_id, 'full');
                        $label_url    = $label_id ? wp_get_attachment_url($label_id) : null;
                        $wine_types   = get_the_terms($wine_id, 'dswg_wine_type');
                        $wine_type    = ($wine_types && !is_wp_error($wine_types)) ? $wine_types[0] : null;

                        // Build subtitle: "2024 · Red Wine" — only parts that exist
                        $subtitle_parts = array_filter([$vintage, $wine_type ? $wine_type->name : '']);
                        $subtitle = implode(' · ', $subtitle_parts);

                        // Trim tasting notes to ~40 words
                        $tasting_notes = $excerpt ? wp_trim_words($excerpt, 40, '&hellip;') : '';

                        // Build file list (same pattern as single wine template)
                        $files = [];
                        if ($wine_files) {
                            foreach (array_filter(array_map('trim', explode(',', $wine_files))) as $fid) {
                                $furl  = wp_get_attachment_url($fid);
                                $fname = get_the_title($fid);
                                if ($furl) $files[] = ['id' => $fid, 'url' => $furl, 'name' => $fname ?: 'Download'];
                            }
                        }

                        $has_expand = $varietal || $alcohol || $tasting_notes || !empty($files) || $bottle_url || $bottle_full || $label_url;
                    ?>

                    <article class="wine-card <?php echo $has_expand ? 'wine-card--expandable' : ''; ?>"
                             data-wine-id="<?php echo esc_attr($wine_id); ?>">

                        <button type="button" class="wine-card__toggle" aria-expanded="false"
                                <?php if (!$has_expand) : ?>style="cursor:default;"<?php endif; ?>>

                            <div class="wine-card__bottle">
                                <img src="<?php echo esc_url($bottle_url ?: $placeholder_url); ?>"
                                     alt="<?php echo esc_attr(get_the_title()); ?>"
                                     class="wine-card__bottle-img<?php echo !$bottle_url ? ' wine-card__bottle-img--placeholder' : ''; ?>">
                            </div>

                            <div class="wine-card__info">
                                <h3 class="wine-card__title"><?php the_title(); ?></h3>
                                <?php if ($subtitle) : ?>
                                    <p class="wine-card__subtitle"><?php echo esc_html($subtitle); ?></p>
                                <?php endif; ?>
                            </div>

                        </button>

                        <?php if ($has_expand) : ?>
                        <template class="wine-card__panel-data">
                            <div class="wine-panel__bottle">
                                <img src="<?php echo esc_url($bottle_url ?: $placeholder_url); ?>"
                                     alt="<?php echo esc_attr(get_the_title()); ?>"
                                     class="wine-panel__bottle-img<?php echo !$bottle_url ? ' wine-panel__bottle-img--placeholder' : ''; ?>">
                            </div>
                            <div class="wine-panel__details">
                                <h3 class="wine-panel__title"><?php the_title(); ?></h3>
                                <?php if ($vintage || ($wine_type && $wine_type->name)) : ?>
                                    <p class="wine-panel__subtitle"><?php echo esc_html($subtitle); ?></p>
                                <?php endif; ?>

                                <?php if ($varietal) : ?>
                                <div class="wine-panel__field">
                                    <span class="wine-panel__eyebrow">Varietal / Blend</span>
                                    <p class="wine-panel__field-value"><?php echo esc_html($varietal); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if ($tasting_notes) : ?>
                                <div class="wine-panel__field">
                                    <span class="wine-panel__eyebrow">Tasting Notes</span>
                                    <p class="wine-panel__field-value"><?php echo esc_html($tasting_notes); ?></p>
                                </div>
                                <?php endif; ?>

                                <a href="<?php the_permalink(); ?>" class="button button--secondary wine-panel__full-link">
                                    View Full Details &rarr;
                                </a>
                            </div>
                            <div class="wine-panel__sidebar">
                                <?php if ($wine_type || $alcohol) : ?>
                                <div class="wine-panel__meta">
                                    <?php if ($wine_type) : ?>
                                        <span class="wine-panel__meta-type"><?php echo esc_html($wine_type->name); ?></span>
                                    <?php endif; ?>
                                    <?php if ($alcohol) : ?>
                                        <p class="wine-panel__meta-abv"><?php echo esc_html($alcohol); ?>% ABV</p>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <?php if ($bottle_full || $label_url || !empty($files)) : ?>
                                <div class="wine-panel__downloads">
                                    <p class="wine-panel__downloads-heading">Download</p>
                                    <ul class="wine-panel__download-list">
                                        <?php if ($bottle_full) : ?>
                                        <li class="wine-panel__download-item">
                                            <a href="<?php echo esc_url($bottle_full); ?>"
                                               class="wine-panel__download-link" target="_blank" rel="noopener" download>
                                                <span class="wine-panel__download-icon"><?php dsp_inline_svg_producer('icon-bottle.svg'); ?></span>
                                                Bottle Image
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if ($label_url) : ?>
                                        <li class="wine-panel__download-item">
                                            <a href="<?php echo esc_url($label_url); ?>"
                                               class="wine-panel__download-link" target="_blank" rel="noopener" download>
                                                <span class="wine-panel__download-icon"><?php dsp_inline_svg_producer('icon-label.svg'); ?></span>
                                                Label Image
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php foreach ($files as $f) : ?>
                                        <li class="wine-panel__download-item">
                                            <a href="<?php echo esc_url($f['url']); ?>"
                                               class="wine-panel__download-link" target="_blank" rel="noopener">
                                                <span class="wine-panel__download-icon"><?php dsp_inline_svg_producer('icon-document.svg'); ?></span>
                                                <?php echo esc_html($f['name']); ?>
                                            </a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                            </div>
                        </template>
                        <?php endif; ?>

                    </article>

                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

        </section>
        <?php endif; ?>

        <!-- Connect -->
        <?php if ($has_connect) : ?>
        <section class="section section--alt producer-connect-section">

                <h2 class="producer-connect__label">Connect with <?php the_title(); ?></h2>

                <div class="producer-connect">

                    <div class="producer-connect__logo-col">
                        <?php if ($logo_id) : ?>
                            <?php echo wp_get_attachment_image($logo_id, 'full', false, ['class' => 'producer-connect__logo']); ?>
                        <?php endif; ?>
                    </div>

                    <div class="producer-connect__identity">
                        <h3 class="producer-connect__name"><?php the_title(); ?></h3>
                        <?php if ($location) : ?>
                            <p class="producer-connect__location"><?php echo esc_html($location); ?></p>
                        <?php endif; ?>
                        <?php if ($address) : ?>
                            <address class="producer-connect__address"><?php echo nl2br(esc_html($address)); ?></address>
                        <?php endif; ?>
                    </div>

                    <?php if ($contact_email || $contact_phone || $website || $instagram || $facebook || $twitter) : ?>
                    <div class="producer-connect__col">
                        <?php if ($website) : ?>
                            <p><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?>
                            </a></p>
                        <?php endif; ?>
                        <?php if ($contact_email) : ?>
                            <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                        <?php endif; ?>
                        <?php if ($contact_phone) : ?>
                            <p><a href="tel:<?php echo esc_attr($contact_phone); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                        <?php endif; ?>

                        <?php if ($instagram || $facebook || $twitter) : ?>
                        <div class="producer-connect__social">
                            <?php if ($instagram) : ?>
                                <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">Instagram</a>
                            <?php endif; ?>
                            <?php if ($facebook) : ?>
                                <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">Facebook</a>
                            <?php endif; ?>
                            <?php if ($twitter) : ?>
                                <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter/X">Twitter/X</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($producer_files)) : ?>
                    <div class="producer-connect__col producer-connect__col--downloads">
                        <p class="producer-connect__downloads-heading">Downloads</p>
                        <ul class="producer-connect__download-list">
                            <?php foreach ($producer_files as $f) : ?>
                            <li class="producer-connect__download-item">
                                <a href="<?php echo esc_url($f['url']); ?>"
                                   class="producer-connect__download-link"
                                   target="_blank" rel="noopener">
                                    <span class="producer-connect__download-icon"><?php dsp_inline_svg_producer('icon-document.svg'); ?></span>
                                    <?php echo esc_html($f['name']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if ($latitude && $longitude) : ?>
                    <div class="producer-connect__map">
                        <iframe
                            width="100%"
                            height="200"
                            frameborder="0"
                            scrolling="no"
                            marginheight="0"
                            marginwidth="0"
                            src="https://www.openstreetmap.org/export/embed.html?bbox=<?php
                                echo esc_attr($longitude - 0.05); ?>%2C<?php
                                echo esc_attr($latitude  - 0.05); ?>%2C<?php
                                echo esc_attr($longitude + 0.05); ?>%2C<?php
                                echo esc_attr($latitude  + 0.05); ?>&amp;layer=mapnik&amp;marker=<?php
                                echo esc_attr($latitude); ?>%2C<?php
                                echo esc_attr($longitude); ?>">
                        </iframe>
                    </div>
                    <?php endif; ?>

                </div>

        </section>
        <?php endif; ?>

    </article>

    <script>
    (function() {
        // Story expand/collapse
        var storyContent = document.getElementById('producer-story-content');
        var storyToggle  = document.getElementById('story-toggle');
        if (storyContent && storyToggle) {
            if (storyContent.scrollHeight <= 320) {
                storyToggle.style.display = 'none';
            } else {
                storyToggle.addEventListener('click', function() {
                    storyContent.classList.toggle('is-expanded');
                    storyToggle.classList.toggle('is-expanded');
                    var open = storyContent.classList.contains('is-expanded');
                    storyToggle.setAttribute('aria-expanded', open);
                    storyToggle.querySelector('.story__toggle-text').textContent =
                        open ? 'Show less' : 'Read the full story';
                });
            }
        }

        // Wine card row-panel expand
        (function() {
            var grid       = document.getElementById('wine-grid');
            var activeCard = null;
            var rowPanel   = null;

            if (!grid) return;

            function getCardCenterX(card) {
                var gridRect = grid.getBoundingClientRect();
                var cardRect = card.getBoundingClientRect();
                return cardRect.left - gridRect.left + cardRect.width / 2;
            }

            function getCardsInSameRow(card) {
                var top = card.getBoundingClientRect().top;
                return Array.from(grid.querySelectorAll('.wine-card')).filter(function(c) {
                    return Math.abs(c.getBoundingClientRect().top - top) < 10;
                });
            }

            function getLastCardInRow(card) {
                var row = getCardsInSameRow(card);
                return row[row.length - 1];
            }

            function buildPanel(card) {
                var tpl  = card.querySelector('.wine-card__panel-data');
                if (!tpl) return null;

                var panel = document.createElement('div');
                panel.className = 'wine-row-panel';
                panel.setAttribute('role', 'region');

                var inner = document.createElement('div');
                inner.className = 'wine-row-panel__inner';
                inner.appendChild(document.importNode(tpl.content, true));
                panel.appendChild(inner);

                return panel;
            }

            function closePanel(animate) {
                if (!rowPanel) return;
                if (activeCard) {
                    activeCard.classList.remove('wine-card--active');
                    activeCard.querySelector('.wine-card__toggle').setAttribute('aria-expanded', 'false');
                }
                activeCard = null;
                if (!animate) {
                    rowPanel.remove();
                    rowPanel = null;
                    return;
                }
                rowPanel.classList.remove('wine-row-panel--open');
                rowPanel.addEventListener('transitionend', function handler() {
                    rowPanel.removeEventListener('transitionend', handler);
                    if (rowPanel) { rowPanel.remove(); rowPanel = null; }
                });
            }

            function openPanel(card) {
                var lastCard = getLastCardInRow(card);
                var panel    = buildPanel(card);
                if (!panel) return;

                // Position caret
                var centerX = getCardCenterX(card);
                var gridW   = grid.offsetWidth;
                panel.style.setProperty('--caret-x', (centerX / gridW * 100).toFixed(2) + '%');

                lastCard.after(panel);
                rowPanel   = panel;
                activeCard = card;

                card.classList.add('wine-card--active');
                card.querySelector('.wine-card__toggle').setAttribute('aria-expanded', 'true');

                // Trigger transition, then scroll so the open panel is visible.
                // We wait for transitionend because the panel is height:0 when
                // inserted — scrollIntoView at insertion time sees it as already
                // in view and does nothing.
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        panel.classList.add('wine-row-panel--open');

                        panel.addEventListener('transitionend', function handler(e) {
                            if (e.propertyName !== 'max-height') return;
                            panel.removeEventListener('transitionend', handler);

                            var header   = document.querySelector('.site-header');
                            var headerH  = header ? header.offsetHeight : 0;
                            var panelTop = panel.getBoundingClientRect().top + window.scrollY - headerH - 16;

                            window.scrollTo({ top: panelTop, behavior: 'smooth' });
                        });
                    });
                });
            }

            grid.addEventListener('click', function(e) {
                var btn = e.target.closest('.wine-card--expandable .wine-card__toggle');
                if (!btn) return;
                var card = btn.closest('.wine-card');

                if (activeCard === card) {
                    closePanel(true);
                    return;
                }

                // If a panel is open for a different row, close without animation then open new
                if (rowPanel) {
                    closePanel(false);
                }

                openPanel(card);
            });

            // Close panel when clicking outside the grid
            document.addEventListener('click', function(e) {
                if (rowPanel && !grid.contains(e.target)) {
                    closePanel(true);
                }
            });
        })();
    })();
    </script>

<?php
endwhile;

get_footer();
