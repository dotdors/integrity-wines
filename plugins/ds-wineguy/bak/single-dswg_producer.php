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
    
    // Get meta fields
    $location = get_post_meta(get_the_ID(), 'dswg_location', true);
    $short_desc = get_post_meta(get_the_ID(), 'dswg_short_desc', true);
    $highlights = get_post_meta(get_the_ID(), 'dswg_highlights', true);
    $logo_id = get_post_meta(get_the_ID(), 'dswg_producer_logo', true);
    
    // Get featured image
    $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    
    ?>
    
    <!-- Producer Hero -->
    <div class="producer-hero">
        <?php if ($hero_image) : ?>
            <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="producer-hero__image">
            <div class="producer-hero__overlay"></div>
        <?php endif; ?>
        
        <?php if ($logo_id) : ?>
            <?php echo wp_get_attachment_image($logo_id, 'full', false, ['class' => 'producer-hero__logo']); ?>
        <?php endif; ?>
    </div>
    
    <!-- Producer Name & Location -->
    <div class="producer-identity">
        <div class="container" style="max-width: var(--content-max-width);">
            <h1 class="producer-identity__name"><?php the_title(); ?></h1>
            <?php if ($location) : ?>
                <p class="producer-identity__location"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <article id="post-<?php the_ID(); ?>" <?php post_class('producer-single'); ?>>
        
        <!-- Short Description & Highlights -->
        <?php if ($short_desc || $highlights) : ?>
        <section class="section">
            <div class="container" style="max-width: var(--content-max-width);">
                
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
        </section>
        <?php endif; ?>
        
        <!-- The Story (with gallery) -->
        <?php if (get_the_content()) : ?>
        <section class="section section--alt">
            <div class="container">
                <div class="producer-story" style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--spacing-xl); max-width: var(--container-max-width); margin: 0 auto;">
                    
                    <!-- Story Content (left) -->
                    <div class="story">
                        <h2>The Story</h2>
                        
                        <div class="story__body" id="producer-story-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <button type="button" class="story__toggle" id="story-toggle" aria-expanded="false">
                            <span class="story__toggle-text">Read the full story</span>
                        </button>
                    </div>
                    
                    <!-- Photo Grid (right) -->
                    <div class="producer-photos">
                        <?php 
                        // Display gallery if available
                        $gallery_ids = get_post_meta(get_the_ID(), 'dswg_gallery_ids', true);
                        if ($gallery_ids) :
                            $gallery_array = explode(',', $gallery_ids);
                            $gallery_array = array_slice($gallery_array, 0, 4); // Max 4 for 2x2 grid
                            
                            if (count($gallery_array) > 0) :
                            ?>
                            <div class="photo-grid photo-grid--2x2">
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
        
        if ($wines->have_posts()) :
        ?>
        <section class="section">
            <div class="container" style="max-width: var(--container-max-width);">
                
                <div class="section-header">
                    <h2 class="section-header__title">The Wines</h2>
                </div>
                
                <div class="wine-grid">
                    <?php while ($wines->have_posts()) : $wines->the_post(); ?>
                        
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
        <?php endif; ?>
        
        <!-- Contact & Social (optional section) -->
        <?php 
        $contact_email = get_post_meta(get_the_ID(), 'dswg_contact_email', true);
        $contact_phone = get_post_meta(get_the_ID(), 'dswg_contact_phone', true);
        $website = get_post_meta(get_the_ID(), 'dswg_website', true);
        $instagram = get_post_meta(get_the_ID(), 'dswg_instagram', true);
        $facebook = get_post_meta(get_the_ID(), 'dswg_facebook', true);
        $twitter = get_post_meta(get_the_ID(), 'dswg_twitter', true);
        
        if ($contact_email || $contact_phone || $website || $instagram || $facebook || $twitter) :
        ?>
        <section class="section section--alt">
            <div class="container" style="max-width: var(--content-max-width); text-align: center;">
                <h2>Connect With <?php the_title(); ?></h2>
                
                <div class="producer-contact" style="margin-top: var(--spacing-lg);">
                    <?php if ($website) : ?>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" class="button">
                            Visit Website
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($instagram || $facebook || $twitter) : ?>
                    <div class="producer-social" style="justify-content: center; margin-top: var(--spacing-md);">
                        <?php if ($instagram) : ?>
                            <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                <span class="dashicons dashicons-instagram"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($facebook) : ?>
                            <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                <span class="dashicons dashicons-facebook"></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($twitter) : ?>
                            <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                                <span class="dashicons dashicons-twitter"></span>
                            </a>
                        <?php endif; ?>
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
