# Theme System Testing Strategy

Prevent theme toggle regressions with automated testing.

## Problem Solved

**Issue:** Theme toggle worked only on some elements because:
1. Hardcoded color tokens (`--color-dark`, `--color-gray`) instead of theme-aware tokens
2. `reset.css` was disabled, so `body` didn't inherit theme from `html`

## Prevention Strategy

### 1. Visual Regression Tests ✅

**File:** `tests/visual/theme.spec.ts`

```typescript
import { test, expect } from "@playwright/test";

test.describe("Theme Toggle", () => {
  test.beforeEach(async ({ page }) => {
    await page.goto("/");
  });

  test("should switch from dark to light mode", async ({ page }) => {
    // Start in dark mode (default)
    await expect(page.locator("html")).toHaveAttribute("data-theme", "dark");
    
    // Click theme toggle
    await page.click(".theme-toggle");
    
    // Verify light mode
    await expect(page.locator("html")).toHaveAttribute("data-theme", "light");
    
    // Verify body background changed
    const bodyBg = await page.evaluate(() => 
      getComputedStyle(document.body).backgroundColor
    );
    expect(bodyBg).not.toBe("rgb(10, 10, 10)"); // Not dark mode black
  });

  test("should persist theme in localStorage", async ({ page }) => {
    await page.click(".theme-toggle");
    
    const savedTheme = await page.evaluate(() => 
      localStorage.getItem("theme")
    );
    expect(savedTheme).toBe("light");
  });

  test("all elements should respect theme", async ({ page }) => {
    // Check multiple elements
    const elements = [
      { selector: "body", property: "backgroundColor" },
      { selector: ".header", property: "backgroundColor" },
      { selector: ".card", property: "backgroundColor" },
      { selector: ".btn", property: "color" },
    ];

    for (const { selector, property } of elements) {
      const el = await page.locator(selector).first();
      const value = await el.evaluate((el, prop) => 
        getComputedStyle(el)[prop as any], property
      );
      
      // Should not be hardcoded dark mode values
      expect(value).not.toBe("rgb(10, 10, 10)"); // --color-bg dark
      expect(value).not.toBe("rgb(255, 255, 255)"); // --color-text dark
    }
  });
});
```

### 2. CSS Linting Rules

**File:** `.stylelintrc.json`

```json
{
  "rules": {
    "declaration-property-value-disallowed-list": {
      "background": ["/#0a0a0a/", "/#1a1a2e/", "/#333333/"],
      "color": ["/#ffffff/", "/#b0b0b0/"],
      "border-color": ["/#2d2d2d/"]
    },
    "declaration-property-value-allowed-list": {
      "background": ["/var\\(--color-/"],
      "color": ["/var\\(--color-/"]
    }
  }
}
```

**Usage:**
```bash
pnpm add -D stylelint stylelint-config-standard
pnpm exec stylelint "src/styles/**/*.css"
```

### 3. Unit Tests for Theme Module

**File:** `src/ui/theme.test.ts`

```typescript
import { describe, it, expect, beforeEach } from "vitest";
import { initTheme, initThemeToggle } from "./theme";

describe("Theme System", () => {
  beforeEach(() => {
    document.documentElement.removeAttribute("data-theme");
    localStorage.clear();
  });

  it("should default to dark mode", () => {
    initTheme();
    expect(document.documentElement.getAttribute("data-theme")).toBe("dark");
  });

  it("should load saved theme from localStorage", () => {
    localStorage.setItem("theme", "light");
    initTheme();
    expect(document.documentElement.getAttribute("data-theme")).toBe("light");
  });

  it("should toggle theme on button click", () => {
    document.body.innerHTML = `<button class="theme-toggle"></button>`;
    initTheme();
    initThemeToggle();

    // Toggle to light
    document.querySelector(".theme-toggle")?.click();
    expect(document.documentElement.getAttribute("data-theme")).toBe("light");

    // Toggle back to dark
    document.querySelector(".theme-toggle")?.click();
    expect(document.documentElement.getAttribute("data-theme")).toBe("dark");
  });

  it("should save theme to localStorage", () => {
    document.body.innerHTML = `<button class="theme-toggle"></button>`;
    initTheme();
    initThemeToggle();

    document.querySelector(".theme-toggle")?.click();
    expect(localStorage.getItem("theme")).toBe("light");
  });
});
```

### 4. CSS Token Validation

**File:** `scripts/validate-tokens.js`

```javascript
#!/usr/bin/env node

/**
 * Validate that CSS files use theme-aware tokens
 * instead of hardcoded colors
 */

const fs = require("fs");
const path = require("path");

const HARDCODED_COLORS = [
  "#0a0a0a", // --color-bg (dark)
  "#1a1a2e", // --color-dark
  "#333333", // --color-gray
  "#ffffff", // --color-white
  "#b0b0b0", // --color-white-muted
];

const THEME_AWARE_TOKENS = [
  "--color-bg",
  "--color-bg-alt",
  "--color-bg-hover",
  "--color-text",
  "--color-text-muted",
  "--color-border",
];

function validateFile(filePath) {
  const content = fs.readFileSync(filePath, "utf-8");
  const errors = [];

  HARDCODED_COLORS.forEach(color => {
    const regex = new RegExp(`[^-]${color}`, "g");
    if (regex.test(content)) {
      errors.push(`Hardcoded color ${color} found in ${filePath}`);
    }
  });

  return errors;
}

// Scan all component CSS files
const stylesDir = path.join(__dirname, "../src/styles/components");
const files = fs.readdirSync(stylesDir)
  .filter(f => f.endsWith(".css"));

let allErrors = [];
files.forEach(file => {
  const filePath = path.join(stylesDir, file);
  allErrors = allErrors.concat(validateFile(filePath));
});

if (allErrors.length > 0) {
  console.error("❌ Theme token validation failed:");
  allErrors.forEach(err => console.error(`  - ${err}`));
  process.exit(1);
}

console.log("✅ All CSS files use theme-aware tokens");
```

**Add to package.json:**
```json
{
  "scripts": {
    "validate:tokens": "node scripts/validate-tokens.js"
  }
}
```

### 5. CI/CD Integration

**File:** `.github/workflows/theme-check.yml`

```yaml
name: Theme Check

on:
  pull_request:
    paths:
      - "views/templates/src/styles/**"
      - "views/templates/src/ui/theme.ts"

jobs:
  theme-validation:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - uses: pnpm/action-setup@v2
      
      - name: Install dependencies
        run: pnpm install
      
      - name: Validate theme tokens
        run: ppnpm run validate:tokens
      
      - name: Run visual regression tests
        run: pnpm test:visual
      
      - name: Run unit tests
        run: pnpm test -- src/ui/theme.test.ts
```

### 6. Documentation

**File:** `docs/THEME_GUIDELINES.md`

```markdown
# Theme System Guidelines

## Rules

1. **ALWAYS use theme-aware tokens:**
   - ✅ `var(--color-bg)`
   - ✅ `var(--color-text)`
   - ❌ `#0a0a0a`
   - ❌ `#ffffff`

2. **Token mapping:**
   ```
   Dark Mode → Light Mode
   --color-bg (#0a0a0a) → --color-bg (#ffffff)
   --color-text (#ffffff) → --color-text (#1a1a2e)
   ```

3. **Test checklist:**
   - [ ] Toggle works on all pages
   - [ ] Theme persists after refresh
   - [ ] All components respect theme
   - [ ] Visual tests pass
```

## Quick Test Commands

```bash
# Validate CSS tokens
ppnpm run validate:tokens

# Run theme unit tests
pnpm test -- src/ui/theme.test.ts

# Run visual regression tests
pnpm test:visual

# Check for hardcoded colors
grep -r "#0a0a0a\|#ffffff\|#1a1a2e" src/styles/
```

## Checklist for New Components

- [ ] Use `var(--color-bg)` instead of hardcoded colors
- [ ] Use `var(--color-text)` for text
- [ ] Use `var(--color-border)` for borders
- [ ] Test in both dark and light mode
- [ ] Add to visual regression test suite

---

**Last Updated:** 2026-02-19  
**Version:** 2.0
