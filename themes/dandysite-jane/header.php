<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e('Skip to content', 'dandysite-jane'); ?></a>

<div id="page" class="site">

    <header id="masthead" class="site-header" role="banner">
        <div class="header-inner">

            <!-- Logo -->
            <div class="header-logo">
                <?php dsp_display_header_logo(); ?>
            </div>

            <!-- Desktop / Mobile Navigation -->
            <nav id="site-navigation" class="header-nav" aria-label="<?php esc_attr_e('Primary navigation', 'dandysite-jane'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'menu',
                    'container'      => false,
                    'fallback_cb'    => 'dsp_fallback_menu',
                ]);
                ?>
            </nav>

            <!-- Hamburger Toggle -->
            <button class="header-hamburger"
                    aria-controls="site-navigation"
                    aria-expanded="false"
                    aria-label="<?php esc_attr_e('Toggle menu', 'dandysite-jane'); ?>">
                <span class="hamburger-bar" aria-hidden="true"></span>
                <span class="hamburger-bar" aria-hidden="true"></span>
                <span class="hamburger-bar" aria-hidden="true"></span>
            </button>

        </div><!-- .header-inner -->
    </header><!-- #masthead -->

    <div class="header-spacer" aria-hidden="true"></div>

    <main id="primary" class="site-main">
