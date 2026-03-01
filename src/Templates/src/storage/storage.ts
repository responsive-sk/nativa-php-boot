/**
 * Safe localStorage wrapper with error handling
 * Provides graceful degradation when localStorage is unavailable
 */

/**
 * Safely get item from storage
 */
export function safeGetItem(key: string): string | null {
    try {
        return localStorage.getItem(key);
    } catch (e) {
        console.warn("localStorage not available:", e);
        return null;
    }
}

/**
 * Safely set item in storage
 */
export function safeSetItem(key: string, value: string): void {
    try {
        localStorage.setItem(key, value);
    } catch (e) {
        console.warn("localStorage not available:", e);
    }
}

/**
 * Safely remove item from storage
 */
export function safeRemoveItem(key: string): void {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.warn("localStorage not available:", e);
    }
}

/**
 * Safely clear all storage
 */
export function safeClear(): void {
    try {
        localStorage.clear();
    } catch (e) {
        console.warn("localStorage not available:", e);
    }
}
