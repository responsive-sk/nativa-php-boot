import { test, expect } from "@playwright/test";

/**
 * Component Visual Regression Tests
 * Tests individual UI components in isolation
 */

test.describe("Button Components", () => {
  test("button variants should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    // Find button section or create test buttons
    await expect(page.locator(".btn")).toHaveScreenshot("buttons.png", {
      maxDiffPixels: 20,
    });
  });

  test("button states should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    const buttons = page.locator(".btn");
    
    // Test hover state
    await buttons.first().hover();
    await expect(buttons.first()).toHaveScreenshot("button-hover.png");
  });
});

test.describe("Card Components", () => {
  test("card variants should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    await expect(page.locator(".card")).toHaveScreenshot("cards.png", {
      maxDiffPixels: 30,
    });
  });

  test("card hover effect should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    const card = page.locator(".card").first();
    await card.hover();
    await expect(card).toHaveScreenshot("card-hover.png");
  });
});

test.describe("Form Components", () => {
  test("form inputs should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    await expect(page.locator(".form-control, .input")).toHaveScreenshot(
      "form-inputs.png",
      { maxDiffPixels: 20 },
    );
  });

  test("input focus state should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    const input = page.locator(".input, .form-control").first();
    await input.focus();
    await expect(input).toHaveScreenshot("input-focus.png");
  });
});

test.describe("Alert Components", () => {
  test("alert variants should match baseline", async ({ page }) => {
    await page.goto("/docs");
    
    await expect(page.locator(".alert")).toHaveScreenshot("alerts.png", {
      maxDiffPixels: 30,
    });
  });
});
