# Assets Flow Documentation

## Overview

This document describes how CSS and JavaScript assets are built, hashed, and loaded in the Nativa CMS using Vite and AssetHelper.

## Architecture

```
+-------------------+     +--------------+     +-------------------+
|  Source Files     |---->|  Vite Build  |---->|  Public Assets    |
|  (.ts, .css)      |     |              |     |  (hashed files)   |
+-------------------+     +--------------+     +-------------------+
                               |
                               v
                        +--------------+
                        | manifest.json|
                        +--------------+
                               |
                               v
                        +--------------+
                        | AssetHelper  |
                        | (PHP)        |
                        +--------------+
                               |
                               v
                        +--------------+
                        | Templates    |
                        | (PHP views)  |
                        +--------------+
```

## File Structure

### Source Files
```
src/Templates/src/
├── frontend/use-cases/
│   ├── admin/
│   │   ├── admin.ts      # JavaScript entry point
│   │   └── admin.css     # Styles (imported by admin.ts)
│   └── auth/
│       ├── auth.ts
│       └── auth.css
├── init.js                # Core initialization
├── app.ts                 # Shared JavaScript
└── css.ts                 # Shared CSS
```

### Built Assets
```
public/assets/
├── admin.B7BK8-Lz.js      # Hashed JavaScript
├── admin.Dv6V3LyM.css     # Hashed CSS
├── auth.BMoBR4cc.js
├── auth.gY3oVpOW.css
├── core-init.CM62Fm_M.js
├── core-css.Tmr-WscG.css
└── manifest.json          # Asset manifest
```

## Vite Configuration

### Entry Points
```typescript
// vite.config.ts
export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        // Core
        'core-init': 'src/init.js',
        'core-app': 'src/app.ts',
        'core-css': 'src/css.ts',
        
        // Feature modules
        'admin': 'src/frontend/use-cases/admin/admin.ts',
        'auth': 'src/frontend/use-cases/auth/auth.ts',
      }
    }
  }
})
```

### Output Naming
```typescript
output: {
  entryFileNames: '[name].[hash].js',
  assetFileNames: (assetInfo) => {
    if (assetInfo.name.endsWith('.css')) {
      return isProd ? '[name].[hash].css' : '[name].css';
    }
    // ... other asset types
  }
}
```

## Asset Manifest

Vite generates `manifest.json` that maps source files to hashed output files:

```json
{
  "frontend/use-cases/admin/admin.ts": {
    "file": "admin.B7BK8-Lz.js",
    "name": "admin",
    "src": "frontend/use-cases/admin/admin.ts",
    "isEntry": true,
    "css": [
      "admin.Dv6V3LyM.css"
    ]
  },
  "frontend/use-cases/auth/auth.ts": {
    "file": "auth.BMoBR4cc.js",
    "name": "auth",
    "src": "frontend/use-cases/auth/auth.ts",
    "isEntry": true,
    "css": [
      "auth.gY3oVpOW.css"
    ]
  }
}
```

## AssetHelper (PHP)

The `AssetHelper` class resolves asset names to their hashed filenames using the manifest.

### Usage in Templates

```php
<?php
use Infrastructure\View\AssetHelper;

$adminCss = AssetHelper::css('admin');
$adminJs = AssetHelper::js('admin');
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?= $adminCss ?>">
    <!-- Outputs: /assets/admin.Dv6V3LyM.css -->
</head>
<body>
    <!-- Content -->
    
    <script src="<?= $adminJs ?>" defer></script>
    <!-- Outputs: /assets/admin.B7BK8-Lz.js -->
</body>
</html>
```

### How AssetHelper Works

#### CSS Resolution
```php
AssetHelper::css('admin')
```

1. Load `manifest.json`
2. Try possible keys:
   - `admin`
   - `admin.ts`
   - `admin.css`
   - `frontend/use-cases/admin/admin.ts`
3. Find entry: `frontend/use-cases/admin/admin.ts`
4. Extract CSS from `css` array: `admin.Dv6V3LyM.css`
5. Return: `/assets/admin.Dv6V3LyM.css`

#### JS Resolution
```php
AssetHelper::js('admin')
```

1. Load `manifest.json`
2. Try possible keys:
   - `admin`
   - `admin.ts`
   - `admin.js`
   - `frontend/use-cases/admin/admin.ts`
3. Find entry: `frontend/use-cases/admin/admin.ts`
4. Extract file: `admin.B7BK8-Lz.js`
5. Return: `/assets/admin.B7BK8-Lz.js`

## Design Tokens

Design tokens are the visual design atoms of the design system. They store design decisions as variables for reuse across the codebase.

### Token Categories

```css
:root {
    /* Colors */
    --bg: #0b0f17;
    --card: #121a26;
    --text: #e6edf3;
    --muted: #9aa9b6;
    --accent: #7c5cff;
    --border: #233044;
    --ok: #2bd576;
    --warn: #ffcc66;
    
    /* Spacing */
    --space-1: 0.25rem;   /* 4px */
    --space-2: 0.5rem;    /* 8px */
    --space-3: 0.75rem;   /* 12px */
    --space-4: 1rem;      /* 16px */
    --space-6: 1.5rem;    /* 24px */
    --space-8: 2rem;      /* 32px */
    
    /* Typography */
    --font-sans: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
    --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas;
    
    /* Border Radius */
    --radius-md: 10px;
    --radius-lg: 12px;
    --radius-xl: 14px;
    
    /* Shadows */
    --shadow-card: 0 12px 30px rgba(0, 0, 0, 0.25);
    
    /* Transitions */
    --transition-fast: 0.2s;
    --transition-slow: 0.3s;
}
```

### Usage in CSS

```css
/* Good - uses tokens */
.card {
    background: var(--card);
    border-radius: var(--radius-xl);
    padding: var(--space-4);
    box-shadow: var(--shadow-card);
}

/* Bad - hardcoded values */
.card {
    background: #121a26;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
}
```

### Theme Support

Design tokens enable easy theme switching:

```css
/* Dark theme (default) */
:root {
    --bg: #0b0f17;
    --text: #e6edf3;
    --card: #121a26;
}

/* Light theme */
body.light-mode {
    --bg: #f5f7fa;
    --text: #1e293b;
    --card: #ffffff;
}
```

### Token Files

```
src/Templates/src/styles/shared/
└── design-tokens.css    # Global design tokens
```

Import in your component CSS:

```css
@import '../../styles/shared/design-tokens.css';
```

## BEM Methodology

BEM (Block Element Modifier) is a naming convention for CSS classes that helps create reusable components and shared code in front-end development.

### BEM Structure

```
.block {}
.block__element {}
.block--modifier {}
```

- **Block**: Standalone entity that is meaningful on its own (e.g., `card`, `menu`, `button`)
- **Element**: Part of a block that has no standalone meaning (e.g., `card__title`, `menu__item`)
- **Modifier**: Flag on a block or element that changes appearance or behavior (e.g., `button--primary`, `card--highlighted`)

### Examples from Nativa CMS

#### Block
```css
.theme-toggle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}
```

#### Element
```css
.theme-toggle__icon {
    width: 20px;
    height: 20px;
}

.theme-toggle__label {
    font-size: 12px;
}
```

#### Modifier
```css
.theme-toggle--light {
    background: rgba(255, 255, 255, 0.2);
}

.theme-toggle__icon--sun {
    opacity: 1;
}
```

### HTML Structure

```html
<!-- Block -->
<button class="theme-toggle" aria-label="Toggle theme">
    <!-- Elements -->
    <svg class="theme-toggle__icon theme-toggle__icon--sun">...</svg>
    <svg class="theme-toggle__icon theme-toggle__icon--moon">...</svg>
</button>

<!-- Block with modifier -->
<button class="theme-toggle theme-toggle--light">
    <svg class="theme-toggle__icon">...</svg>
</button>
```

### BEM Best Practices

1. **Use double underscores for elements**
   ```css
   /* Good */
   .card__title { }
   .card__content { }
   
   /* Bad */
   .card-title { }
   .card.content { }
   ```

2. **Use double hyphens for modifiers**
   ```css
   /* Good */
   .button--primary { }
   .button--disabled { }
   
   /* Bad */
   .button-primary { }
   .button_primary { }
   ```

3. **Don't reflect DOM structure in BEM names**
   ```html
   <!-- Good - flat structure -->
   <div class="card">
       <h2 class="card__title">Title</h2>
       <p class="card__content">Content</p>
   </div>
   
   <!-- Bad - nested BEM -->
   <div class="card">
       <h2 class="card__title__text">Title</h2>
   </div>
   ```

4. **Modifiers can be used without blocks**
   ```css
   /* When modifier changes meaning significantly */
   .checkbox--checked { }
   ```

### BEM + Design Tokens

Combine BEM with design tokens for maximum reusability:

```css
.card {
    background: var(--card);
    border-radius: var(--radius-xl);
    padding: var(--space-4);
}

.card--highlighted {
    border-color: var(--accent);
    box-shadow: var(--shadow-card);
}

.card__title {
    color: var(--text);
    font-size: var(--font-size-lg);
}
```

## Best Practices

### 1. Import CSS in TypeScript
```typescript
// admin.ts
import './admin.css';  // CSS will be bundled with JS

// Theme Toggle logic
function initThemeToggle() {
  // ...
}
```

### 2. Use AssetHelper in Templates
```php
// Good - uses AssetHelper
$css = AssetHelper::css('admin');
$js = AssetHelper::js('admin');

// Bad - hardcoded path
<link rel="stylesheet" href="/assets/admin.css">
<script src="/assets/admin.js"></script>
```

### 3. Naming Conventions
- Use lowercase with hyphens: `admin`, `auth`, `core-init`
- Match entry point names: `admin.ts` -> `AssetHelper::js('admin')`
- Keep CSS and JS in same directory: `admin.ts` + `admin.css`

### 4. Build Commands
```bash
# Development (with HMR)
pnpm run dev

# Production (minified, hashed)
pnpm run build:prod
```

### 5. Cache Busting
- Hashes change when content changes
- Browser cache automatically invalidated
- No manual version management needed

## Troubleshooting

### CSS 404 Error
**Problem:** `GET /assets/admin.css 404`

**Solution:**
1. Check `manifest.json` has correct entry
2. Ensure CSS is imported in TS: `import './admin.css'`
3. Use correct AssetHelper call: `AssetHelper::css('admin')`

### JS 404 Error
**Problem:** `GET /assets/admin.js 404`

**Solution:**
1. Check `manifest.json` entry exists
2. Verify AssetHelper returns hashed name
3. Rebuild if manifest is outdated

### Hashes Not Changing
**Problem:** Content changed but hash stays same

**Solution:**
1. Clean build: `rm -rf public/assets && pnpm run build:prod`
2. Check file timestamps
3. Verify Vite config has `[hash]` in output

## Example: Adding New Feature

### 1. Create Source Files
```typescript
// src/frontend/use-cases/blog/blog.ts
import './blog.css';

console.log('Blog module loaded');
```

```css
/* src/frontend/use-cases/blog/blog.css */
.blog-post {
  background: var(--card);
  border-radius: var(--radius);
}
```

### 2. Update Vite Config
```typescript
// vite.config.ts
input: {
  // ...existing
  'blog': 'src/frontend/use-cases/blog/blog.ts',
}
```

### 3. Build
```bash
pnpm run build:prod
```

### 4. Use in Template
```php
<?php
$blogCss = AssetHelper::css('blog');
$blogJs = AssetHelper::js('blog');
?>

<link rel="stylesheet" href="<?= $blogCss ?>">
<script src="<?= $blogJs ?>" defer></script>
```

## Summary

1. **Source files** (`.ts`, `.css`) are in `src/Templates/src/`
2. **Vite builds** them to `public/assets/` with hashes
3. **Manifest** maps source to hashed output
4. **AssetHelper** reads manifest and returns correct URLs
5. **Templates** use AssetHelper for all asset links

This flow ensures:
- Automatic cache busting
- Minification in production
- Type safety with TypeScript
- Clean separation of concerns
- No hardcoded asset paths
- Reusable components with BEM
- Consistent design with design tokens
