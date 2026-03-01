/**
 * Motion Effects Tests
 * Tests for animation utilities
 */

import { describe, it, expect, beforeEach } from "vitest";
import { fadeIn, fadeOut, slideInLeft, slideInRight, scaleIn } from "./motion";

describe("Motion Effects", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
  });

  describe("fadeIn", () => {
    it("should exist and be a function", () => {
      expect(typeof fadeIn).toBe("function");
    });
  });

  describe("fadeOut", () => {
    it("should exist and be a function", () => {
      expect(typeof fadeOut).toBe("function");
    });
  });

  describe("slideInLeft", () => {
    it("should exist and be a function", () => {
      expect(typeof slideInLeft).toBe("function");
    });
  });

  describe("slideInRight", () => {
    it("should exist and be a function", () => {
      expect(typeof slideInRight).toBe("function");
    });
  });

  describe("scaleIn", () => {
    it("should exist and be a function", () => {
      expect(typeof scaleIn).toBe("function");
    });
  });
});
