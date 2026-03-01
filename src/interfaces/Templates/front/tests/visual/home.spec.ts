import { test, expect } from "@playwright/test";

/**
 * Homepage Visual Regression Tests
 * Compares screenshots against baseline to detect unintended changes
 */

test.describe("Homepage Visual Tests", () => {
  test.beforeEach(async ({ page }) => {
    // Go to homepage before each test
    await page.goto("/");
    // Wait for any animations to complete
    await page.waitForTimeout(500);
  });

  test("homepage desktop should match baseline", async ({ page }) => {
    await expect(page).toHaveScreenshot("homepage-desktop.png", {
      fullPage: true,
      maxDiffPixels: 100,
    });
  });

  test("homepage mobile should match baseline", async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page).toHaveScreenshot("homepage-mobile.png", {
      fullPage: true,
      maxDiffPixels: 50,
    });
  });

  test("hero section should match baseline", async ({ page }) => {
    const hero = page.locator(".hero, .app-hero, [class*='hero']");
    await expect(hero).toHaveScreenshot("hero-section.png");
  });

  test("header navigation should match baseline", async ({ page }) => {
    const header = page.locator("header");
    await expect(header).toHaveScreenshot("header-navigation.png");
  });

  test("footer should match baseline", async ({ page }) => {
    const footer = page.locator("footer");
    await expect(footer).toHaveScreenshot("footer.png");
  });
});
