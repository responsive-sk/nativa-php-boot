#!/usr/bin/env node

/**
 * Theme Token Validator
 * Ensures CSS files use theme-aware tokens instead of hardcoded colors
 */

import { readFileSync, readdirSync } from "fs";
import { join } from "path";

const HARDCODED_COLORS = [
  { value: "#0a0a0a", replacement: "var(--color-bg)" },
  { value: "#1a1a2e", replacement: "var(--color-bg-alt)" },
  { value: "#333333", replacement: "var(--color-bg-hover)" },
  { value: "#ffffff", replacement: "var(--color-text)" },
  { value: "#b0b0b0", replacement: "var(--color-text-muted)" },
  { value: "#2d2d2d", replacement: "var(--color-border)" },
];

const STYLES_DIR = join(process.cwd(), "src/styles/components");

function validateFile(filePath) {
  const content = readFileSync(filePath, "utf-8");
  const errors = [];
  const fileName = filePath.split("/").pop();

  HARDCODED_COLORS.forEach(({ value, replacement }) => {
    // Check for hardcoded color (but not in comments or token definitions)
    const regex = new RegExp(`[^-/](${value})`, "g");
    if (regex.test(content)) {
      errors.push(
        `${fileName}: Found hardcoded ${value} - use ${replacement} instead`
      );
    }
  });

  return errors;
}

// Scan all component CSS files
const files = readdirSync(STYLES_DIR).filter((f) => f.endsWith(".css"));

let allErrors = [];
files.forEach((file) => {
  const filePath = join(STYLES_DIR, file);
  allErrors = allErrors.concat(validateFile(filePath));
});

if (allErrors.length > 0) {
  console.error("❌ Theme token validation failed:\n");
  allErrors.forEach((err) => console.error(`  - ${err}`));
  console.error(
    "\n💡 Fix: Replace hardcoded colors with theme-aware tokens from tokens.css"
  );
  process.exit(1);
}

console.log("✅ All CSS files use theme-aware tokens");
process.exit(0);
