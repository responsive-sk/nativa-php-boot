/**
 * Storage Utilities Tests
 * Tests for localStorage wrapper functions
 */

import { describe, it, expect, beforeEach, vi } from "vitest";
import {
  safeGetItem,
  safeSetItem,
  safeRemoveItem,
  safeClear,
} from "../storage/storage";

describe("Storage Utilities", () => {
  beforeEach(() => {
    localStorage.clear();
  });

  describe("safeGetItem", () => {
    it("should return value for existing key", () => {
      localStorage.setItem("test-key", "test-value");
      expect(safeGetItem("test-key")).toBe("test-value");
    });

    it("should return null for non-existing key", () => {
      expect(safeGetItem("non-existing")).toBeNull();
    });
  });

  describe("safeSetItem", () => {
    it("should set value for key", () => {
      safeSetItem("test-key", "test-value");
      expect(safeGetItem("test-key")).toBe("test-value");
    });

    it("should not throw when localStorage throws error", () => {
      const originalSetItem = localStorage.setItem;
      localStorage.setItem = vi.fn(() => {
        throw new Error("Storage disabled");
      });

      expect(() => safeSetItem("key", "value")).not.toThrow();

      localStorage.setItem = originalSetItem;
    });

    it("should update existing key", () => {
      safeSetItem("test-key", "value1");
      safeSetItem("test-key", "value2");
      expect(safeGetItem("test-key")).toBe("value2");
    });
  });

  describe("safeRemoveItem", () => {
    it("should remove existing key", () => {
      safeSetItem("test-key", "test-value");
      safeRemoveItem("test-key");
      expect(safeGetItem("test-key")).toBeNull();
    });

    it("should not throw when removing non-existing key", () => {
      expect(() => safeRemoveItem("non-existing")).not.toThrow();
    });

    it("should not throw when localStorage throws error", () => {
      const originalRemoveItem = localStorage.removeItem;
      localStorage.removeItem = vi.fn(() => {
        throw new Error("Storage disabled");
      });

      expect(() => safeRemoveItem("key")).not.toThrow();

      localStorage.removeItem = originalRemoveItem;
    });
  });

  describe("safeClear", () => {
    it("should clear all items", () => {
      safeSetItem("key1", "value1");
      safeSetItem("key2", "value2");
      safeClear();
      expect(safeGetItem("key1")).toBeNull();
      expect(safeGetItem("key2")).toBeNull();
    });

    it("should not throw when localStorage throws error", () => {
      const originalClear = localStorage.clear;
      localStorage.clear = vi.fn(() => {
        throw new Error("Storage disabled");
      });

      expect(() => safeClear()).not.toThrow();

      localStorage.clear = originalClear;
    });
  });
});
