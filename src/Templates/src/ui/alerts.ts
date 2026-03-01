/**
 * Alert System
 * Simple HTML alert close functionality
 */

/**
 * Close alert by ID with fade animation
 */
export function closeAlert(alertId: string): void {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.classList.add("fade-out");
        setTimeout(() => alert.remove(), 300);
    }
}

/**
 * Initialize alerts - expose closeAlert to window
 */
export function initAlerts(): void {
    (window as any).closeAlert = closeAlert;
}
