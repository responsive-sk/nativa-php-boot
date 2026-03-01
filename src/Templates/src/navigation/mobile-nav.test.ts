/**
 * Mobile Navigation Tests
 * Tests for mobile menu functionality
 */

import { describe, it, expect, beforeEach, afterEach, vi } from "vitest";
import { MobileNav, initMobileNav } from "./mobile-nav";

describe("MobileNav", () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <button class="mobile-menu-btn" aria-label="Open menu" aria-expanded="false">
        Menu
      </button>
      <nav class="mobile-menu" id="mobile-menu">
        <div class="mobile-menu__nav">
          <a href="/" class="mobile-menu__link">Home</a>
          <a href="/services" class="mobile-menu__link">Services</a>
        </div>
        <div class="mobile-menu__actions">
          <a href="/login" class="btn">Sign In</a>
        </div>
      </nav>
    `;
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  describe("Initialization", () => {
    it("should initialize without errors", () => {
      const nav = new MobileNav();
      expect(() => nav.init()).not.toThrow();
    });

    it("should find menu elements", () => {
      const nav = new MobileNav();
      nav.init();
      expect(nav).toBeTruthy();
    });

    it("should handle missing menu gracefully", () => {
      document.body.innerHTML = "";
      const nav = new MobileNav();
      expect(() => nav.init()).not.toThrow();
    });
  });

  describe("Toggle", () => {
    it("should open menu on button click", () => {
      const nav = new MobileNav();
      nav.init();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      btn.click();

      const menu = document.querySelector(".mobile-menu");
      expect(menu?.classList.contains("active")).toBe(true);
      expect(btn.getAttribute("aria-expanded")).toBe("true");
    });

    it("should close menu on second click", () => {
      const nav = new MobileNav();
      nav.init();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      btn.click(); // Open
      btn.click(); // Close

      const menu = document.querySelector(".mobile-menu");
      expect(menu?.classList.contains("active")).toBe(false);
      expect(btn.getAttribute("aria-expanded")).toBe("false");
    });

    it("should toggle isOpen state", () => {
      const nav = new MobileNav();
      nav.init();

      expect(nav.isOpenMenu()).toBe(false);

      nav.toggle();
      expect(nav.isOpenMenu()).toBe(true);

      nav.toggle();
      expect(nav.isOpenMenu()).toBe(false);
    });
  });

  describe("Open", () => {
    it("should add active class to menu", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const menu = document.querySelector(".mobile-menu");
      expect(menu?.classList.contains("active")).toBe(true);
    });

    it("should set aria-expanded to true", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      expect(btn.getAttribute("aria-expanded")).toBe("true");
    });

    it("should update aria-label", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      expect(btn.getAttribute("aria-label")).toBe("Close menu");
    });
  });

  describe("Close", () => {
    it("should remove active class from menu", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();
      nav.close();

      const menu = document.querySelector(".mobile-menu");
      expect(menu?.classList.contains("active")).toBe(false);
    });

    it("should set aria-expanded to false", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();
      nav.close();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      expect(btn.getAttribute("aria-expanded")).toBe("false");
    });

    it("should update aria-label", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();
      nav.close();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLButtonElement;
      expect(btn.getAttribute("aria-label")).toBe("Open menu");
    });
  });

  describe("Close on link click", () => {
    it("should close menu when link is clicked", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const link = document.querySelector(".mobile-menu__link") as HTMLElement;
      link.click();

      expect(nav.isOpenMenu()).toBe(false);
    });
  });

  describe("Close on escape key", () => {
    it("should close menu on escape key press", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const escapeEvent = new KeyboardEvent("keydown", { key: "Escape" });
      document.dispatchEvent(escapeEvent);

      expect(nav.isOpenMenu()).toBe(false);
    });

    it("should not close on other keys", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const enterEvent = new KeyboardEvent("keydown", { key: "Enter" });
      document.dispatchEvent(enterEvent);

      expect(nav.isOpenMenu()).toBe(true);
    });
  });

  describe("Close on outside click", () => {
    it("should close when clicking outside menu", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const outside = document.createElement("div");
      document.body.appendChild(outside);
      outside.click();

      expect(nav.isOpenMenu()).toBe(false);
    });

    it("should not close when clicking inside menu", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const menu = document.querySelector(".mobile-menu") as HTMLElement;
      menu.click();

      expect(nav.isOpenMenu()).toBe(true);
    });

    it("should not close when clicking button", () => {
      const nav = new MobileNav();
      nav.init();

      nav.open();

      const btn = document.querySelector(".mobile-menu-btn") as HTMLElement;
      btn.click();

      expect(nav.isOpenMenu()).toBe(false); // Should toggle closed
    });
  });

  describe("Destroy", () => {
    it("should cleanup without errors", () => {
      const nav = new MobileNav();
      nav.init();
      expect(() => nav.destroy()).not.toThrow();
    });
  });
});

describe("initMobileNav", () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <button class="mobile-menu-btn" aria-label="Open menu">Menu</button>
      <nav class="mobile-menu">
        <a href="/" class="mobile-menu__link">Home</a>
      </nav>
    `;
  });

  it("should initialize mobile nav", () => {
    expect(() => initMobileNav()).not.toThrow();
  });

  it("should expose mobileNav on window", () => {
    initMobileNav();
    expect((window as any).mobileNav).toBeTruthy();
    expect((window as any).mobileNav).toBeInstanceOf(MobileNav);
  });
});
