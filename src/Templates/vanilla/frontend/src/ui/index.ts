/**
 * UI Module Exports
 * User interface components: notifications, alerts, toast
 */

// Notifications
export {
    NotificationManager,
    notifications,
    initHtmxNotifications,
    type NotificationOptions,
} from "./notifications.js";

// Alerts
export { initAlerts, closeAlert } from "./alerts.js";

// Toast
export { showToast, type ToastType, type ToastOptions } from "./toast.js";

// Theme
export { initTheme, initThemeToggle } from "./theme.js";

// FAQ Toggle
export { initFaqToggle } from "./faqToggle.js";

// Pricing Toggle
export { initPricingToggle } from "./pricingToggle.js";

// Gallery Lightbox
export { initGalleryLightbox } from "./galleryLightbox.js";

// Card Interactions
export { initCardInteractions } from "./cardInteractions.js";

// Alpine.js integration
export { setupAlpine, alpineData } from "./alpine.js";
