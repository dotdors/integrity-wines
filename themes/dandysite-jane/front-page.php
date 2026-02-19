<?php
/**
 * Front Page Template
 * Delegates to the shared hero partial.
 *
 * Because front-page.php always takes priority in the WP template hierarchy,
 * page template assignments are ignored here by default. We read the assigned
 * template manually and map it to a hero layout so the Page Attributes
 * template dropdown works as expected on the homepage too.
 *
 * Hero image:   Featured Image on this page.
 * Hero content: Hero Section meta box in the page editor.
 * Logos:        Appearance → Site Identity.
 */

$template_map = [
    'page-templates/page-hero-fullbleed.php'   => 'fullbleed',
    'page-templates/page-hero-split-left.php'  => 'split-left',
    'page-templates/page-hero-split-right.php' => 'split-right',
];

$assigned = get_page_template_slug( get_option( 'page_on_front' ) );
$layout   = $template_map[ $assigned ] ?? 'fullbleed';

get_header();
?>

<?php get_template_part( 'template-parts/hero', null, [ 'layout' => $layout ] ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="front-page-content">
        <?php the_content(); ?>
    </div>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
