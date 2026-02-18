/**
 * Dandysite Jane - Header Behavior
 * Vanilla JS — no jQuery dependency.
 *
 * Reads config from dspHeader (localized via PHP):
 *   dspHeader.style          'overlay' | 'solid'
 *   dspHeader.scrollReveal   'solid' | 'transparent'
 *   dspHeader.navBreakpoint  px value (e.g. 1024)
 *   dspHeader.scrollThreshold px before hiding (e.g. 80)
 */

(function () {
    'use strict';

    const config = window.dspHeader || {
        style:           'solid',
        scrollReveal:    'solid',
        navBreakpoint:   1024,
        scrollThreshold: 80,
    };

    // ================================================================
    // STATE
    // ================================================================
    let lastScrollY     = window.scrollY;
    let rafPending      = false;
    let mobileMenuOpen  = false;
    let header          = null;
    let menuToggle      = null;
    let nav             = null;

    // ================================================================
    // INIT
    // ================================================================
    function init() {
        header     = document.querySelector('.site-header');
        menuToggle = document.querySelector('.header-hamburger');
        nav        = document.querySelector('.header-nav');

        if (!header) return;

        // Set initial nav visibility based on breakpoint
        updateNavMode();

        // Scroll behavior
        window.addEventListener('scroll', onScroll, { passive: true });

        // Resize — re-evaluate nav mode and close mobile menu if now desktop
        window.addEventListener('resize', onResize, { passive: true });

        // Hamburger toggle
        if (menuToggle) {
            menuToggle.addEventListener('click', toggleMobileMenu);
        }

        // Close mobile menu on nav link click
        if (nav) {
            nav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
        }

        // Close mobile menu on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileMenuOpen) {
                closeMobileMenu();
            }
        });

        // Run once immediately to set correct initial state
        applyScrollState();
    }

    // ================================================================
    // SCROLL HANDLER — throttled via rAF
    // ================================================================
    function onScroll() {
        if (!rafPending) {
            rafPending = true;
            requestAnimationFrame(function () {
                applyScrollState();
                rafPending = false;
            });
        }
    }

    function applyScrollState() {
        const currentY   = window.scrollY;
        const threshold  = config.scrollThreshold;
        const scrollingDown = currentY > lastScrollY;

        // --- Has the user scrolled past the threshold? ---
        if (currentY > threshold) {
            header.classList.add('header--scrolled');
        } else {
            // Back near top — restore to initial state
            header.classList.remove('header--scrolled');
            header.classList.remove('header--hidden');
            header.classList.remove('header--revealed');
            lastScrollY = currentY;
            return;
        }

        // --- Hide on scroll down, reveal on scroll up ---
        if (scrollingDown) {
            // Only hide if not already hidden and menu is closed
            if (!mobileMenuOpen) {
                header.classList.add('header--hidden');
                header.classList.remove('header--revealed');
            }
        } else {
            // Scrolling up — reveal
            if (header.classList.contains('header--hidden')) {
                header.classList.remove('header--hidden');
                header.classList.add('header--revealed');

                // Apply scroll-reveal style
                if (config.scrollReveal === 'transparent') {
                    header.classList.add('header--reveal-transparent');
                    header.classList.remove('header--reveal-solid');
                } else {
                    header.classList.add('header--reveal-solid');
                    header.classList.remove('header--reveal-transparent');
                }
            }
        }

        lastScrollY = currentY;
    }

    // ================================================================
    // NAV MODE (hamburger vs full)
    // ================================================================
    function updateNavMode() {
        const isMobile = window.innerWidth <= config.navBreakpoint;
        header.classList.toggle('header--mobile-nav', isMobile);
        header.classList.toggle('header--desktop-nav', !isMobile);

        // If switching to desktop, close any open mobile menu
        if (!isMobile && mobileMenuOpen) {
            closeMobileMenu();
        }

        // Update hamburger visibility via aria
        if (menuToggle) {
            menuToggle.setAttribute('aria-hidden', String(!isMobile));
        }
    }

    function onResize() {
        updateNavMode();
    }

    // ================================================================
    // MOBILE MENU
    // ================================================================
    function toggleMobileMenu() {
        mobileMenuOpen ? closeMobileMenu() : openMobileMenu();
    }

    function openMobileMenu() {
        mobileMenuOpen = true;
        header.classList.add('mobile-menu-open');
        document.body.classList.add('mobile-menu-open');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'true');
        if (nav) nav.setAttribute('aria-hidden', 'false');
    }

    function closeMobileMenu() {
        mobileMenuOpen = false;
        header.classList.remove('mobile-menu-open');
        document.body.classList.remove('mobile-menu-open');
        if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
        if (nav) nav.setAttribute('aria-hidden', 'true');
    }

    // ================================================================
    // START
    // ================================================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
