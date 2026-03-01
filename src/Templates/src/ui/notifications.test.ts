/**
 * NotificationManager Tests
 * Tests for the notification system
 */

import { describe, it, expect, beforeEach, vi, afterEach } from "vitest";
import { NotificationManager, type NotificationOptions } from "./notifications";

describe("NotificationManager", () => {
  let manager: NotificationManager;
  let container: HTMLElement;

  beforeEach(() => {
    document.body.innerHTML = "";
    manager = new NotificationManager();
    container = document.querySelector(".alerts-container")!;
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  describe("Initialization", () => {
    it("should create alerts container if not exists", () => {
      expect(container).toBeTruthy();
      expect(container?.className).toBe("alerts-container");
    });
  });

  describe("show", () => {
    it("should create notification with correct type", () => {
      const id = manager.show({
        type: "success",
        message: "Test message",
      });

      const notification = container.querySelector(".alert-success");
      expect(notification).toBeTruthy();
      expect(notification?.textContent).toContain("Test message");
    });

    it("should include title if provided", () => {
      manager.show({
        type: "info",
        message: "Test message",
        title: "Test Title",
      });

      const notification = container.querySelector(".alert-info");
      expect(notification?.innerHTML).toContain("<strong>Test Title</strong>");
    });

    it("should return notification ID", () => {
      const id = manager.show({
        type: "success",
        message: "Test",
      });
      expect(id).toMatch(/notification-\d+-[a-z0-9]+/);
    });
  });

  describe("success", () => {
    it("should show success notification", () => {
      manager.success("Operation completed");
      const notification = container.querySelector(".alert-success");
      expect(notification).toBeTruthy();
      expect(notification?.textContent).toContain("Operation completed");
    });

    it("should accept optional title", () => {
      manager.success("Saved successfully", "Success");
      const notification = container.querySelector(".alert-success");
      expect(notification?.innerHTML).toContain("<strong>Success</strong>");
    });
  });

  describe("error", () => {
    it("should show error notification", () => {
      manager.error("Something went wrong");
      const notification = container.querySelector(".alert-error");
      expect(notification).toBeTruthy();
    });
  });

  describe("warning", () => {
    it("should show warning notification", () => {
      manager.warning("Please check your input");
      const notification = container.querySelector(".alert-warning");
      expect(notification).toBeTruthy();
    });
  });

  describe("info", () => {
    it("should show info notification", () => {
      manager.info("New feature available");
      const notification = container.querySelector(".alert-info");
      expect(notification).toBeTruthy();
    });
  });

  describe("Notification Icons", () => {
    it("should include SVG icon for success", () => {
      manager.success("Test");
      const icon = container.querySelector(".alert-icon svg");
      expect(icon).toBeTruthy();
    });

    it("should include SVG icon for error", () => {
      manager.error("Test");
      const icon = container.querySelector(".alert-error .alert-icon svg");
      expect(icon).toBeTruthy();
    });

    it("should include SVG icon for warning", () => {
      manager.warning("Test");
      const icon = container.querySelector(".alert-warning .alert-icon svg");
      expect(icon).toBeTruthy();
    });

    it("should include SVG icon for info", () => {
      manager.info("Test");
      const icon = container.querySelector(".alert-info .alert-icon svg");
      expect(icon).toBeTruthy();
    });
  });
});
