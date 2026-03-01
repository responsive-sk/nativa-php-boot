/**
 * CsrfManager Tests
 * Tests for CSRF token management
 */

import { describe, it, expect, beforeEach } from "vitest";
import { CsrfManager } from "../core/csrf";

describe("CsrfManager", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
    document.head.innerHTML = "";
    // Reset static properties
    (CsrfManager as any).token = null;
    (CsrfManager as any).tokenName = "_csrf";
  });

  describe("getToken", () => {
    it("should return null when no token found", () => {
      expect(CsrfManager.getToken()).toBeNull();
    });

    it("should get token from meta tag", () => {
      const meta = document.createElement("meta");
      meta.name = "csrf-token";
      meta.content = "abc123";
      document.head.appendChild(meta);

      expect(CsrfManager.getToken()).toBe("abc123");
    });

    it("should get token from hidden input", () => {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "_csrf";
      input.value = "xyz789";
      document.body.appendChild(input);

      expect(CsrfManager.getToken()).toBe("xyz789");
    });

    it("should prefer meta tag over input", () => {
      const meta = document.createElement("meta");
      meta.name = "csrf-token";
      meta.content = "meta-token";
      document.head.appendChild(meta);

      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "_csrf";
      input.value = "input-token";
      document.body.appendChild(input);

      expect(CsrfManager.getToken()).toBe("meta-token");
    });
  });

  describe("getTokenName", () => {
    it("should return default token name", () => {
      expect(CsrfManager.getTokenName()).toBe("_csrf");
    });
  });

  describe("setTokenName", () => {
    it("should set custom token name", () => {
      CsrfManager.setTokenName("custom_csrf");
      expect(CsrfManager.getTokenName()).toBe("custom_csrf");
    });
  });

  describe("addToForm", () => {
    it("should add hidden input to form when token exists", () => {
      // Setup token
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "_csrf";
      input.value = "token123";
      document.body.appendChild(input);

      const form = document.createElement("form");
      document.body.appendChild(form);

      CsrfManager.addToForm(form);

      const csrfInput = form.querySelector('input[type="hidden"][name="_csrf"]');
      expect(csrfInput).toBeTruthy();
      expect((csrfInput as HTMLInputElement).value).toBe("token123");
    });

    it("should not add if token not available", () => {
      const form = document.createElement("form");
      document.body.appendChild(form);

      CsrfManager.addToForm(form);

      expect(form.querySelectorAll('input[type="hidden"]')).toHaveLength(0);
    });
  });

  describe("getHeader", () => {
    it("should return empty object when no token", () => {
      expect(CsrfManager.getHeader()).toEqual({});
    });

    it("should return header with token", () => {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "_csrf";
      input.value = "header-token";
      document.body.appendChild(input);

      expect(CsrfManager.getHeader()).toEqual({
        "X-CSRF-Token": "header-token",
      });
    });
  });
});
