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
    $hero_image    = get_the_post_thumbnail_url(get_the_ID(), 'full');

    $has_connect = $contact_email || $contact_phone || $website
                   || $instagram || $facebook || $twitter
                   || $address || ($latitude && $longitude);
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
                                if ($gallery_array) : ?>
                            <div class="story__photos">
                                <div class="photo-grid photo-grid--single-column">
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

            </div>
        </section>
        <?php endif; ?>

        <!-- The Wines -->
        <?php
        $wines = new WP_Query([
            'post_type'      => 'dswg_wine',
            'posts_per_page' => -1,
            'meta_query'     => [[
                'key'     => 'dswg_producer_id',
                'value'   => get_the_ID(),
                'compare' => '=',
            ]],
            'orderby' => 'title',
            'order'   => 'ASC',
        ]);

        if ($wines->have_posts()) : ?>
        <section class="section">
            <div class="container container--narrow">

                <div class="section-header">
                    <h2 class="section-header__title">The Wines</h2>
                </div>

                <div class="wine-grid">
                    <?php
                    $placeholder_url = WP_PLUGIN_URL . '/ds-wineguy/assets/images/wineplaceholder.png';

                    while ($wines->have_posts()) : $wines->the_post();
                        $wine_id    = get_the_ID();
                        $vintage    = get_post_meta($wine_id, 'dswg_vintage', true);
                        $varietal   = get_post_meta($wine_id, 'dswg_varietal', true);
                        $alcohol    = get_post_meta($wine_id, 'dswg_alcohol', true);
                        $wine_files = get_post_meta($wine_id, 'dswg_wine_files', true);
                        $excerpt    = get_the_excerpt();
                        $wine_types = get_the_terms($wine_id, 'dswg_wine_type');
                        $wine_type  = ($wine_types && !is_wp_error($wine_types)) ? $wine_types[0] : null;
                        $type_class = $wine_type ? 'wine-card__type--' . strtolower(str_replace([' ', 'é'], ['-', 'e'], $wine_type->name)) : '';
                        $has_expand = $varietal || $alcohol || $excerpt || $wine_files;
                    ?>

                    <article class="wine-card <?php echo $has_expand ? 'wine-card--expandable' : ''; ?>">

                        <button type="button" class="wine-card__toggle" aria-expanded="false"
                            <?php if (!$has_expand) : ?>style="cursor:default;"<?php endif; ?>>

                            <div class="wine-card__image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('dswg-bottle-large'); ?>
                                <?php else : ?>
                                    <img src="<?php echo esc_url($placeholder_url); ?>"
                                         alt="<?php echo esc_attr(get_the_title()); ?>"
                                         class="wine-card__placeholder">
                                <?php endif; ?>
                            </div>

                            <div class="wine-card__content">
                                <h3 class="wine-card__title"><?php the_title(); ?></h3>
                                <div class="wine-card__meta-row">
                                    <?php if ($vintage) : ?>
                                        <span class="wine-card__vintage"><?php echo esc_html($vintage); ?></span>
                                    <?php endif; ?>
                                    <?php if ($wine_type) : ?>
                                        <span class="wine-card__type <?php echo esc_attr($type_class); ?>">
                                            <?php echo esc_html($wine_type->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($has_expand) : ?>
                                    <span class="wine-card__expand-hint" aria-hidden="true">More info ↓</span>
                                <?php endif; ?>
                            </div>

                        </button>

                        <?php if ($has_expand) : ?>
                        <div class="wine-card__expand" hidden>
                            <div class="wine-card__expand-inner">

                                <?php if ($varietal || $alcohol) : ?>
                                <dl class="wine-card__details">
                                    <?php if ($varietal) : ?>
                                        <dt>Varietal</dt>
                                        <dd><?php echo esc_html($varietal); ?></dd>
                                    <?php endif; ?>
                                    <?php if ($alcohol) : ?>
                                        <dt>Alcohol</dt>
                                        <dd><?php echo esc_html($alcohol); ?>%</dd>
                                    <?php endif; ?>
                                </dl>
                                <?php endif; ?>

                                <?php if ($excerpt) : ?>
                                    <p class="wine-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>

                                <?php if ($wine_files) : ?>
                                <ul class="download-list wine-card__files">
                                    <?php foreach (explode(',', $wine_files) as $file_id) :
                                        $file_id   = trim($file_id);
                                        if (!$file_id) continue;
                                        $file_url  = wp_get_attachment_url($file_id);
                                        $file_name = get_the_title($file_id);
                                        if ($file_url) : ?>
                                    <li class="download-item">
                                        <a href="<?php echo esc_url($file_url); ?>" class="download-link"
                                           target="_blank" rel="noopener">
                                            <?php echo esc_html($file_name ?: 'Download'); ?>
                                        </a>
                                    </li>
                                    <?php endif; endforeach; ?>
                                </ul>
                                <?php endif; ?>

                                <a href="<?php the_permalink(); ?>" class="button button--secondary wine-card__full-link">
                                    View full details →
                                </a>

                            </div>
                        </div>
                        <?php endif; ?>

                    </article>

                    <?php endwhile; wp_reset_postdata(); ?>
                </div>

            </div>
        </section>
        <?php endif; ?>

        <!-- Connect -->
        <?php if ($has_connect) : ?>
        <section class="section section--alt producer-connect-section">
            <div class="container">

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

                    <?php if ($contact_email || $contact_phone || $website) : ?>
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
                    </div>
                    <?php endif; ?>

                    <?php if ($instagram || $facebook || $twitter) : ?>
                    <div class="producer-connect__col producer-connect__col--social">
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

        // Wine card expand/collapse
        document.querySelectorAll('.wine-card--expandable .wine-card__toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var card   = btn.closest('.wine-card');
                var panel  = card.querySelector('.wine-card__expand');
                var hint   = btn.querySelector('.wine-card__expand-hint');
                var isOpen = btn.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                    requestAnimationFrame(function() { panel.style.maxHeight = '0'; });
                    btn.setAttribute('aria-expanded', 'false');
                    card.classList.remove('wine-card--open');
                    if (hint) hint.textContent = 'More info ↓';
                    panel.addEventListener('transitionend', function handler() {
                        panel.hidden = true;
                        panel.style.maxHeight = '';
                        panel.removeEventListener('transitionend', handler);
                    });
                } else {
                    panel.hidden = false;
                    panel.style.maxHeight = '0';
                    requestAnimationFrame(function() {
                        requestAnimationFrame(function() {
                            panel.style.maxHeight = panel.scrollHeight + 'px';
                        });
                    });
                    btn.setAttribute('aria-expanded', 'true');
                    card.classList.add('wine-card--open');
                    if (hint) hint.textContent = 'Less info ↑';
                    panel.addEventListener('transitionend', function handler() {
                        panel.style.maxHeight = 'none';
                        panel.removeEventListener('transitionend', handler);
                    });
                }
            });
        });
    })();
    </script>

<?php
endwhile;

get_footer();
