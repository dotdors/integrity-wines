<?php
/**
 * Template Name: Overlay Header
 * Template Post Type: post, page
 *
 * Use this template on any page or post where you want the
 * transparent overlay header (header overlays the hero image).
 *
 * Sets header style to 'overlay' regardless of the default setting.
 */

get_header();
?>

<div id="primary" class="page-content">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/content', 'page');
    endwhile;
    ?>
</div>

<?php get_footer(); ?>
