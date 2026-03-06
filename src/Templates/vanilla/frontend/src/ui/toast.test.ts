/**
 * Toast Tests
 * Tests for toast notification utility
 */

import { describe, it, expect, beforeEach, vi, afterEach } from "vitest";
import { showToast } from "./toast";

describe("showToast", () => {
  beforeEach(() => {
    document.body.innerHTML = "";
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  it("should create toast element", () => {
    showToast("Test message");
    const toast = document.querySelector(".app-toast");
    expect(toast).toBeTruthy();
  });

  it("should have correct type class", () => {
    showToast("Success", "success");
    expect(document.querySelector(".toast-success")).toBeTruthy();

    showToast("Error", "error");
    expect(document.querySelector(".toast-error")).toBeTruthy();

    showToast("Warning", "warning");
    expect(document.querySelector(".toast-warning")).toBeTruthy();

    showToast("Info", "info");
    expect(document.querySelector(".toast-info")).toBeTruthy();
  });

  it("should display message", () => {
    showToast("Hello World");
    const toast = document.querySelector(".app-toast");
    expect(toast?.textContent).toContain("Hello World");
  });

  it("should show success icon", () => {
    showToast("Success", "success");
    const icon = document.querySelector(".toast-icon svg");
    expect(icon).toBeTruthy();
  });

  it("should have toast-content structure", () => {
    showToast("Test");
    const toastContent = document.querySelector(".toast-content");
    expect(toastContent).toBeTruthy();
    expect(toastContent?.querySelector(".toast-icon")).toBeTruthy();
    expect(toastContent?.querySelector(".toast-message")).toBeTruthy();
  });

  it("should have close button", () => {
    showToast("Test");
    const closeButton = document.querySelector(".toast-close");
    expect(closeButton).toBeTruthy();
  });
});
