/**
 * Storage Module Exports
 * localStorage utilities and cookie management
 */

// Storage utilities
export {
    safeGetItem,
    safeSetItem,
    safeRemoveItem,
    safeClear,
} from "./storage.js";

// Cookie management
export {
    CookieManager,
    type CookiePreferences,
    type UserInfo,
} from "./cookieManager.js";

// Cookie consent UI
export {
    initCookieConsent,
    updateGreeting,
    showCookieConsent,
    removeCookieConsent,
} from "../components/cookieConsent.js";
