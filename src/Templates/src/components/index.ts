/**
 * JavaScript Components Entry Point
 * Exports all reusable UI components
 */

// Cookie Consent Component
export {
  CookieManager,
  type CookiePreferences,
  type UserInfo,
} from "../storage/cookieManager.js";
export {
  initCookieConsent,
  updateGreeting,
  showCookieConsent,
  removeCookieConsent,
} from "../components/cookieConsent.js";

// Lit-based Components (if using Lit)
// export { AgCard } from "./ag/Card/core/Card.js";

// Form Components
export { initFormEnhancements } from "../forms/formEnhancements.js";

// UI Components
export {
  notifications,
  NotificationManager,
  initHtmxNotifications,
} from "../ui/notifications.js";
export { showToast } from "../ui/toast.js";
export { initAlerts, closeAlert } from "../ui/alerts.js";

// Interactive Components
export { initGalleryLightbox } from "../ui/galleryLightbox.js";
export { initCardInteractions } from "../ui/cardInteractions.js";
export { initFaqToggle } from "../ui/faqToggle.js";
export { initPricingToggle } from "../ui/pricingToggle.js";

// Theme Component
export { initTheme, initThemeToggle } from "../ui/theme.js";

// Alpine.js Integration
export { setupAlpine, alpineData } from "../ui/alpine.js";
