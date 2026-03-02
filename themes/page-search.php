<?php
/**
 * Template Name: Search Page
 * Template for displaying the search form
 */

get_header(); 
?>

<div class="search-page">
    <div class="search-hero">
        <div class="container">
            <div class="search-content">
                
                <h1 class="search-title">Search</h1>
                <p class="search-subtitle">Find videos, productions, and more</p>
                
                <div class="search-form-wrapper">
                    <form role="search" method="get" class="search-form-enhanced" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="search-input-container">
                            <input 
                                type="search" 
                                class="search-input-minimal" 
                                placeholder="Search for videos, productions..." 
                                value="<?php echo get_search_query(); ?>" 
                                name="s" 
                                autocomplete="off"
                            />
                            <button type="submit" class="search-button-minimal" aria-label="Search">
                                <svg width="24" height="24" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="none">
                                    <path fill="currentColor" fill-rule="evenodd" d="M4 9a5 5 0 1110 0A5 5 0 014 9zm5-7a7 7 0 104.2 12.6.999.999 0 00.093.107l3 3a1 1 0 001.414-1.414l-3-3a.999.999 0 00-.107-.093A7 7 0 009 2z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                
                <?php 
                // Get some recent videos or popular categories for suggestions
                $recent_videos = get_posts([
                    'post_type' => 'video',
                    'posts_per_page' => 5,
                    'post_status' => 'publish'
                ]);
                
                $video_categories = get_terms([
                    'taxonomy' => 'video_category',
                    'hide_empty' => true,
                    'number' => 6
                ]);
                ?>
                
                <?php if ($recent_videos || $video_categories) : ?>
                <div class="search-suggestions-grid">
                    
                    <?php if ($video_categories) : ?>
                    <div class="search-section-column">
                        <h3>Browse by Category</h3>
                        <div class="search-category-list">
                            <?php foreach ($video_categories as $category) : ?>
                                <a href="<?php echo get_term_link($category); ?>" class="search-category-link">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($recent_videos) : ?>
                    <div class="search-section-column">
                        <h3>Recent Videos</h3>
                        <ul class="search-video-list">
                            <?php foreach ($recent_videos as $video) : 
                                $thumbnail = get_post_meta($video->ID, '_ovp_video_thumbnail', true);
                                $duration = get_post_meta($video->ID, '_ovp_video_duration', true);
                            ?>
                                <li class="search-video-item">
                                    <a href="<?php echo get_permalink($video->ID); ?>" class="search-video-link">
                                        <?php if ($thumbnail) : ?>
                                            <div class="search-video-thumb">
                                                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($video->post_title); ?>" loading="lazy">
                                                <?php if ($duration) : ?>
                                                    <span class="search-video-duration"><?php echo gmdate('i:s', $duration); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="search-video-title"><?php echo esc_html($video->post_title); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
