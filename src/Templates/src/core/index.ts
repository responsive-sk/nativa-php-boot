/**
 * Core Module Exports
 * Essential utilities used throughout the application
 */

// Storage utilities
export {
    safeGetItem,
    safeSetItem,
    safeRemoveItem,
    safeClear,
} from "../storage/storage.js";

// Cookie management
export {
    CookieManager,
    type CookiePreferences,
    type UserInfo,
} from "../storage/cookieManager.js";

// CSRF Manager (if exists, otherwise create stub)
export { CsrfManager } from "./csrf.js";
