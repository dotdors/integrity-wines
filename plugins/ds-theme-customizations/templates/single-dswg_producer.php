<?php
/**
 * Template for single producer pages
 * 
 * Template Name: Producer Single
 * 
 * This template is loaded by ds-theme-customizations plugin's template loader
 * when viewing a single dswg_producer post.
 */

get_header();

while (have_posts()) : the_post();
    
    // LAYOUT OPTION: Set to 'overlay' or 'below'
    // 'overlay' = name/location overlaid on hero image
    // 'below' = name/location in separate section below hero
    $hero_layout = 'below'; // Change this to 'overlay' to test
    
    // Get meta fields
    $location = get_post_meta(get_the_ID(), 'dswg_location', true);
    $short_desc = get_post_meta(get_the_ID(), 'dswg_short_desc', true);
    $highlights = get_post_meta(get_the_ID(), 'dswg_highlights', true);
    $logo_id = get_post_meta(get_the_ID(), 'dswg_producer_logo', true);
    
    // Get featured image
    $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    
    // DEBUG - Remove this after testing
    echo '<!-- DEBUG INFO:';
    echo ' Logo ID: ' . var_export($logo_id, true);
    echo ' | Location: ' . var_export($location, true);
    echo ' | Short Desc: ' . var_export($short_desc, true);
    echo ' | Has Hero Image: ' . var_export(!empty($hero_image), true);
    echo ' -->';
    
    ?>
    
    <!-- Producer Hero -->
    <?php if ($hero_image || $logo_id) : ?>
    <div class="producer-hero <?php echo ($hero_layout === 'overlay') ? 'producer-hero--overlay' : ''; ?>">
        <?php if ($hero_image) : ?>
            <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="producer-hero__image">
            <div class="producer-hero__overlay"></div>
        <?php else : ?>
            <!-- No featured image set -->
            <div class="producer-hero__placeholder" style="background: var(--color-background-alt); height: 60vh; display: flex; align-items: center; justify-content: center; color: var(--color-text-muted);">
                <p>No featured image set for hero</p>
            </div>
        <?php endif; ?>
        
        <?php if ($logo_id) : ?>
            <?php 
            $logo_img = wp_get_attachment_image($logo_id, 'full', false, ['class' => 'producer-hero__logo']);
            echo '<!-- Logo HTML: ' . esc_html($logo_img) . ' -->';
            echo $logo_img;
            ?>
        <?php else : ?>
            <!-- No producer logo set (dswg_producer_logo meta field) -->
        <?php endif; ?>
        
        <?php if ($hero_layout === 'overlay') : ?>
            <!-- OVERLAY LAYOUT: Name/Location on hero -->
            <div class="producer-hero__identity">
                <div class="producer-hero__identity-left">
                    <h1 class="producer-hero__name"><?php the_title(); ?></h1>
                    <?php if ($location) : ?>
                        <p class="producer-hero__location"><?php echo esc_html($location); ?></p>
                    <?php endif; ?>
                </div>
                <!-- Right side reserved for future content (e.g., CTA button, social icons, etc.) -->
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php if ($hero_layout === 'below') : ?>
    <!-- BELOW LAYOUT: Name/Location in separate section -->
    <div class="producer-identity">
        <div class="container" style="max-width: var(--content-max-width);">
            <h1 class="producer-identity__name"><?php the_title(); ?></h1>
            <?php if ($location) : ?>
                <p class="producer-identity__location"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content Area -->
    <article id="post-<?php the_ID(); ?>" <?php post_class('producer-single'); ?>>
        
        <!-- Short Description & Highlights -->
        <?php if ($short_desc || $highlights) : ?>
        <section class="section" id="producer-intro-section">
            <div class="container" style="max-width: var(--content-max-width);">
                
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
                            foreach ($highlights_array as $highlight) : 
                            ?>
                                <li><?php echo esc_html($highlight); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </section>
        <?php endif; ?>
        
        <!-- The Story (with gallery) -->
        <?php if (get_the_content()) : ?>
        <section class="section">
            <div class="container">
                <div class="producer-story-container" style="max-width: var(--container-max-width); margin: 0 auto;">
                    
                    <h2>The Story</h2>
                    
                    <div class="story" id="producer-story">
                        <div class="story__body" id="producer-story-content">
                            <div class="story__grid">
                                <!-- Story Content (left column) -->
                                <div class="story__text">
                                    <?php the_content(); ?>
                                </div>
                                
                                <!-- Photo Grid (right column, single column) -->
                                <div class="story__photos">
                                    <?php 
                                    // Display gallery if available
                                    $gallery_ids = get_post_meta(get_the_ID(), 'dswg_gallery_ids', true);
                                    if ($gallery_ids) :
                                        $gallery_array = explode(',', $gallery_ids);
                                        
                                        if (count($gallery_array) > 0) :
                                        ?>
                                        <div class="photo-grid photo-grid--single-column">
                                            <?php foreach ($gallery_array as $image_id) : ?>
                                                <div class="photo-grid__item">
                                                    <?php echo wp_get_attachment_image($image_id, 'dswg-producer-large'); ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="story__toggle" id="story-toggle" aria-expanded="false">
                            <span class="story__toggle-text">Read the full story</span>
                        </button>
                    </div>
                    
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- The Wines -->
        <?php
        // Get wines for this producer
        $wines = new WP_Query([
            'post_type' => 'dswg_wine',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'dswg_producer_id',
                    'value' => get_the_ID(),
                    'compare' => '='
                ]
            ],
            'orderby' => 'title',
            'order' => 'ASC'
        ]);
        
        // DEBUG
        echo '<!-- WINE QUERY DEBUG: ';
        echo 'Producer ID: ' . get_the_ID();
        echo ' | Found wines: ' . $wines->found_posts;
        echo ' | Query: ' . print_r($wines->query_vars, true);
        echo ' -->';
        
        if ($wines->have_posts()) :
        ?>
        <!-- WINE SECTION RENDERING -->
        <section class="section">
            <div class="container" style="max-width: var(--container-max-width);">
                
                <div class="section-header">
                    <h2 class="section-header__title">The Wines</h2>
                </div>
                
                <div class="wine-grid">
                    <?php 
                    $wine_count = 0;
                    while ($wines->have_posts()) : $wines->the_post(); 
                    $wine_count++;
                    echo '<!-- Wine #' . $wine_count . ': ' . get_the_title() . ' (ID: ' . get_the_ID() . ') -->';
                    ?>
                        
                        <article class="wine-card">
                            <a href="<?php the_permalink(); ?>" class="wine-card__link">
                                
                                <?php if (has_post_thumbnail()) : ?>
                                <div class="wine-card__image">
                                    <?php the_post_thumbnail('dswg-bottle-large'); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="wine-card__content">
                                    <h3 class="wine-card__title"><?php the_title(); ?></h3>
                                    
                                    <?php 
                                    $vintage = get_post_meta(get_the_ID(), 'dswg_vintage', true);
                                    if ($vintage) : 
                                    ?>
                                        <span class="wine-card__vintage"><?php echo esc_html($vintage); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $wine_types = get_the_terms(get_the_ID(), 'dswg_wine_type');
                                    if ($wine_types && !is_wp_error($wine_types)) :
                                        $wine_type = $wine_types[0];
                                        $type_class = 'wine-card__type--' . strtolower(str_replace(' ', '-', $wine_type->name));
                                    ?>
                                        <span class="wine-card__type <?php echo esc_attr($type_class); ?>">
                                            <?php echo esc_html($wine_type->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                            </a>
                        </article>
                        
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                
            </div>
        </section>
        <?php else : ?>
        <!-- WINES SECTION NOT RENDERING: Query found <?php echo $wines->found_posts; ?> wines but have_posts() is false -->
        <?php endif; ?>
        
        <!-- Contact & Social (optional section) -->
        <?php 
        $contact_email = get_post_meta(get_the_ID(), 'dswg_contact_email', true);
        $contact_phone = get_post_meta(get_the_ID(), 'dswg_contact_phone', true);
        $website = get_post_meta(get_the_ID(), 'dswg_website', true);
        $instagram = get_post_meta(get_the_ID(), 'dswg_instagram', true);
        $facebook = get_post_meta(get_the_ID(), 'dswg_facebook', true);
        $twitter = get_post_meta(get_the_ID(), 'dswg_twitter', true);
        $address = get_post_meta(get_the_ID(), 'dswg_address', true);
        $latitude = get_post_meta(get_the_ID(), 'dswg_latitude', true);
        $longitude = get_post_meta(get_the_ID(), 'dswg_longitude', true);
        
        // DEBUG - what's actually in the database?
        echo '<!-- CONNECT DEBUG: ';
        echo 'Instagram: ' . var_export($instagram, true) . ' | ';
        echo 'Facebook: ' . var_export($facebook, true) . ' | ';
        echo 'Twitter: ' . var_export($twitter, true);
        echo ' -->';
        
        if ($contact_email || $contact_phone || $website || $instagram || $facebook || $twitter || $address || ($latitude && $longitude)) :
        ?>
        <section class="section section--alt">
            <div class="container" style="max-width: var(--container-max-width);">
                
                <h6 class="producer-connect__label">Connect With <?php the_title(); ?></h6>
                
                <div class="producer-connect">
                    
                    <!-- Column 1: Logo Only -->
                    <div class="producer-connect__logo-col">
                        <?php if ($logo_id) : ?>
                            <?php echo wp_get_attachment_image($logo_id, 'thumbnail', false, ['class' => 'producer-connect__logo']); ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Column 2: Name / Location / Address -->
                    <div class="producer-connect__identity">
                        <h3 class="producer-connect__name"><?php the_title(); ?></h3>
                        <?php if ($location) : ?>
                            <p class="producer-connect__location"><?php echo esc_html($location); ?></p>
                        <?php endif; ?>
                        <?php if ($address) : ?>
                            <address class="producer-connect__address"><?php echo nl2br(esc_html($address)); ?></address>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contact -->
                    <?php if ($contact_email || $contact_phone || $website) : ?>
                    <div class="producer-connect__col">
                        <?php if ($website) : ?>
                            <p><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener"><?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?></a></p>
                        <?php endif; ?>
                        <?php if ($contact_email) : ?>
                            <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                        <?php endif; ?>
                        <?php if ($contact_phone) : ?>
                            <p><a href="tel:<?php echo esc_attr($contact_phone); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Social Links -->
                    <!-- DEBUG: Instagram: <?php echo $instagram ? 'YES' : 'NO'; ?> | Facebook: <?php echo $facebook ? 'YES' : 'NO'; ?> | Twitter: <?php echo $twitter ? 'YES' : 'NO'; ?> -->
                    <?php if ($instagram || $facebook || $twitter) : ?>
                    <div class="producer-connect__col producer-connect__col--social">
                        <div class="producer-connect__social">
                            <?php if ($instagram) : ?>
                                <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                    Instagram
                                </a>
                            <?php endif; ?>
                            <?php if ($facebook) : ?>
                                <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                    Facebook
                                </a>
                            <?php endif; ?>
                            <?php if ($twitter) : ?>
                                <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                                    Twitter
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Map -->
                    <?php if ($latitude && $longitude) : ?>
                    <div class="producer-connect__map">
                        <iframe 
                            width="100%" 
                            height="200" 
                            frameborder="0" 
                            scrolling="no" 
                            marginheight="0" 
                            marginwidth="0" 
                            src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo esc_attr($longitude - 0.05); ?>%2C<?php echo esc_attr($latitude - 0.05); ?>%2C<?php echo esc_attr($longitude + 0.05); ?>%2C<?php echo esc_attr($latitude + 0.05); ?>&amp;layer=mapnik&amp;marker=<?php echo esc_attr($latitude); ?>%2C<?php echo esc_attr($longitude); ?>" 
                            style="border-radius: var(--border-radius);">
                        </iframe>
                    </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </section>
        <?php endif; ?>
        
    </article>
    
    <!-- Story Toggle JavaScript -->
    <script>
    (function() {
        const content = document.getElementById('producer-story-content');
        const toggle = document.getElementById('story-toggle');
        
        if (!content || !toggle) return;
        
        // Check if content is taller than max-height
        if (content.scrollHeight <= 320) {
            toggle.style.display = 'none';
            return;
        }
        
        toggle.addEventListener('click', function() {
            content.classList.toggle('is-expanded');
            toggle.classList.toggle('is-expanded');
            
            const isExpanded = content.classList.contains('is-expanded');
            toggle.setAttribute('aria-expanded', isExpanded);
            toggle.querySelector('.story__toggle-text').textContent = 
                isExpanded ? 'Show less' : 'Read the full story';
        });
    })();
    </script>
    
    <?php
endwhile;

get_footer();
