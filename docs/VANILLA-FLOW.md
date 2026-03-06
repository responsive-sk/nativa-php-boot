# Vanilla TypeScript/JavaScript/CSS Flow

## 📊 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     VITE BUILD SYSTEM                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  STYLES (CSS)                                                │
│  ├── styles/tokens.css ← Master tokens                      │
│  │   └── shared/design-tokens.css ← Copy for vanilla       │
│  │       └── Imported by all page CSS                      │
│  │                                                           │
│  ├── styles/components.css ← BEM components (Svelte)       │
│  │                                                           │
│  └── src/frontend/pages/*.css ← Page-specific styles       │
│      ├── home.css                                           │
│      ├── blog.css                                           │
│      ├── portfolio.css                                      │
│      └── ... (all import design-tokens.css)                │
│                                                              │
│  SCRIPTS (TS/JS)                                             │
│  ├── src/init.js ← Core initialization                      │
│  ├── src/app.ts ← Main application                          │
│  │                                                           │
│  └── src/frontend/pages/*.ts ← Page-specific logic         │
│      ├── home.ts                                            │
│      ├── blog.ts                                            │
│      ├── portfolio.ts                                       │
│      └── ...                                                │
│                                                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
                    Vite Build
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                  PUBLIC ASSETS (Output)                      │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  CSS Files:                                                  │
│  ├── home.HASH.css                                          │
│  ├── blog.HASH.css                                          │
│  ├── portfolio.HASH.css                                     │
│  └── ... (each page has hashed CSS)                        │
│                                                              │
│  JS Files:                                                   │
│  ├── core-init.HASH.js                                      │
│  ├── core-app.HASH.js                                       │
│  ├── home.HASH.js                                           │
│  ├── blog.HASH.js                                           │
│  └── ... (each page has hashed JS)                         │
│                                                              │
│  Manifest:                                                   │
│  └── manifest.json ← Maps source → hashed files            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
                            ↓
                    PHP Template (AssetHelper)
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    HTML OUTPUT                               │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  <head>                                                      │
│    <link rel="stylesheet" href="/assets/home.HASH.css">    │
│  </head>                                                     │
│  <body>                                                      │
│    <!-- PHP rendered content -->                            │
│    <script type="module" src="/assets/core-init.js"></script>
│    <script type="module" src="/assets/home.js"></script>    │
│  </body>                                                     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎨 Design Tokens Flow

### Token Structure

```
styles/tokens.css (Master - all in src/)
    ↓ (imported by)
src/frontend/pages/home.css
src/frontend/pages/blog.css
src/frontend/pages/portfolio.css
...
```

**No more copying!** All files import directly from `../styles/tokens.css`

### Token Usage Example

**tokens.css:**
```css
:root {
  --color-primary-500: #3b82f6;
  --spacing-4: 1rem;
  --font-size-lg: 1.25rem;
}
```

**Page CSS (home.css):**
```css
@import '../../styles/shared/design-tokens.css';

.home-hero {
  padding: var(--spacing-4);
  color: var(--color-primary-500);
  font-size: var(--font-size-lg);
}
```

---

## 📁 File Structure

```
src/Templates/
└── src/                    ← Všetky zdroje TU
    ├── styles/
    │   ├── tokens.css              ← Master design tokens
    │   ├── components.css          ← BEM components (Svelte)
    │   ├── utilities.css           ← Utility classes
    │   └── *.css                   ← Other shared styles
    │
    ├── init.js                 ← Core init (theme, etc.)
    ├── app.ts                  ← Main app logic
    ├── css.ts                  ← CSS imports
    │
    └── frontend/
        ├── pages/
        │   ├── home.ts         ← Page logic
        │   ├── home.css        ← Page styles (imports ../styles/tokens.css)
        │   ├── blog.ts
        │   ├── blog.css
        │   └── ...
        │
        └── use-cases/
            ├── docs/
            │   ├── docs.ts
            │   └── docs.css
            └── ...
```

---

## 🔧 Build Process

### 1. CSS Build

```
src/frontend/pages/home.css
    + styles/shared/design-tokens.css
    ↓ (Vite + PostCSS)
public/assets/home.HASH.css
```

**What gets included:**
- ✅ Design tokens (CSS custom properties)
- ✅ Page-specific styles
- ✅ Animations
- ✅ Media queries

### 2. JS/TS Build

```
src/frontend/pages/home.ts
    + dependencies (HTMX, Lit, etc.)
    ↓ (Vite + TypeScript)
public/assets/home.HASH.js
```

**What gets included:**
- ✅ Page-specific logic
- ✅ Event listeners
- ✅ Animations
- ✅ Component imports

### 3. Manifest Generation

```json
{
  "frontend/pages/home.ts": {
    "file": "home.HASH.js",
    "css": ["home.HASH.css"]
  },
  "frontend/pages/blog.ts": {
    "file": "blog.HASH.js",
    "css": ["blog.HASH.css"]
  }
}
```

---

## 🎯 Page Loading Flow

### Example: Homepage

**1. PHP Template (pages/home.php):**
```php
<?php
$coreCss = AssetHelper::css('core-css');
$homeCss = AssetHelper::css('home');
$coreJs = AssetHelper::js('core-init');
$homeJs = AssetHelper::js('home');
?>

<link rel="stylesheet" href="<?= $coreCss ?>">
<link rel="stylesheet" href="<?= $homeCss ?>">

<script type="module" src="<?= $coreJs ?>"></script>
<script type="module" src="<?= $homeJs ?>"></script>
```

**2. Browser Loads:**
```
1. core-css.HASH.css     → Design tokens + base styles
2. home.HASH.css         → Homepage-specific styles
3. core-init.HASH.js     → Theme, core logic
4. home.HASH.js          → Homepage interactions
```

**3. Execution Order:**
```
1. CSS parsed → Design tokens available
2. core-init.js → Theme initialized
3. home.js → Page-specific logic runs
```

---

## 🎨 Token Categories

### Color Tokens
```css
--color-primary-500: #3b82f6;
--color-neutral-0: #ffffff;
--color-neutral-900: #111827;
--color-success: #10b981;
--color-error: #ef4444;
```

### Spacing Tokens
```css
--spacing-0: 0;
--spacing-1: 0.25rem;   /* 4px */
--spacing-4: 1rem;      /* 16px */
--spacing-8: 2rem;      /* 32px */
```

### Typography Tokens
```css
--font-size-base: 1rem;
--font-size-lg: 1.25rem;
--font-weight-semibold: 600;
--line-height-normal: 1.5;
```

### Border Tokens
```css
--radius-lg: 0.5rem;
--radius-xl: 0.75rem;
--border-width-1: 1px;
```

### Shadow Tokens
```css
--shadow-md: 0 4px 6px rgba(0,0,0,0.1);
--shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
```

---

## 📋 Checklist: Vanilla vs Svelte

| Feature | Vanilla Flow | Svelte Flow |
|---------|-------------|-------------|
| **CSS** | `@import 'design-tokens.css'` | Uses `design-system.css` |
| **Components** | Hand-coded HTML/CSS | BEM `.svelte` components |
| **JS Logic** | TypeScript files | Svelte runes + JS |
| **Build Output** | Per-page CSS/JS | Shared + page-specific |
| **State** | Vanilla JS | Svelte `$state()` |
| **Best For** | Static pages, animations | Interactive components |

---

## 🚀 Usage Examples

### Adding a New Page

**1. Create CSS:**
```css
/* src/frontend/pages/my-page.css */
@import '../../styles/shared/design-tokens.css';

.my-page {
  padding: var(--spacing-8);
  background: var(--color-bg-primary);
}
```

**2. Create TS:**
```typescript
// src/frontend/pages/my-page.ts
console.log('My page loaded!');

// Add interactions
document.querySelector('.my-button')?.addEventListener('click', () => {
  // Handle click
});
```

**3. Add to Vite Config:**
```typescript
// vite.config.ts
input: {
  'my-page': resolve(__dirname, 'src/frontend/pages/my-page.ts'),
}
```

**4. Use in PHP:**
```php
<?php
$myPageCss = AssetHelper::css('my-page');
$myPageJs = AssetHelper::js('my-page');
?>

<link rel="stylesheet" href="<?= $myPageCss ?>">
<script type="module" src="<?= $myPageJs ?>"></script>
```

---

## 📊 Current Token Usage

**Files using design tokens:**
- ✅ home.css
- ✅ blog.css
- ✅ portfolio.css
- ✅ contact.css
- ✅ about.css
- ✅ services.css
- ✅ pricing.css
- ✅ docs.css
- ✅ articles.css

**Total:** 9 page stylesheets using shared tokens

---

## 🔍 Debugging

### Check if tokens are loaded:
```javascript
// In browser console
getComputedStyle(document.documentElement)
  .getPropertyValue('--color-primary-500');
// Should return: #3b82f6
```

### Check which CSS is loaded:
```javascript
// In browser console
document.styleSheets
// Lists all loaded stylesheets
```

### Check manifest mapping:
```bash
cat public/assets/manifest.json | grep 'my-page'
```

---

**Last Updated:** 2026-03-06  
**Status:** ✅ Active (alongside Svelte hybrid)
