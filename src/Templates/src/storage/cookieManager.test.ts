/**
 * CookieManager Tests
 * Tests for cookie consent and user preferences management
 */

import { describe, it, expect, beforeEach } from "vitest";
import { CookieManager } from "./cookieManager";

describe("CookieManager", () => {
  beforeEach(() => {
    localStorage.clear();
  });

  describe("hasConsent", () => {
    it("should return false when no consent given", () => {
      expect(CookieManager.hasConsent()).toBe(false);
    });

    it("should return true when consent has been given", () => {
      CookieManager.setConsent({
        essential: true,
        analytics: false,
        marketing: false,
        personalization: false,
      });
      expect(CookieManager.hasConsent()).toBe(true);
    });
  });

  describe("getConsent", () => {
    it("should return null when no consent given", () => {
      expect(CookieManager.getConsent()).toBeNull();
    });

    it("should return consent preferences", () => {
      const expected = {
        essential: true,
        analytics: true,
        marketing: false,
        personalization: true,
      };
      CookieManager.setConsent(expected);
      expect(CookieManager.getConsent()).toEqual(expected);
    });
  });

  describe("setConsent", () => {
    it("should store consent preferences", () => {
      const consent = {
        essential: true,
        analytics: true,
        marketing: false,
        personalization: false,
      };
      CookieManager.setConsent(consent);
      expect(CookieManager.getConsent()).toEqual(consent);
    });
  });

  describe("getUserInfo", () => {
    it("should return null when no user info stored", () => {
      expect(CookieManager.getUserInfo()).toBeNull();
    });

    it("should return user info", () => {
      const expected = {
        name: "John",
        visitCount: 5,
        lastVisit: "2024-01-01T00:00:00.000Z",
        preferences: {
          essential: true,
          analytics: false,
          marketing: false,
          personalization: false,
        },
      };
      localStorage.setItem("user_info", JSON.stringify(expected));
      expect(CookieManager.getUserInfo()).toEqual(expected);
    });
  });

  describe("setUserInfo", () => {
    it("should create new user info when none exists", () => {
      const info = CookieManager.setUserInfo({ name: "Alice" });
      expect(info.name).toBe("Alice");
      expect(info.visitCount).toBe(0);
    });

    it("should update existing user info", () => {
      CookieManager.setUserInfo({ name: "Bob", visitCount: 3 });
      const updated = CookieManager.setUserInfo({ name: "Bob Updated" });
      expect(updated.name).toBe("Bob Updated");
      expect(updated.visitCount).toBe(3);
    });
  });

  describe("incrementVisit", () => {
    it("should increment visit count from 0", () => {
      const info = CookieManager.incrementVisit();
      expect(info.visitCount).toBe(1);
    });

    it("should increment existing visit count", () => {
      CookieManager.setUserInfo({ visitCount: 5 });
      const info = CookieManager.incrementVisit();
      expect(info.visitCount).toBe(6);
    });

    it("should update lastVisit timestamp", () => {
      const before = new Date().toISOString();
      const info = CookieManager.incrementVisit();
      const after = new Date().toISOString();
      expect(info.lastVisit >= before && info.lastVisit <= after).toBe(true);
    });
  });

  describe("shouldShowConsent", () => {
    it("should return true when no consent and not shown before", () => {
      expect(CookieManager.shouldShowConsent()).toBe(true);
    });

    it("should return false when consent already given", () => {
      CookieManager.setConsent({
        essential: true,
        analytics: false,
        marketing: false,
        personalization: false,
      });
      expect(CookieManager.shouldShowConsent()).toBe(false);
    });

    it("should return false when already shown", () => {
      CookieManager.markConsentShown();
      expect(CookieManager.shouldShowConsent()).toBe(false);
    });
  });

  describe("markConsentShown", () => {
    it("should mark consent as shown", () => {
      CookieManager.markConsentShown();
      expect(CookieManager.shouldShowConsent()).toBe(false);
    });
  });

  describe("getGreeting", () => {
    it("should return default greeting when no user info", () => {
      expect(CookieManager.getGreeting()).toBe("Welcome!");
    });

    it("should return personalized greeting with name", () => {
      CookieManager.setUserInfo({ name: "Alice", visitCount: 1 });
      const greeting = CookieManager.getGreeting();
      expect(greeting).toContain("Alice");
    });

    it("should include visit count in greeting", () => {
      CookieManager.setUserInfo({ name: "Bob", visitCount: 5 });
      const greeting = CookieManager.getGreeting();
      expect(greeting).toContain("5th time");
    });

    it("should say 'first time' for first visit", () => {
      CookieManager.setUserInfo({ name: "Charlie", visitCount: 1 });
      const greeting = CookieManager.getGreeting();
      expect(greeting).toContain("first time");
    });
  });
});
