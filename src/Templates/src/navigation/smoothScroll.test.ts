/**
 * Smooth Scroll Tests
 * Tests for smooth scroll navigation utility
 */

import { describe, it, expect, beforeEach, vi } from "vitest";
import { smoothScroll, initSmoothScroll } from "./smoothScroll";

describe("Smooth Scroll", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
    vi.clearAllMocks();
  });

  describe("smoothScroll", () => {
    it("should call window.scrollTo with correct parameters", () => {
      const target = document.createElement("div");
      target.id = "target";
      document.body.appendChild(target);

      // Mock offsetTop
      Object.defineProperty(target, "offsetTop", {
        get: () => 500,
        configurable: true,
      });

      smoothScroll(target);

      expect(window.scrollTo).toHaveBeenCalledWith({
        top: 420, // 500 - default offset 80
        behavior: "smooth",
      });
    });

    it("should use custom offset", () => {
      const target = document.createElement("div");
      target.id = "target";
      document.body.appendChild(target);

      Object.defineProperty(target, "offsetTop", {
        get: () => 500,
        configurable: true,
      });

      smoothScroll(target, { offset: 100 });

      expect(window.scrollTo).toHaveBeenCalledWith({
        top: 400, // 500 - 100
        behavior: "smooth",
      });
    });
  });

  describe("initSmoothScroll", () => {
    it("should add click listeners to anchor links", () => {
      const link = document.createElement("a");
      link.href = "#section";
      document.body.appendChild(link);

      const target = document.createElement("div");
      target.id = "section";
      Object.defineProperty(target, "offsetTop", {
        get: () => 300,
        configurable: true,
      });
      document.body.appendChild(target);

      initSmoothScroll();

      link.click();

      expect(window.scrollTo).toHaveBeenCalledWith({
        top: expect.any(Number),
        behavior: "smooth",
      });
    });

    it("should ignore empty hash links", () => {
      const link = document.createElement("a");
      link.href = "#";
      document.body.appendChild(link);

      initSmoothScroll();

      link.click();
      expect(window.scrollTo).not.toHaveBeenCalled();
    });

    it("should ignore links without target", () => {
      const link = document.createElement("a");
      link.href = "#nonexistent";
      document.body.appendChild(link);

      initSmoothScroll();

      link.click();
      expect(window.scrollTo).not.toHaveBeenCalled();
    });
  });
});
