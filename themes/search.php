<?php
/**
 * Template for displaying search results
 * Matches taxonomy template structure
 */

get_header(); 
?>

<div class="ovp-single-video-page">
    <div class="ovp-title-section">
        <div class="ovp-container">
            <?php if (have_posts()) : ?>
                <h1 class="ovp-video-title">
                    <?php printf(__('Search Results for: %s', 'dandysite-jane'), '<span>' . get_search_query() . '</span>'); ?>
                </h1>
                <p style="text-align: center; color: var(--ovp-color-text-light);">
                    <?php echo esc_html(sprintf(_n('%s result found', '%s results found', $wp_query->found_posts, 'dandysite-jane'), number_format_i18n($wp_query->found_posts))); ?>
                </p>
            <?php else : ?>
                <h1 class="ovp-video-title"><?php _e('No Results Found', 'dandysite-jane'); ?></h1>
            <?php endif; ?>
        </div>
    </div>

    <?php if (have_posts()) : ?>
        <div class="ovp-video-section">
            <div class="ovp-container">
                <div class="ovp-video-grid">
                    <?php while (have_posts()) : the_post(); 
                        // Get video thumbnail if this is a video post type
                        $video_thumbnail = '';
                        if (get_post_type() === 'video') {
                            $video_thumbnail = get_post_meta(get_the_ID(), '_ovp_video_thumbnail', true);
                        }
                    ?>
                        <article <?php post_class('ovp-video-card'); ?>>
                            <a href="<?php the_permalink(); ?>" class="ovp-video-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="ovp-video-thumbnail">
                                        <?php the_post_thumbnail('large'); ?>
                                    </div>
                                <?php elseif ($video_thumbnail) : ?>
                                    <div class="ovp-video-thumbnail">
                                        <img src="<?php echo esc_url($video_thumbnail); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="ovp-video-info">
                                    <span class="ovp-badge">
                                        <?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name); ?>
                                    </span>
                                    <h3 class="ovp-video-title-text"><?php the_title(); ?></h3>
                                    <?php if (has_excerpt()) : ?>
                                        <p class="ovp-video-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <div class="ovp-pagination">
                    <?php
                    the_posts_pagination([
                        'mid_size' => 2,
                        'prev_text' => __('&laquo; Previous', 'dandysite-jane'),
                        'next_text' => __('Next &raquo;', 'dandysite-jane'),
                    ]);
                    ?>
                </div>
            </div>
        </div>

    <?php else : ?>
        <div class="ovp-video-section">
            <div class="ovp-container" style="text-align: center; padding: 4rem 2rem;">
                <span style="font-size: 5rem; display: block; margin-bottom: 2rem; opacity: 0.5;">🔍</span>
                <h2><?php _e('Nothing Found', 'dandysite-jane'); ?></h2>
                <p><?php _e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'dandysite-jane'); ?></p>
                
                <div style="max-width: 400px; margin: 2rem auto;">
                    <?php get_search_form(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
