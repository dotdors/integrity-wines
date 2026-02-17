/**
 * DS WineGuy - Producer Carousel Initialization
 * 
 * Plugin: ds-wineguy
 * File: assets/js/producer-carousel.js
 * 
 * Initializes Swiper.js carousels for producer displays
 * Supports autoplay, responsive breakpoints, and accessibility
 */

(function() {
    'use strict';
    
    /**
     * Initialize all DS WineGuy producer carousels on the page
     */
    function initDSProducerCarousels() {
        const carousels = document.querySelectorAll('.ds-producer-carousel');
        
        if (!carousels.length) {
            return;
        }
        
        carousels.forEach(function(carousel) {
            const autoplayEnabled = carousel.dataset.autoplay === 'true';
            const autoplayDelay = parseInt(carousel.dataset.autoplayDelay) || 5000;
            
            // Base configuration
            const config = {
                // Responsive breakpoints
                slidesPerView: 1,
                spaceBetween: 20,
                
                // Loop behavior
                loop: false,
                
                // Navigation
                navigation: {
                    nextEl: carousel.querySelector('.swiper-button-next'),
                    prevEl: carousel.querySelector('.swiper-button-prev'),
                },
                
                // Pagination
                pagination: {
                    el: carousel.querySelector('.swiper-pagination'),
                    clickable: true,
                    dynamicBullets: true,
                    dynamicMainBullets: 5,
                },
                
                // Responsive breakpoints
                breakpoints: {
                    // Mobile landscape / small tablet
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    // Tablet / small desktop
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                    // Desktop
                    1280: {
                        slidesPerView: 4,
                        spaceBetween: 30,
                    },
                    // Large desktop
                    1600: {
                        slidesPerView: 5,
                        spaceBetween: 30,
                    }
                },
                
                // Keyboard control
                keyboard: {
                    enabled: true,
                    onlyInViewport: true,
                },
                
                // Mouse wheel control
                mousewheel: {
                    forceToAxis: true,
                },
                
                // Smooth transitions
                speed: 600,
                
                // Grab cursor
                grabCursor: true,
                
                // Accessibility
                a11y: {
                    enabled: true,
                    prevSlideMessage: 'Previous producer',
                    nextSlideMessage: 'Next producer',
                    paginationBulletMessage: 'Go to producer {{index}}',
                }
            };
            
            // Add autoplay if enabled
            if (autoplayEnabled) {
                config.autoplay = {
                    delay: autoplayDelay,
                    disableOnInteraction: false, // Continue autoplay after user interaction
                    pauseOnMouseEnter: true, // Pause when hovering
                };
            }
            
            // Allow theme customization via JS filter
            if (typeof window.dsWineguyCarouselConfig === 'function') {
                const customConfig = window.dsWineguyCarouselConfig(config, carousel);
                if (customConfig) {
                    Object.assign(config, customConfig);
                }
            }
            
            // Initialize Swiper
            const swiper = new Swiper(carousel, config);
            
            // Store reference for potential theme access
            carousel.swiperInstance = swiper;
            
            // Optional: Debug logging
            if (window.location.search.includes('debug') || window.dsWineguyDebug) {
                console.log('DS WineGuy Producer Carousel initialized:', {
                    id: carousel.id,
                    autoplay: autoplayEnabled,
                    delay: autoplayDelay,
                    slides: swiper.slides.length
                });
            }
            
            // Pause autoplay when tab is not visible (performance)
            if (autoplayEnabled) {
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        swiper.autoplay.stop();
                    } else {
                        swiper.autoplay.start();
                    }
                });
            }
            
            // Fire custom event for theme hooks
            const event = new CustomEvent('dsWineguyCarouselInit', {
                detail: { carousel: carousel, swiper: swiper }
            });
            document.dispatchEvent(event);
        });
    }
    
    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDSProducerCarousels);
    } else {
        // DOM already loaded
        initDSProducerCarousels();
    }
    
})();

/**
 * THEME CUSTOMIZATION HOOKS
 * 
 * Themes can customize carousel behavior via JavaScript:
 * 
 * 1. Modify configuration before initialization:
 * 
 * window.dsWineguyCarouselConfig = function(config, carouselElement) {
 *     // Modify config object
 *     config.speed = 800; // Slower transitions
 *     config.breakpoints[1280].slidesPerView = 5; // More slides on desktop
 *     return config;
 * };
 * 
 * 
 * 2. React to carousel initialization:
 * 
 * document.addEventListener('dsWineguyCarouselInit', function(e) {
 *     const carousel = e.detail.carousel;
 *     const swiper = e.detail.swiper;
 *     console.log('Carousel initialized with', swiper.slides.length, 'slides');
 * });
 * 
 * 
 * 3. Access Swiper instance after initialization:
 * 
 * const carousel = document.querySelector('.ds-producer-carousel');
 * if (carousel && carousel.swiperInstance) {
 *     carousel.swiperInstance.slideTo(3); // Go to slide 3
 *     carousel.swiperInstance.autoplay.stop(); // Stop autoplay
 * }
 */
