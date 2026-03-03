/**
 * App - Core JavaScript Entry Point
 * ONLY truly shared utilities (loaded on EVERY page)
 *
 * Keep this minimal for maximum performance!
 * Page-specific features go in pages/*.ts
 *
 * Dependencies:
 * - NO 3rd party libs in core (vanilla JS only)
 */

// Core utilities
import { CsrfManager } from "@core/index.js";

// UI - ONLY essential components
import {
  notifications,
  initHtmxNotifications,
  initTheme,
  initThemeToggle,
  initAlerts,
} from "@ui/index.js";

// Navigation - essential for all pages
import { initSmoothScroll, initMobileMenu } from "@navigation/index.js";

// Effects - anim blocks for text reveal
import { initAnimBlocks } from "@effects/animBlock.js";

console.log("%cCORE LOADING...", "color: #d4af37; font-size: 16px; font-weight: bold");

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
 * Initialize CORE features only
 * Page-specific features are initialized in their own entry points
 */
function initCore(): void {
  // Theme (load first to prevent flash)
  initTheme();
  initThemeToggle();

  // UI - essential only
  initAlerts();

  // Navigation - essential for all pages
  initSmoothScroll();
  initMobileMenu();

  // Notifications
  initHtmxNotifications();

  // Effects - text reveal animations
  initAnimBlocks();

  // Make globals available
  (window as any).notifications = notifications;
}

document.addEventListener("DOMContentLoaded", () => {
  initCore();
  debugAssets();
  console.log("%c✅ CORE READY", "color: #10b981; font-size: 16px; font-weight: bold");
});
