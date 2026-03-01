# Visual Regression Testing

Visual regression testing using Playwright to detect unintended UI changes.

## Setup

```bash
# Install Playwright
cd views/templates
pnpm add -D @playwright/test

# Install browser binaries
pnpm exec playwright install --with-deps chromium
```

## Configuration

**playwright.config.ts**
```typescript
import { defineConfig, devices } from "@playwright/test";

export default defineConfig({
  testDir: "./tests/visual",
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: "html",
  use: {
    baseURL: "http://localhost:8008",
    trace: "on-first-retry",
    screenshot: "only-on-failure",
  },
  projects: [
    {
      name: "chromium",
      use: { ...devices["Desktop Chrome"] },
    },
    {
      name: "Mobile Chrome",
      use: { ...devices["Pixel 5"] },
    },
  ],
});
```

## Running Tests

```bash
# Run all visual tests
pnpm exec playwright test

# Run with UI mode (interactive)
pnpm exec playwright test --ui

# Run specific test file
pnpm exec playwright test tests/visual/home.spec.ts

# Run with specific project
pnpm exec playwright test --project=chromium

# Update baseline screenshots
pnpm exec playwright test --update-snapshots
```

## Test Structure

```
tests/
└── visual/
    ├── home.spec.ts           # Homepage visual tests
    ├── blog.spec.ts           # Blog pages
    ├── pricing.spec.ts        # Pricing page
    ├── components/
    │   ├── buttons.spec.ts    # Button components
    │   ├── cards.spec.ts      # Card components
    │   └── forms.spec.ts      # Form components
    └── snapshots/             # Baseline screenshots (auto-generated)
```

## Example Test

```typescript
import { test, expect } from "@playwright/test";

test("homepage should look the same", async ({ page }) => {
  await page.goto("/");
  
  // Wait for any animations to complete
  await page.waitForTimeout(1000);
  
  // Take screenshot and compare with baseline
  await expect(page).toHaveScreenshot("homepage.png", {
    fullPage: true,
    maxDiffPixels: 100, // Allow small differences
  });
});

test("button components", async ({ page }) => {
  await page.goto("/docs/components/buttons");
  await expect(page).toHaveScreenshot("buttons.png");
});
```

## CI/CD Integration

```yaml
# .github/workflows/visual-regression.yml
name: Visual Regression Tests

on: [pull_request]

jobs:
  visual-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: 20
      - run: cd views/templates && pnpm install
      - run: cd views/templates && pnpm exec playwright install --with-deps chromium
      - run: php -S localhost:8008 -t public &
      - run: cd views/templates && pnpm exec playwright test
      - uses: actions/upload-artifact@v4
        if: failure()
        with:
          name: playwright-report
          path: views/templates/playwright-report/
```

## Best Practices

1. **Stable Data**: Use consistent test data for screenshots
2. **Wait for Load**: Wait for animations and lazy-loaded content
3. **Viewport Consistency**: Test multiple viewports explicitly
4. **Ignore Dynamic Content**: Mask timestamps, avatars, etc.
5. **Regular Baseline Updates**: Update snapshots after intentional changes

## Masking Dynamic Content

```typescript
await expect(page).toHaveScreenshot("dashboard.png", {
  mask: [
    page.locator(".timestamp"),
    page.locator(".user-avatar"),
    page.locator("[data-dynamic]"),
  ],
});
```

## Troubleshooting

**Tests failing after intentional changes:**
```bash
pnpm exec playwright test --update-snapshots
```

**Different results on different machines:**
- Use Docker for consistent environment
- Specify exact browser versions
- Use `maxDiffPixels` option for minor differences

## Resources

- [Playwright Documentation](https://playwright.dev/)
- [Visual Regression Testing Guide](https://playwright.dev/docs/test-snapshots)
