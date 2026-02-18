<?php
/**
 * Template Name: Solid Header
 * Template Post Type: post, page
 *
 * Use this template on any page or post where you want the
 * solid (opaque) header, overriding any default overlay setting.
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
