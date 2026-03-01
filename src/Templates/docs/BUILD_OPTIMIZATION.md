# Build System & Performance Optimization

## Vite Configuration Overview

### Production Optimizations

```typescript
// vite.config.ts
export default defineConfig({
  build: {
    target: "es2020",           // Modern browsers
    minify: "esbuild",          // Fast minification
    cssMinify: true,            // CSS minification
    cssCodeSplit: true,         // Split CSS by entry
    treeshake: "smallest",      // Aggressive tree-shaking
  },
});
```

### Bundle Analysis

```bash
# Analyze bundle size
pnpm run analyze

# View interactive report
open views/templates/stats.html
```

## Critical CSS Extraction

### What is Critical CSS?

Critical CSS is the minimal CSS required to render the above-the-fold content. Loading it inline speeds up First Contentful Paint (FCP).

### Implementation

```typescript
// src/core/criticalCss.ts
import { CriticalCss } from "./core/criticalCss";

// Get critical CSS for homepage
const critical = CriticalCss.getForPage("home");
```

### Usage in PHP Template

```php
<head>
  <!-- Inline critical CSS -->
  <style><?= CriticalCss::getForPage($page) ?></style>
  
  <!-- Load full CSS asynchronously -->
  <link rel="stylesheet" href="/assets/css.css" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="/assets/css.css"></noscript>
</head>
```

### Critical CSS Selectors

```typescript
const CriticalSelectors = {
  home: [
    ":root", "*", "body",
    "header", ".header", ".nav",
    ".hero", ".btn", ".container",
  ],
  blog: [
    ":root", "body", "header",
    ".blog-hero", ".blog-card", ".btn",
  ],
};
```

## CSS Architecture (BEM)

### Naming Convention

```css
/* Block */
.card { }

/* Element */
.card__header { }
.card__title { }

/* Modifier */
.card--featured { }
.card--hover:hover { }
```

### Component Structure

```css
/* ===== COMPONENT ===== */
/* Description */

.block {
  /* Base styles */
}

/* Modifier */
.block--modifier { }

/* Element */
.block__element { }

/* Element with modifier */
.block__element--modifier { }

/* State (JS hook) */
.is-active { }

/* Responsive */
@media (max-width: 768px) { }
```

## Performance Metrics

### Before Optimization

| Metric | Value |
|--------|-------|
| CSS Bundle | 34.82 KB |
| JS Bundle | 26.64 KB |
| FCP | ~1.2s |

### After Optimization

| Metric | Target |
|--------|--------|
| CSS Bundle | < 30 KB |
| JS Bundle | < 25 KB |
| FCP | < 0.8s |

## Optimization Techniques

### 1. Code Splitting

```typescript
// Automatic by Vite
rollupOptions: {
  input: {
    css: "./src/css.ts",      // Shared CSS
    app: "./src/app.ts",      // Shared JS
    home: "./src/home.ts",    // Page-specific
  }
}
```

### 2. Tree Shaking

```typescript
// Remove unused code
treeshake: "smallest"
```

### 3. Compression

```bash
# Gzip: ~6.37 KB (from 34.82 KB)
# Brotli: ~5.35 KB (from 34.82 KB)
```

### 4. CSS Purging (Optional)

Install PurgeCSS:
```bash
pnpm add -D @fullhuman/postcss-purgecss
```

Configure in `postcss.config.js`:
```javascript
export default {
  plugins: {
    '@fullhuman/postcss-purgecss': {
      content: ['./src/**/*.php', './src/**/*.ts'],
      safelist: [/^btn/, /^card/, /active/]
    }
  }
}
```

## Build Commands

```bash
# Development build (fast, no minification)
pnpm run build:dev

# Production build (optimized)
pnpm run build:prod

# Analyze bundle
pnpm run analyze
```

## Lightning CSS

Fast CSS processor written in Rust:

```typescript
css: {
  lightningcss: {
    targets: { chrome: 90, firefox: 90, safari: 15 },
    minify: true,
  }
}
```

### Features

- 10x faster than PostCSS
- Automatic vendor prefixing
- CSS nesting support
- Custom media queries

## Monitoring

### Lighthouse Scores

Run Lighthouse:
```bash
npx lighthouse http://localhost:8008 --output=html
```

### Key Metrics

- **FCP** (First Contentful Paint): < 1.0s
- **LCP** (Largest Contentful Paint): < 2.5s
- **CLS** (Cumulative Layout Shift): < 0.1
- **TBT** (Total Blocking Time): < 200ms

## Best Practices

1. **Inline Critical CSS** - Above-the-fold styles
2. **Async Load Non-Critical** - `media="print"` trick
3. **Use CSS Variables** - Reduce repetition
4. **Avoid @import** - Use build tool imports
5. **Minify in Production** - Enable cssMinify
6. **Tree-shake Unused** - Remove dead code
7. **Compress** - Gzip + Brotli

## Resources

- [Vite Build Options](https://vitejs.dev/config/build-options.html)
- [Critical CSS Guide](https://web.dev/extract-critical-css/)
- [Lightning CSS](https://lightningcss.dev/)
- [Web Performance](https://web.dev/performance/)
