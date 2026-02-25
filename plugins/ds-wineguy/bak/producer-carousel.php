<?php
/**
 * Producer Carousel Template
 * 
 * Template for displaying producer carousel
 * Located in: ds-wineguy/templates/producer-carousel.php
 * 
 * Can be overridden by copying to:
 * your-theme/ds-wineguy/producer-carousel.php
 * 
 * @var string $country         Country slug to filter (optional)
 * @var bool   $autoplay       Enable autoplay
 * @var int    $autoplay_delay  Delay in milliseconds
 * @var bool   $randomize       Randomize order
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Build query args
$query_args = array(
    'post_type' => 'dswg_producer',  // CORRECTED
    'posts_per_page' => -1,
    'post_status' => 'publish'
);

// Add country filter if specified
if (!empty($country)) {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'dswg_country',  // CORRECTED
            'field' => 'slug',
            'terms' => $country
        )
    );
}

// Randomize or alphabetize
if ($randomize) {
    $query_args['orderby'] = 'rand';
} else {
    $query_args['orderby'] = 'title';
    $query_args['order'] = 'ASC';
}

// Allow filtering of query args
$query_args = apply_filters('ds_wineguy_carousel_query_args', $query_args, $country);

// Execute query
$producers = new WP_Query($query_args);

// Don't display if no producers found
if (!$producers->have_posts()) {
    return;
}

// Generate unique ID for this carousel instance
$carousel_id = 'ds-producer-carousel-' . uniqid();

// Allow customization of carousel classes
$carousel_classes = apply_filters('ds_wineguy_carousel_classes', array(
    'ds-producer-carousel-wrapper'
), $country);
?>

<div class="<?php echo esc_attr(implode(' ', $carousel_classes)); ?>">
    <div class="ds-producer-carousel swiper" 
         id="<?php echo esc_attr($carousel_id); ?>" 
         data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
         data-autoplay-delay="<?php echo esc_attr($autoplay_delay); ?>">
         
        <div class="swiper-wrapper">
            <?php 
            while ($producers->have_posts()) : 
                $producers->the_post(); 
                
                // Get producer data
                $producer_id = get_the_ID();
                $producer_title = get_the_title();
                $producer_permalink = get_permalink();
                $producer_image = get_the_post_thumbnail_url($producer_id, 'large');
                $producer_logo = get_post_meta($producer_id, 'producer_logo', true);
                
                // Get country
                $countries = get_the_terms($producer_id, 'country');
                $country_name = $countries && !is_wp_error($countries) ? $countries[0]->name : '';
                
                // Allow filtering of card data
                $card_data = apply_filters('ds_wineguy_carousel_card_data', array(
                    'id' => $producer_id,
                    'title' => $producer_title,
                    'permalink' => $producer_permalink,
                    'image' => $producer_image,
                    'logo' => $producer_logo,
                    'country' => $country_name
                ), $producer_id);
            ?>
            
            <div class="swiper-slide">
                <div class="ds-producer-card">
                    <!-- Background image -->
                    <div class="ds-producer-card__image" 
                         style="background-image: url('<?php echo esc_url($card_data['image']); ?>');"
                         role="img"
                         aria-label="<?php echo esc_attr($card_data['title']); ?> vineyard">
                        
                        <!-- White logo overlay -->
                        <?php if ($card_data['logo']) : ?>
                        <div class="ds-producer-card__logo">
                            <img src="<?php echo esc_url($card_data['logo']); ?>" 
                                 alt="<?php echo esc_attr($card_data['title']); ?> logo"
                                 loading="lazy">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Dark bottom overlay with text -->
                        <div class="ds-producer-card__info">
                            <?php if ($card_data['country']) : ?>
                            <span class="ds-producer-card__country">
                                <?php echo esc_html($card_data['country']); ?>
                            </span>
                            <?php endif; ?>
                            
                            <h3 class="ds-producer-card__name">
                                <?php echo esc_html($card_data['title']); ?>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Clickable overlay link -->
                    <a href="<?php echo esc_url($card_data['permalink']); ?>" 
                       class="ds-producer-card__link" 
                       aria-label="View <?php echo esc_attr($card_data['title']); ?> details"></a>
                </div>
            </div>
            
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        
        <!-- Navigation arrows -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        
        <!-- Pagination dots -->
        <div class="swiper-pagination"></div>
    </div>
</div>
