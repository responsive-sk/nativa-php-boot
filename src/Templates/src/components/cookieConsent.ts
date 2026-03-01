/**
 * Cookie Consent UI Component
 * Displays and manages the cookie consent modal
 */

import { CookieManager, type CookiePreferences } from "@storage/cookieManager.js";

/**
 * Show cookie consent modal
 */
export function showCookieConsent(): void {
    const consent = CookieManager.getConsent();
    const preferences = consent || {
        essential: true,
        analytics: false,
        marketing: false,
        personalization: false,
    };

    const modal = document.createElement("div");
    modal.className = "cookie-consent-overlay";
    modal.innerHTML = `
    <div class="cookie-consent-modal">
      <div class="cookie-consent-header">
        <h3>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10"/>
            <path d="M12 2a10 10 0 0 0-10 10 10 10 0 0 0 10 10"/>
            <circle cx="8" cy="10" r="1.5"/>
            <circle cx="16" cy="10" r="1.5"/>
            <path d="M8 15h8"/>
          </svg>
          Cookie Preferences
        </h3>
        <p>We use cookies to enhance your experience and personalize your visit.</p>
      </div>

      <div class="cookie-consent-options">
        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" checked disabled>
            <span>Essential</span>
            <small>Required for the site to function</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="analytics" ${preferences.analytics ? "checked" : ""}>
            <span>Analytics</span>
            <small>Help us improve the site</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="marketing" ${preferences.marketing ? "checked" : ""}>
            <span>Marketing</span>
            <small>For personalized ads</small>
          </label>
        </div>

        <div class="cookie-option">
          <label class="cookie-option-label">
            <input type="checkbox" id="personalization" ${preferences.personalization ? "checked" : ""}>
            <span>Personalization</span>
            <small>Remember your preferences</small>
          </label>
        </div>
      </div>

      <div class="cookie-consent-actions">
        <button class="btn btn-outline" onclick="acceptEssential()">Essential Only</button>
        <button class="btn btn-outline" onclick="acceptSelected()">Accept Selected</button>
        <button class="btn btn--primary" onclick="acceptAll()">Accept All</button>
      </div>
    </div>
  `;

    document.body.appendChild(modal);
    document.body.style.overflow = "hidden";

    // Global functions for button handlers
    (window as any).acceptEssential = () => {
        const prefs: CookiePreferences = {
            essential: true,
            analytics: false,
            marketing: false,
            personalization: false,
        };
        CookieManager.setConsent(prefs);
        removeCookieConsent();
    };

    (window as any).acceptSelected = () => {
        const prefs: CookiePreferences = {
            essential: true,
            analytics: (
                document.getElementById("analytics") as HTMLInputElement
            ).checked,
            marketing: (
                document.getElementById("marketing") as HTMLInputElement
            ).checked,
            personalization: (
                document.getElementById("personalization") as HTMLInputElement
            ).checked,
        };
        CookieManager.setConsent(prefs);
        removeCookieConsent();
    };

    (window as any).acceptAll = () => {
        const prefs: CookiePreferences = {
            essential: true,
            analytics: true,
            marketing: true,
            personalization: true,
        };
        CookieManager.setConsent(prefs);
        removeCookieConsent();
    };
}

/**
 * Remove cookie consent modal
 */
export function removeCookieConsent(): void {
    const modal = document.querySelector(
        ".cookie-consent-overlay",
    ) as HTMLElement;
    if (modal) {
        modal.style.opacity = "0";
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = "";
        }, 300);
    }
}

/**
 * Update greeting elements on page
 */
export function updateGreeting(): void {
    const greetingElements = document.querySelectorAll("[data-greeting]");
    const greeting = CookieManager.getGreeting();

    greetingElements.forEach((element) => {
        element.textContent = greeting;
    });
}

/**
 * Initialize cookie consent system
 */
export function initCookieConsent(): void {
    // Increment visit count
    CookieManager.incrementVisit();

    // Update greeting if personalization is allowed
    const consent = CookieManager.getConsent();
    if (consent?.personalization) {
        updateGreeting();
    }

    // Show consent modal if needed
    if (CookieManager.shouldShowConsent()) {
        setTimeout(() => {
            showCookieConsent();
            CookieManager.markConsentShown();
        }, 1000);
    }
}
