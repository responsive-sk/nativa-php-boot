# Blog CSS Refactoring Plan

## Problem Summary

Blog hero section overlay is covering navigation menu and articles due to incorrect z-index stacking, missing CSS imports, and **BEM pattern violations**.

---

## Root Causes

### 0. BEM Pattern Violations ⚠️

**File:** `src/use-cases/blog/blog.css`

Current (INCORRECT):
```css
.blog__hero-image { }      /* ❌ Double element */
.blog__hero-content { }    /* ❌ Double element */
```

Correct BEM:
```css
.blog-hero__image { }      /* ✅ Block__Element */
.blog-hero__content { }    /* ✅ Block__Element */
```

**HTML must match:**
```html
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <picture class="blog-hero__picture">
        <img class="blog-hero__image" />
    </picture>
    <div class="blog-hero__content">
```

---

### 1. Z-Index Stacking Issue
**File:** `src/use-cases/blog/blog.css`

Current (broken):
```css
.blog__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;  /* ← Problem: no z-index on parent */
}

.blog__picture {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    /* Missing z-index: 0 */
}

.blog__hero-content {
    position: relative;
    z-index: 2;
    /* Works but parent needs proper stacking context */
}
```

**Fix:** Add explicit z-index to all layers
```css
.blog-hero {
    position: relative;
    isolation: isolate; /* Create new stacking context */
}

.blog__picture {
    z-index: 0; /* Background layer */
}

.blog__overlay {
    z-index: 1; /* Overlay layer */
}

.blog__hero-content {
    z-index: 2; /* Content layer */
}
```

---

### 2. Duplicate CSS / Unused File
**Files:** 
- `src/use-cases/blog/blog.css` (used)
- `src/use-cases/blog/blog-hero.css` (NOT used)

**Problem:** `blog-hero.css` exists but is never imported in `blog.ts`

**Solution Options:**

**Option A: Delete blog-hero.css** (Recommended)
```bash
rm src/use-cases/blog/blog-hero.css
```

**Option B: Import in blog.ts**
```typescript
// src/blog.ts
import "./use-cases/blog/blog.css";
import "./use-cases/blog/blog-hero.css"; // Add this
```

**Recommendation:** Option A - Merge all blog styles into `blog.css` for simplicity.

---

### 3. Design Tokens Not Used
**File:** `src/use-cases/blog/blog.css`

Current (violates design tokens):
```css
.blog__overlay {
    background: rgba(0, 0, 0, 0.5); /* Hardcoded */
}
```

Fixed (uses tokens):
```css
.blog__overlay {
    background: var(--overlay-bg); /* Uses design token */
}
```

**Required token in `src/styles/tokens.css`:**
```css
:root {
    --overlay-bg: rgba(0, 0, 0, 0.5);
    --overlay-bg-dark: rgba(0, 0, 0, 0.7);
    --overlay-bg-light: rgba(255, 255, 255, 0.9);
}
```

---

### 4. Build Hash Mismatch
**File:** `src/interfaces/Templates/front/layouts/cms.php`

Problem:
```php
$pageSpecificCssFile = match($page) {
    'blog' => 'blog.D9K_1enD.css', // ← Hardcoded, will break on next build
    // ...
};
```

**Solution:** Read from Vite manifest.json

Create AssetHelper method:
```php
public static function css(string $asset): string
{
    $manifest = self::loadManifest();
    
    // For page-specific CSS, read from manifest
    $pageMap = [
        'home' => 'home.ts',
        'blog' => 'blog.ts',
        'portfolio' => 'portfolio.ts',
        // ...
    ];
    
    if (isset($pageMap[$asset])) {
        $entry = $manifest[$pageMap[$asset]];
        if (isset($entry['css'])) {
            return '/assets/' . $entry['css'][0];
        }
    }
    
    // Fallback...
}
```

Usage in cms.php:
```php
$pageSpecificCssUrl = AssetHelper::pageCss($page);
```

---

## Implementation Tasks

### Task 0: Fix BEM Naming

**Files:**
- `src/use-cases/blog/blog.css`
- `src/interfaces/Templates/front/pages/blog.php`
- `src/interfaces/Templates/front/pages/blog/show.php`

**CSS Changes:**
```css
/* OLD (wrong) */
.blog__overlay { }
.blog__picture { }
.blog__hero-image { }
.blog__hero-content { }

/* NEW (correct BEM) */
.blog-hero { }              /* Block */
.blog-hero__overlay { }     /* Element */
.blog-hero__picture { }     /* Element */
.blog-hero__image { }       /* Element */
.blog-hero__content { }     /* Element */
```

**Template Changes:**
```php
<!-- OLD -->
<section class="blog-hero">
    <div class="blog__overlay"></div>
    <picture class="blog__picture">
        <img class="blog__hero-image" />
    </picture>
    <div class="blog__hero-content">

<!-- NEW -->
<section class="blog-hero">
    <div class="blog-hero__overlay"></div>
    <picture class="blog-hero__picture">
        <img class="blog-hero__image" />
    </picture>
    <div class="blog-hero__content">
```

### Task 1: Fix Z-Index Stacking
**File:** `src/use-cases/blog/blog.css`

Changes:
- Add `isolation: isolate` to `.blog-hero`
- Add `z-index: 0` to `.blog__picture`
- Keep `z-index: 1` for `.blog__overlay`
- Keep `z-index: 2` for `.blog__hero-content`

### Task 2: Add Design Tokens
**File:** `src/styles/tokens.css`

Add missing tokens:
```css
:root {
    --overlay-bg: rgba(0, 0, 0, 0.5);
    --overlay-bg-dark: rgba(0, 0, 0, 0.7);
    --overlay-bg-light: rgba(255, 255, 255, 0.9);
    
    --z-base: 0;
    --z-dropdown: 100;
    --z-sticky: 200;
    --z-fixed: 300;
    --z-modal-backdrop: 400;
    --z-modal: 500;
    --z-popover: 600;
    --z-tooltip: 700;
}
```

Update blog.css to use tokens:
```css
.blog__overlay {
    background: var(--overlay-bg);
}

.blog__picture {
    z-index: var(--z-base);
}

.blog__overlay {
    z-index: var(--z-dropdown);
}

.blog__hero-content {
    z-index: var(--z-sticky);
}
```

### Task 3: Remove Unused File
**File:** `src/use-cases/blog/blog-hero.css`

Action: Delete file

### Task 4: Dynamic Asset Loading
**Files:**
- `src/infrastructure/View/AssetHelper.php`
- `src/interfaces/Templates/front/layouts/cms.php`

Add method to AssetHelper:
```php
public static function pageCss(string $page): ?string
{
    $manifest = self::loadManifest();
    
    $pageMap = [
        'home' => 'home.ts',
        'blog' => 'blog.ts',
        'portfolio' => 'portfolio.ts',
        'contact' => 'contact.ts',
        'docs' => 'docs.ts',
        'services' => 'services.ts',
        'pricing' => 'pricing.ts',
    ];
    
    if (!isset($pageMap[$page])) {
        return null;
    }
    
    $entry = $manifest[$pageMap[$page]] ?? null;
    if (!$entry || !isset($entry['css']) || empty($entry['css'])) {
        return null;
    }
    
    return '/assets/' . $entry['css'][0];
}
```

Update cms.php:
```php
$pageSpecificCssUrl = AssetHelper::pageCss($page);

if ($pageSpecificCssUrl) {
    echo '<link rel="stylesheet" href="' . $pageSpecificCssUrl . '">';
}
```

---

## Testing Checklist

- [ ] Blog hero image doesn't cover navigation
- [ ] Overlay is visible on hero image
- [ ] Hero content (title, subtitle) is above overlay
- [ ] Dark theme works correctly
- [ ] Light theme works correctly
- [ ] Mobile responsive layout works
- [ ] Build produces correct CSS hash
- [ ] Manifest.json is read correctly
- [ ] No console errors

---

## Files to Change

1. `src/use-cases/blog/blog.css` - Fix z-index, use tokens
2. `src/styles/tokens.css` - Add overlay and z-index tokens
3. `src/use-cases/blog/blog-hero.css` - DELETE
4. `src/infrastructure/View/AssetHelper.php` - Add pageCss() method
5. `src/interfaces/Templates/front/layouts/cms.php` - Use dynamic CSS loading

---

## Estimated Time

- Task 0 (BEM): 20 min
- Task 1 (Z-Index): 10 min
- Task 2 (Design Tokens): 15 min
- Task 3 (Delete Unused): 2 min
- Task 4 (Dynamic Assets): 30 min
- Testing: 15 min

**Total:** ~1.5 hours

---

## Priority

**CRITICAL** - Blog page is currently broken (hero covers navigation)
**BEM Violation** - Must fix for maintainability
