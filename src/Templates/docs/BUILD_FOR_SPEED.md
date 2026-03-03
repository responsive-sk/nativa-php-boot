# Build for Speed

Performance-first architecture for the Nativa CMS frontend.

## Core Principle

> **"Core + Page-Specific"**
>
> Split bundles into shared core (loaded on every page) and page-specific features (loaded only when needed).

---

## Architecture

```
Frontend/
├── Core (Always Loaded)
│   ├── core-init.js     # Theme initialization (<1KB)
│   ├── core-app.js      # Shared utilities (~5KB)
│   └── core-css.css     # Design tokens + utilities (~5KB)
│
├── Page-Specific (Loaded Per Page)
│   ├── home.js          # Homepage features (GSAP, etc.)
│   ├── home.css         # Homepage-specific styles
│   ├── docs.js          # Docs features (search, etc.)
│   ├── docs.css         # Docs-specific styles
│   └── ...
│
└── Vendor (Chunked)
    └── vendor.js        # Third-party libs (GSAP, etc.)
```

---

## Bundle Strategy

### Core Bundle (Always Loaded)

**JavaScript (`core-app.js`):**
- CSRF token management
- Theme toggle (dark/light)
- Mobile menu navigation
- Smooth scroll
- Notifications system
- Alerts

**CSS (`core-css.css`):**
- Design tokens (`tokens.css`)
- Reset & normalize
- Utility classes
- Shared components (buttons, header, footer)

**Target Size:** <10KB gzipped

### Page-Specific Bundles

Loaded ONLY on pages that need them:

| Page | Features | Estimated Size |
|------|----------|----------------|
| Home | GSAP animations, cookie consent, gallery, parallax | ~15KB |
| Docs | Search, syntax highlighting, back-to-top | ~8KB |
| Blog | Article interactions, reading progress | ~5KB |
| Contact | Form validation, map integration | ~6KB |

---

## Implementation

### Vite Configuration

```typescript
// vite.config.ts
rollupOptions: {
  input: {
    // Core - always loaded
    'core-init': './src/init.js',
    'core-app': './src/app.ts',
    'core-css': './src/css.ts',
    
    // Page-specific
    'home': './src/frontend/pages/home.ts',
    'docs': './src/frontend/pages/docs.ts',
    'blog': './src/frontend/pages/blog.ts',
  }
}
```

### Template Integration

```php
// In layout (frontend.php)
$themeInitJs = AssetHelper::js('init.js');    // Core init
$cssBundle = AssetHelper::css('css.css');     // Core CSS
$appJs = AssetHelper::js('app.js');           // Core JS

// Page-specific (only if exists)
$pageSpecificCssUrl = AssetHelper::pageCss($page);
$pageSpecificJsUrl = AssetHelper::js($page . '.js');
```

---

## Dark/Light Theme Consistency

### Critical Rule

> **Theme must be consistent across the entire page.**
> No mixing dark header with light content or vice versa.

### Implementation

1. **Theme detected BEFORE CSS loads:**
```html
<head>
  <script src="init.js"></script>  <!-- Sets data-theme attribute -->
  <link rel="stylesheet" href="core-css.css">
</head>
```

2. **CSS variables flip together:**
```css
:root[data-theme="dark"] {
  --color-bg: #0a0a0a;
  --color-text: #ffffff;
  --color-border: #2d2d2d;
}

:root[data-theme="light"] {
  --color-bg: #ffffff;
  --color-text: #1a1a2e;
  --color-border: #e2e8f0;
}
```

3. **No inline theme overrides:**
```css
/* ❌ BAD - breaks consistency */
.header {
  background: #0a0a0a; /* Always dark */
}

/* ✅ GOOD - uses theme token */
.header {
  background: var(--color-bg); /* Flips with theme */
}
```

---

## Performance Targets

### Lighthouse Scores

Target: **100/100/100/100**

| Metric | Target | Strategy |
|--------|--------|----------|
| Performance | 100 | Code splitting, lazy loading |
| Accessibility | 100 | Semantic HTML, ARIA |
| Best Practices | 100 | HTTPS, no deprecated APIs |
| SEO | 100 | Meta tags, structured data |

### Bundle Size Limits

| Bundle | Max Size (gzipped) |
|--------|-------------------|
| Core CSS | <5KB |
| Core JS | <10KB |
| Page JS | <20KB |
| Page CSS | <10KB |
| Vendor | <50KB |

---

## Code Splitting Patterns

### Good Example

```typescript
// ❌ BAD - loads GSAP on every page
import { gsap } from 'gsap';
import { initTheme } from '@ui/theme';

// ✅ GOOD - only core in core-app.ts
import { initTheme } from '@ui/theme';

// ✅ GOOD - GSAP only in home.ts
import { gsap } from 'gsap';
```

### Page-Specific Features

```typescript
// home.ts - Homepage only
import { gsap } from 'gsap';
import { initParallax } from '@effects/parallax';
import { initCookieConsent } from '@components/cookieConsent';

// docs.ts - Documentation only
import { initSearch } from './search';
import { initCodeBlocks } from './codeBlocks';
```

---

## Build Commands

```bash
# Development (with sourcemaps)
pnpm run dev

# Production (optimized)
pnpm run build

# Analyze bundle sizes
pnpm run analyze

# Check performance
pnpm run lighthouse
```

---

## Monitoring

### Bundle Size Tracking

```bash
# After build, check sizes
ls -lh public/assets/*.js | sort -hr
ls -lh public/assets/*.css | sort -hr
```

### Performance Monitoring

- Lighthouse CI in production pipeline
- Web Vitals tracking (LCP, FID, CLS)
- Bundle size budgets in CI

---

## Best Practices

1. **Keep core minimal** - Only what's truly needed on every page
2. **Lazy load everything else** - Features, images, routes
3. **Use design tokens** - Consistent theming, easy maintenance
4. **Test on slow networks** - 3G simulation in DevTools
5. **Monitor production** - Real user metrics (RUM)

---

## Resources

- [Vite Code Splitting Guide](https://vitejs.dev/guide/build.html#code-splitting)
- [Web Performance Best Practices](https://web.dev/performance/)
- [Lighthouse Scoring Calculator](https://googlechrome.github.io/lighthouse/scorecalc/)
