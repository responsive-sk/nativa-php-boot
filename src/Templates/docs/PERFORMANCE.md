# Performance Guide

This guide covers performance optimization techniques and monitoring for the template system.

## Bundle Optimization

### Current Bundle Sizes

After cleanup optimization:

- `app.js`: 12.06 kB (gzipped: 3.39 kB)
- `app.css`: 21.56 kB (gzipped: 4.73 kB)
- Page-specific CSS: 1-5 kB each

### Bundle Analysis

#### Analyze Current Bundle

```bash
# Install bundle analyzer plugin
pnpm add -D rollup-plugin-visualizer

# Add to vite.config.ts plugins:
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
  plugins: [
    visualizer({
      filename: 'stats.html',
      open: true,
      gzipSize: true,
    }),
  ],
});

# Build with analysis
ppnpm run build

# Or use manual analysis
pnpm dlx vite-bundle-analyzer public/assets/app.js
```

#### Identify Large Dependencies

```bash
# Check node_modules size
du -sh node_modules/* | sort -hr | head -10

# Find large packages
pnpm dlx bundle-size

# Check bundle sizes manually
du -h public/assets/*.js | sort -hr

# Check specific file size
ls -lh public/assets/app.js

# Monitor bundle size changes
watch "du -h public/assets/app.js" public/assets/
```

### Code Splitting Strategies

#### Page-Specific CSS Loading

```php
<!-- In layout template -->
<head>
  <link rel="stylesheet" href="/assets/app.css">

  <?php if ($pageCss): ?>
  <link rel="stylesheet" href="/assets/<?= $pageCss ?>">
  <?php endif; ?>
</head>
```

#### Dynamic JavaScript Imports

```typescript
// Lazy load heavy components
const loadHeavyComponent = async () => {
    const { HeavyComponent } = await import("./components/heavy-component.js");
    return HeavyComponent;
};

// Load on interaction
document.addEventListener("click", async (e) => {
    if (e.target.matches(".load-heavy")) {
        const HeavyComponent = await loadHeavyComponent();
        new HeavyComponent().init();
    }
});
```

## CSS Performance

### Critical CSS Optimization

#### Identify Critical CSS

```bash
# Option 1: Manual extraction (recommended)
# 1. Open DevTools in browser
# 2. Run: document.styleSheets[0].cssRules
# 3. Extract above-the-fold CSS:

# Get all CSS rules as text
Array.from(document.styleSheets[0].cssRules)
  .map(rule => rule.cssText)
  .join('\n')

# Or get specific critical rules (header, hero, etc.)
Array.from(document.styleSheets[0].cssRules)
  .filter(rule => rule.selectorText?.match(/:root|html|body|header|hero/))
  .map(rule => rule.cssText)
  .join('\n')

# 4. Copy result to critical.css

# Example: Get only critical above-the-fold CSS
const criticalSelectors = [
  ':root', '[data-theme="light"]', 'html', 'body',
  'header', '.header', '.header__logo', '.nav',
  '.hero', '.hero__title', '.hero__description',
  'h1', 'h2', 'p', 'a', 'img'
];

Array.from(document.styleSheets[0].cssRules)
  .filter(rule =>
    criticalSelectors.some(sel =>
      rule.selectorText?.includes(sel)
    )
  )
  .map(rule => rule.cssText)
  .join('\n');
```

# Option 2: Use critical (simpler alternative)

pnpm dlx critical public/assets/app.css --url http://localhost:8008 --output critical.css

# Option 3: Use penthouse (more complex)

pnpm dlx penthouse --url http://localhost:8008 --css public/assets/app.css --output critical.css

````

#### Inline Critical CSS
```php
<!-- Inline critical CSS for faster first paint -->
<style>
  /* Critical CSS here */
</style>

<!-- Load non-critical CSS asynchronously -->
<link rel="preload" href="/assets/app.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
````

### CSS Optimization Techniques

#### Minimize CSS Selectors

```css
/* Bad - deeply nested */
.header .nav .nav-item .nav-link {
}

/* Good - flat selectors */
.nav-link {
}
```

#### Use Efficient Properties

```css
/* Use transform instead of changing position */
.animated-element {
    transform: translateX(100px);
    /* Instead of left: 100px */
}

/* Use opacity instead of visibility */
.fade-element {
    opacity: 0;
    /* Instead of visibility: hidden */
}
```

#### Optimize Animations

```css
/* Use transform and opacity for 60fps */
.smooth-animation {
    will-change: transform, opacity;
    transform: translateZ(0); /* Hardware acceleration */
}
```

## JavaScript Performance

### Tree Shaking

#### Ensure Proper Exports

```typescript
// utils.js - Use named exports for better tree shaking
export const initTheme = () => {
    /* ... */
};
export const initNav = () => {
    /* ... */
};

// Instead of default export with everything
export default {
    initTheme,
    initNav,
    // ... other functions
};
```

#### Import Only What's Needed

```typescript
// Good - specific imports
import { initTheme, initNav } from "./utils/app.js";

// Bad - import everything
import * as utils from "./utils/app.js";
```

### Code Optimization

#### Debounce and Throttle

```typescript
// Debounce for search/input
const debounce = (func: Function, wait: number) => {
    let timeout: NodeJS.Timeout;
    return function executedFunction(...args: any[]) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Throttle for scroll events
const throttle = (func: Function, limit: number) => {
    let inThrottle: boolean;
    return function () {
        if (!inThrottle) {
            func.apply(this, arguments);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
};
```

#### Optimize Event Listeners

```typescript
// Use event delegation
document.addEventListener("click", (e) => {
    if (e.target.matches(".nav-link")) {
        // Handle navigation
    } else if (e.target.matches(".card")) {
        // Handle card interaction
    }
});

// Instead of individual listeners
document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", handleNavClick);
});
```

## Asset Optimization

### Image Optimization

#### Modern Image Formats

```php
<!-- Use modern formats with fallbacks -->
<picture>
  <source srcset="image.webp" type="image/webp">
  <source srcset="image.avif" type="image/avif">
  <img src="image.jpg" alt="Description" loading="lazy">
</picture>
```

#### Lazy Loading

```php
<!-- Native lazy loading -->
<img src="image.jpg" alt="Description" loading="lazy">

<!-- Or use Intersection Observer for more control -->
<img src="placeholder.jpg" data-src="image.jpg" alt="Description" class="lazy-load">
```

### Font Optimization

#### Font Loading Strategy

```css
/* Preload critical fonts */
<link rel="preload" href="/fonts/inter.woff2" as="font" type="font/woff2" crossorigin>

/* Font display strategy */
@font-face {
    font-family: "Inter";
    src: url("/fonts/inter.woff2") format("woff2");
    font-display: swap; /* Prevents invisible text */
}
```

#### System Font Stack

```css
/* Use system fonts for better performance */
body {
    font-family:
        -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
```

## Network Performance

### HTTP/2 Optimization

#### Resource Hints

```html
<!-- Preconnect for external domains -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://api.example.com" />

<!-- DNS prefetch for future requests -->
<link rel="dns-prefetch" href="https://cdn.example.com" />

<!-- Preload critical resources -->
<link rel="preload" href="/assets/app.css" as="style" />
<link rel="preload" href="/assets/app.js" as="script" />
```

#### Service Worker for Caching

```typescript
// sw.js - Basic service worker
const CACHE_NAME = "app-v1";
const urlsToCache = ["/assets/app.css", "/assets/app.js", "/assets/main.css"];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache)),
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        }),
    );
});
```

## Monitoring and Measurement

### Performance Metrics

#### Core Web Vitals

```bash
# Use Lighthouse CLI
pnpm dlx lighthouse http://localhost:8008 \
  --output=json \
  --output-path=./lighthouse-results.json

# Or use web-vitals library
pnpm add web-vitals
```

#### Custom Performance Monitoring

```typescript
// Measure specific operations
const measurePerformance = (name: string, fn: Function) => {
    const start = performance.now();
    const result = fn();
    const end = performance.now();
    console.log(`${name}: ${end - start}ms`);
    return result;
};

// Measure render time
document.addEventListener("DOMContentLoaded", () => {
    const renderTime = performance.now();
    console.log(`DOM render time: ${renderTime}ms`);
});
```

### Bundle Size Monitoring

#### Automated Size Tracking

```bash
# Track bundle size over time
#!/bin/bash
echo "$(date),$(du -sh public/assets/app.js | cut -f1)" >> bundle-size-history.csv

# Alert if bundle exceeds threshold
BUNDLE_SIZE=$(du -k public/assets/app.js | cut -f1)
if [ $BUNDLE_SIZE -gt 50 ]; then
  echo "Warning: Bundle size exceeds 50KB"
fi
```

##### Build Performance

```typescript
// vite.config.ts - Optimize build performance
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["lit"], // Separate vendor chunks
                    utils: ["./src/utils/app.js"],
                },
            },
        },
        minify: "esbuild", // Use esbuild instead of terser for faster builds
        sourcemap: false, // Disable in production
        target: "es2015", // Target modern browsers
    },
    plugins: [
        // Add bundle analyzer for development
        process.env.NODE_ENV === "development" &&
            visualizer({
                filename: "stats.html",
                open: true,
                gzipSize: true,
            }),
    ].filter(Boolean),
});
```

## Performance Budgets

### Set Performance Targets

- First Contentful Paint: < 1.5s
- Largest Contentful Paint: < 2.5s
- Time to Interactive: < 3.5s
- Cumulative Layout Shift: < 0.1
- Bundle size: < 50KB (gzipped)

### Budget Enforcement

```json
// package.json - Add budget checks
{
    "scripts": {
        "build:check": "ppnpm run build && bundlesize",
        "bundlesize": "bundlesize"
    },
    "bundlesize": [
        {
            "path": "./public/assets/app.js",
            "maxSize": "50kb"
        },
        {
            "path": "./public/assets/app.css",
            "maxSize": "25kb"
        }
    ]
}
```

## Testing Performance

### Load Testing

```bash
# Use artillery for load testing
pnpm add -g artillery
artillery run load-test-config.yml
```

### Performance Regression Testing

```bash
# Compare performance between builds
pnpm dlx lighthouse http://localhost:8008 --output=json --output-path=before.json
# Make changes
pnpm dlx lighthouse http://localhost:8008 --output=json --output-path=after.json
pnpm dlx lighthouse-diff before.json after.json
```

This performance guide should be reviewed monthly and updated based on real-world performance data and user feedback.
