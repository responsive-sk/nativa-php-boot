/**
 * Cookie Consent and User Preferences Management
 * Handles cookie consent, user info tracking, and personalized greetings
 */

import { safeGetItem, safeSetItem } from "@storage/storage.js";

export interface CookiePreferences {
    essential: boolean;
    analytics: boolean;
    marketing: boolean;
    personalization: boolean;
}

export interface UserInfo {
    name?: string;
    visitCount: number;
    lastVisit: string;
    preferences: CookiePreferences;
}

export class CookieManager {
    private static readonly CONSENT_KEY = "cookie_consent";
    private static readonly USER_INFO_KEY = "user_info";
    private static readonly CONSENT_SHOWN_KEY = "consent_shown";

    /**
     * Check if user has given consent
     */
    static hasConsent(): boolean {
        return safeGetItem(this.CONSENT_KEY) !== null;
    }

    /**
     * Get current consent preferences
     */
    static getConsent(): CookiePreferences | null {
        const consent = safeGetItem(this.CONSENT_KEY);
        return consent ? JSON.parse(consent) : null;
    }

    /**
     * Set consent preferences
     */
    static setConsent(preferences: CookiePreferences): void {
        safeSetItem(this.CONSENT_KEY, JSON.stringify(preferences));
    }

    /**
     * Get stored user info
     */
    static getUserInfo(): UserInfo | null {
        const info = safeGetItem(this.USER_INFO_KEY);
        return info ? JSON.parse(info) : null;
    }

    /**
     * Set or update user info
     */
    static setUserInfo(info: Partial<UserInfo>): UserInfo {
        const current = this.getUserInfo() || {
            visitCount: 0,
            lastVisit: new Date().toISOString(),
            preferences: {
                essential: true,
                analytics: false,
                marketing: false,
                personalization: false,
            },
        };

        const updated = { ...current, ...info };
        safeSetItem(this.USER_INFO_KEY, JSON.stringify(updated));
        return updated;
    }

    /**
     * Increment visit count
     */
    static incrementVisit(): UserInfo {
        const current = this.getUserInfo() || {
            visitCount: 0,
            lastVisit: new Date().toISOString(),
            preferences: {
                essential: true,
                analytics: false,
                marketing: false,
                personalization: false,
            },
        };

        const updated = {
            ...current,
            visitCount: current.visitCount + 1,
            lastVisit: new Date().toISOString(),
        };

        safeSetItem(this.USER_INFO_KEY, JSON.stringify(updated));
        return updated;
    }

    /**
     * Check if consent modal should be shown
     */
    static shouldShowConsent(): boolean {
        return (
            !this.hasConsent() && safeGetItem(this.CONSENT_SHOWN_KEY) !== "true"
        );
    }

    /**
     * Mark consent as shown (prevent showing again)
     */
    static markConsentShown(): void {
        safeSetItem(this.CONSENT_SHOWN_KEY, "true");
    }

    /**
     * Get personalized greeting based on user info and time of day
     */
    static getGreeting(): string {
        const userInfo = this.getUserInfo();
        if (!userInfo) return "Welcome!";

        const { name, visitCount } = userInfo;
        const hour = new Date().getHours();

        let timeGreeting = "Hello";
        if (hour < 12) timeGreeting = "Good morning";
        else if (hour < 18) timeGreeting = "Good afternoon";
        else timeGreeting = "Good evening";

        if (name) {
            const visitText =
                visitCount === 1 ? "first time" : `${visitCount}th time`;
            return `${timeGreeting}, ${name}! Welcome back for your ${visitText}.`;
        }

        return `${timeGreeting}! Welcome back.`;
    }
}
