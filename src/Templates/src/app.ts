/**
 * App - Shared JavaScript Entry Point
 * Imports and initializes all shared utilities
 */

// Core utilities
import { CsrfManager } from "@core/index.js";

// Components
import {
  initCookieConsent,
  updateGreeting,
  initGalleryLightbox,
  initCardInteractions,
  initFaqToggle,
  initPricingToggle,
} from "@components/index.js";

// UI components
import {
  notifications,
  initHtmxNotifications,
  initTheme,
  initThemeToggle,
  setupAlpine,
  initAlerts,
} from "@ui/index.js";

// Navigation
import { initSmoothScroll, initMobileMenu, initNavActive } from "@navigation/index.js";

// Effects
import {
  initGoldTextEffect,
  initScrollAnimations,
  initParallax,
} from "@effects/index.js";

// Forms
import { initFormEnhancements } from "@forms/index.js";

console.log("%cAPP LOADING...", "color: #d4af37; font-size: 16px; font-weight: bold");

/**
 * Debug utility - log loaded assets and DOM state
 */
function debugAssets(): void {
  const styles = Array.from(
    document.querySelectorAll<HTMLLinkElement>('link[rel="stylesheet"]'),
  )
    .map((el) => el.href.split("/").pop())
    .filter(Boolean);

  const scripts = Array.from(
    document.querySelectorAll<HTMLScriptElement>("script[src]"),
  )
    .map((el) => el.src.split("/").pop())
    .filter(Boolean);

  const path = window.location.pathname;
  const page = path === "/" ? "home" : path.replace(/^\//, "").split("/")[0];

  console.log("%c📄 CURRENT PAGE:", "color: #ef4444; font-weight: bold", page, `(${path})`);
  console.log("%c🎨 CSS LOADED:", "color: #10b981; font-weight: bold", styles);
  console.log("%c📜 JS LOADED:", "color: #3b82f6; font-weight: bold", scripts);
}

/**
 * Initialize all application features
 */
function initApp(): void {
  // Theme (load first to prevent flash)
  initTheme();
  initThemeToggle();

  // Core components
  initCookieConsent();
  initGalleryLightbox();
  initCardInteractions();
  initFaqToggle();
  initPricingToggle();

  // UI
  setupAlpine();
  initAlerts();

  // Navigation
  initSmoothScroll();
  initMobileMenu();

  // Effects
  initGoldTextEffect();
  initScrollAnimations();
  initParallax();

  // Forms
  initFormEnhancements();

  // Notifications
  initHtmxNotifications();

  // Make globals available
  (window as any).notifications = notifications;
}

document.addEventListener("DOMContentLoaded", () => {
  initApp();
  debugAssets();
  console.log("%c✅ APP READY", "color: #10b981; font-size: 16px; font-weight: bold");
});
