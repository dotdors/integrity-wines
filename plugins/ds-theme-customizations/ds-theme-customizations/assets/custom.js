/**
 * Integrity Wines - Custom JavaScript
 * Minimal JS - CSS-first approach
 */

(function($) {
  'use strict';
  
  // Document ready
  $(document).ready(function() {
    
    // Feature detection for CSS support
    detectCSSFeatures();
    
    // Initialize components
    initMobileMenu();
    initFilters();
    
    console.log('Integrity Wines JS loaded');
  });
  
  /**
   * Detect CSS feature support
   * Adds classes to <html> for progressive enhancement
   */
  function detectCSSFeatures() {
    const html = document.documentElement;
    
    // Test for container query support
    if (CSS.supports('container-type', 'inline-size')) {
      html.classList.add('supports-container-queries');
    }
    
    // Test for :has() selector support
    try {
      document.querySelector(':has(*)');
      html.classList.add('supports-has-selector');
    } catch (e) {
      // :has() not supported
    }
  }
  
  /**
   * Mobile menu toggle
   * Minimal JS - CSS handles animation
   */
  function initMobileMenu() {
    const toggle = $('.menu-toggle');
    const nav = $('.main-navigation');
    
    if (toggle.length && nav.length) {
      toggle.on('click', function(e) {
        e.preventDefault();
        nav.toggleClass('is-open');
        $(this).attr('aria-expanded', function(i, attr) {
          return attr === 'true' ? 'false' : 'true';
        });
      });
      
      // Close on ESC key
      $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && nav.hasClass('is-open')) {
          nav.removeClass('is-open');
          toggle.attr('aria-expanded', 'false');
        }
      });
    }
  }
  
  /**
   * Filter functionality
   * Placeholder - will be enhanced
   */
  function initFilters() {
    // Filter form submission will be handled here
    // For now, let standard form submission work
  }
  
})(jQuery);
