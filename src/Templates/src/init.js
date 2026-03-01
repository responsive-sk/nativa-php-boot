/**
 * Theme Initialization
 * Must load before CSS to prevent flash of unstyled content
 */

(function() {
  'use strict';

  var savedTheme = localStorage.getItem('theme') || 'dark';
  document.documentElement.setAttribute('data-theme', savedTheme);

  // Set basic CSS for mobile menu (full initialization in app.js)
  function initMobileNavStyles() {
    var menu = document.querySelector('.mobile-menu');
    if (!menu) return;

    menu.style.cssText =
      'position: fixed !important;' +
      'top: 60px !important;' +
      'left: 0 !important;' +
      'right: 0 !important;' +
      'bottom: 0 !important;' +
      'z-index: 1000 !important;' +
      'overflow-y: auto !important;' +
      '-webkit-overflow-scrolling: touch !important;' +
      'transform: translateX(-100%) !important;' +
      'transition: transform 250ms ease, opacity 250ms ease !important;' +
      'will-change: transform, opacity !important;' +
      'pointer-events: none !important;' +
      'visibility: visible !important;' +
      'opacity: 1 !important;';

    // Escape key handler
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && menu.classList.contains('active')) {
        menu.classList.remove('active');
        menu.style.transform = 'translateX(-100%)';
        menu.style.opacity = '0';
        menu.style.pointerEvents = 'none';
        var btn = document.querySelector('.mobile-menu-btn');
        if (btn) {
          btn.setAttribute('aria-expanded', 'false');
          btn.setAttribute('aria-label', 'Open menu');
        }
        document.body.style.overflow = '';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileNavStyles);
  } else {
    initMobileNavStyles();
  }

})();
