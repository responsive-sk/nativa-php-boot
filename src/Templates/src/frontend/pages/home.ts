/**
 * Homepage JavaScript - Page-Specific Features
 * 
 * This file is loaded ONLY on the homepage.
 * Keep it lean - only homepage-specific features!
 * 
 * Dependencies:
 * - NO GSAP (use CSS animations instead)
 * - Vanilla JS for interactions
 */

import './home.css';
import './home-animations.css';

// Homepage-specific: Cookie consent, gallery, card interactions
import { initCookieConsent, initGalleryLightbox, initCardInteractions } from '@components/index.js';

// Homepage-specific: Gold text effect, parallax
import { initGoldTextEffect, initParallax } from '@effects/index.js';

console.log('%c🏠 HOMEPAGE LOADING...', 'color: #d4af37; font-size: 14px; font-weight: bold');

/**
 * Initialize homepage-specific features
 * Using CSS animations instead of GSAP for performance
 */
function initHomepage(): void {
  // Initialize homepage-specific components
  initCookieConsent();
  initGalleryLightbox();
  initCardInteractions();
  initGoldTextEffect();
  initParallax();

  // Initialize scroll-triggered animations
  initScrollAnimations();
}

/**
 * Initialize scroll-triggered animations using IntersectionObserver
 */
function initScrollAnimations(): void {
  const animatedElements = document.querySelectorAll('[data-animate]');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px',
  });

  animatedElements.forEach((el) => observer.observe(el));
}

// Smooth scroll for anchor links
document.addEventListener('DOMContentLoaded', () => {
  initHomepage();
  console.log('%c✅ HOMEPAGE READY', 'color: #10b981; font-size: 14px; font-weight: bold');

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href && href !== '#') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
          });
        }
      }
    });
  });
});
