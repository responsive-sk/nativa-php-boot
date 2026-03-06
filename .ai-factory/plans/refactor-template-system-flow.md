# Template System Refactor Plan

**Branch:** `refactor/template-system-flow`  
**Created:** 2026-03-06  
**Priority:** CRITICAL - Breaking current pages

---

## Problem Analysis

### Current Issues

1. **Inconsistent Template Structure:**
   - Some templates in `pages/frontend/` (home.php, blog.php, contact.php)
   - Some templates in `pages/` (login.php, register.php, form.php)
   - Some templates in `pages/articles/` (index.php, show.php)
   - Mixed usage patterns causing confusion

2. **Layout Flow Broken:**
   - Action → Layout → Page Content flow not working correctly
   - `pages/frontend/home.php` is a full page, not a content partial
   - Layout expects `yieldContent()` but pages don't use it properly

3. **Missing Partials:**
   - No header partial (navigation is hardcoded in layout)
   - No footer partial
   - Limited reusability of components

4. **Template Path Inconsistencies:**
   - `'frontend'` layout → `layouts/frontend.php` ✓
   - `'pages/login'` → `pages/login.php` ✓
   - `'pages/frontend/home'` → `pages/frontend/home.php` ✓
   - But structure doesn't match the flow

---

## Target Architecture

### Template Flow

```
Action Controller
    ↓
TemplateRenderer::render(template, data, layout)
    ↓
Layout (layouts/frontend.php)
    ├─ Header Partial (partials/header.php)
    ├─ yieldContent() ← Page Content
    └─ Footer Partial (partials/footer.php)
    ↓
Page Template (pages/frontend/home.php)
    └─ Can include sub-partials (partials/hero-home.php)
```

### Directory Structure

```
src/Templates/
├── layouts/
│   ├── frontend.php       # Main frontend layout with header/footer
│   ├── admin.php          # Admin layout
│   └── base.php           # Minimal layout (for API/special pages)
│
├── partials/              # Reusable components
│   ├── header.php         # Navigation header
│   ├── footer.php         # Footer
│   ├── hero-home.php      # Homepage hero
│   ├── hero-blog.php      # Blog hero
│   ├── nav-primary.php    # Primary navigation
│   ├── nav-mobile.php     # Mobile navigation
│   └── ...
│
├── pages/                 # Page templates
│   ├── home.php           # Homepage (content only, wrapped by layout)
│   ├── login.php          # Login page
│   ├── register.php       # Register page
│   ├── form.php           # Dynamic form page
│   │
│   ├── frontend/          # Frontend content pages
│   │   ├── blog.php       # Blog listing
│   │   ├── contact.php    # Contact page
│   │   ├── portfolio.php  # Portfolio
│   │   ├── docs.php       # Documentation
│   │   ├── about.php      # About page
│   │   ├── services.php   # Services
│   │   ├── pricing.php    # Pricing
│   │   │
│   │   ├── blog/          # Blog sub-pages
│   │   │   ├── show.php   # Article detail
│   │   │   └── search.php # Search results
│   │   │
│   │   └── articles/      # Articles sub-pages
│   │       ├── index.php  # Articles listing
│   │       └── show.php   # Article detail
│   │
│   └── admin/             # Admin pages
│       └── dashboard.php  # Admin dashboard
│
└── errors/                # Error pages (no layout)
    ├── 404.php
    └── 500.php
```

---

## Refactor Tasks

### Phase 1: Create Missing Partials (Tasks 1-3)

#### Task 1: Create Header Partial
**Status:** ⏳ Pending

**File:** `src/Templates/partials/header.php`

**Content:**
```php
<?php
/**
 * Header Partial - Primary Navigation
 *
 * @var string $page Current page identifier
 * @var bool   $isGuest User authentication state
 */
$page ??= 'home';
$isGuest ??= true;
?>

<header class="site-header">
  <nav class="nav-primary">
    <div class="container">
      <a href="/" class="nav-primary__logo">
        <span>Nativa</span>
        <span class="nav-primary__logo-dot">•</span>
        <span>CMS</span>
      </a>
      
      <ul class="nav-primary__list">
        <li class="nav-primary__item <?= 'home' === $page ? 'nav-primary__item--active' : '' ?>">
          <a href="/" class="nav-primary__link">Home</a>
        </li>
        <li class="nav-primary__item <?= 'blog' === $page ? 'nav-primary__item--active' : '' ?>">
          <a href="/blog" class="nav-primary__link">Blog</a>
        </li>
        <li class="nav-primary__item <?= 'portfolio' === $page ? 'nav-primary__item--active' : '' ?>">
          <a href="/portfolio" class="nav-primary__link">Portfolio</a>
        </li>
        <li class="nav-primary__item <?= 'docs' === $page ? 'nav-primary__item--active' : '' ?>">
          <a href="/docs" class="nav-primary__link">Docs</a>
        </li>
        <?php if ($isGuest): ?>
        <li class="nav-primary__item">
          <a href="/login" class="nav-primary__link">Login</a>
        </li>
        <?php else: ?>
        <li class="nav-primary__item">
          <a href="/admin" class="nav-primary__link">Admin</a>
        </li>
        <li class="nav-primary__item">
          <a href="/logout" class="nav-primary__link">Logout</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>
```

---

#### Task 2: Create Footer Partial
**Status:** ⏳ Pending

**File:** `src/Templates/partials/footer.php`

**Content:**
```php
<?php
/**
 * Footer Partial
 *
 * @var int $year Current year
 */
$year ??= date('Y');
?>

<footer class="site-footer">
  <div class="container">
    <div class="site-footer__content">
      <div class="site-footer__brand">
        <h3 class="site-footer__title">Nativa CMS</h3>
        <p class="site-footer__desc">Modern PHP CMS and Blog Platform</p>
      </div>
      
      <nav class="site-footer__nav">
        <a href="/">Home</a>
        <a href="/blog">Blog</a>
        <a href="/docs">Documentation</a>
        <a href="/contact">Contact</a>
      </nav>
      
      <div class="site-footer__legal">
        <p>&copy; <?= $year ?> Nativa CMS. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>
```

---

#### Task 3: Update Frontend Layout to Use Partials
**Status:** ⏳ Pending

**File:** `src/Templates/layouts/frontend.php`

**Changes:**
```php
<!DOCTYPE html>
<html lang="sk">
<head>
  <!-- head content -->
</head>
<body>
  <!-- Include Header Partial -->
  <?php include $this->getTemplatesPath() . '/partials/header.php'; ?>
  
  <!-- Main Content Area -->
  <main class="site-main">
    <?php echo $this->yieldContent(); ?>
  </main>
  
  <!-- Include Footer Partial -->
  <?php include $this->getTemplatesPath() . '/partials/footer.php'; ?>
  
  <!-- Scripts -->
  <script src="<?php echo $appJs; ?>" defer crossorigin="anonymous"></script>
  <?php if (!empty($pageSpecificJs[$page] ?? '')) { ?>
  <script src="<?php echo $pageSpecificJs[$page]; ?>" defer crossorigin="anonymous"></script>
  <?php } ?>
</body>
</html>
```

---

### Phase 2: Fix Template Flow (Tasks 4-7)

#### Task 4: Fix Homepage Template
**Status:** ⏳ Pending

**Current Issue:** `pages/frontend/home.php` is a complete page, should be content-only

**Action:** 
- Option A: Move content into `yieldContent()` wrapper
- Option B: Keep as full page, remove layout wrapper in HomeAction

**Recommended:** Option B - Homepage is special, can be full page

**Update HomeAction:**
```php
$content = $this->renderer->render(
    'pages/frontend/home',
    [
        'articles'  => $articles,
        'pageTitle' => 'Nativa CMS - Modern PHP Blog Platform',
        'page'      => 'home',
    ],
    null  // No layout - homepage is complete
);
```

---

#### Task 5: Standardize All Page Templates
**Status:** ⏳ Pending

**Pattern for content pages:**
```php
<?php
/**
 * Page Template
 *
 * @var array  $articles
 * @var string $pageTitle
 * @var string $page
 */
?>

<!-- Page content here, wrapped by layout -->
<section class="page-content">
    <h1><?php echo $this->e($pageTitle); ?></h1>
    <!-- content -->
</section>
```

**Files to update:**
- `pages/frontend/blog.php` - ensure uses yieldContent pattern
- `pages/frontend/contact.php` - ensure uses yieldContent pattern
- `pages/frontend/portfolio.php` - ensure uses yieldContent pattern
- `pages/frontend/docs.php` - ensure uses yieldContent pattern
- `pages/frontend/about.php` - ensure uses yieldContent pattern
- `pages/frontend/services.php` - ensure uses yieldContent pattern
- `pages/frontend/pricing.php` - ensure uses yieldContent pattern

---

#### Task 6: Fix Article Templates
**Status:** ⏳ Pending

**Files:**
- `pages/articles/index.php` - articles listing
- `pages/articles/show.php` - article detail

**Update controllers:**
- `ShowArticleAction` - ensure correct template path
- `ListArticlesAction` - ensure correct template path

---

#### Task 7: Fix Blog Sub-page Templates
**Status:** ⏳ Pending

**Files:**
- `pages/frontend/blog/show.php` - blog article detail
- `pages/frontend/blog/search.php` - blog search results

**Ensure consistent structure with other pages.**

---

### Phase 3: Update Controllers (Tasks 8-9)

#### Task 8: Audit All Controller Template Paths
**Status:** ⏳ Pending

**Check all frontend controllers:**
```php
// Pattern 1: With layout
$this->renderer->render('pages/frontend/blog', $data, 'frontend');

// Pattern 2: Without layout (full page)
$this->renderer->render('pages/frontend/home', $data, null);

// Pattern 3: Sub-directory
$this->renderer->render('pages/articles/show', $data, 'frontend');
```

**Controllers to check:**
- HomeAction
- BlogAction
- ContactAction
- PortfolioAction
- DocsAction
- DisplayPageAction
- DisplayFormAction
- Article/*Actions
- Auth/*Actions

---

#### Task 9: Add Template Path Constants (Optional Enhancement)
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/View/TemplatePaths.php`

```php
final class TemplatePaths
{
    // Frontend pages
    public const HOME = 'pages/frontend/home';
    public const BLOG = 'pages/frontend/blog';
    public const CONTACT = 'pages/frontend/contact';
    public const PORTFOLIO = 'pages/frontend/portfolio';
    public const DOCS = 'pages/frontend/docs';
    
    // Article pages
    public const ARTICLES_INDEX = 'pages/articles/index';
    public const ARTICLES_SHOW = 'pages/articles/show';
    
    // Auth pages
    public const LOGIN = 'pages/login';
    public const REGISTER = 'pages/register';
    
    // Layouts
    public const LAYOUT_FRONTEND = 'frontend';
    public const LAYOUT_ADMIN = 'admin';
    public const LAYOUT_NONE = null;
}
```

**Usage:**
```php
$content = $this->renderer->render(
    TemplatePaths::BLOG,
    $data,
    TemplatePaths::LAYOUT_FRONTEND
);
```

---

### Phase 4: Testing & Validation (Tasks 10-11)

#### Task 10: Test All Page Routes
**Status:** ⏳ Pending

**Routes to test:**
- `/` - Homepage
- `/blog` - Blog listing
- `/blog/{slug}` - Article detail
- `/contact` - Contact form
- `/portfolio` - Portfolio
- `/docs` - Documentation
- `/about` - About page
- `/services` - Services
- `/pricing` - Pricing
- `/login` - Login page
- `/register` - Register page
- `/articles` - Articles listing
- `/articles/{slug}` - Article detail
- `/form/{slug}` - Dynamic form

**Check:**
- Page renders without errors
- Header partial appears
- Footer partial appears
- Content is correct
- Assets (CSS/JS) load properly

---

#### Task 11: Update Documentation
**Status:** ⏳ Pending

**File:** `docs/templates.md`

**Document:**
- Template directory structure
- Layout flow (Action → Layout → Page → Partials)
- How to create new pages
- How to use partials
- Template path conventions
- AssetHelper usage

---

## Commit Plan

### Commit 1: Create Partials
**Tasks:** 1, 2, 3  
**Message:** `feat: create header and footer partials for reusable layout components`

### Commit 2: Fix Template Flow
**Tasks:** 4, 5, 6, 7  
**Message:** `refactor: standardize page templates to use layout + content pattern`

### Commit 3: Update Controllers
**Tasks:** 8, 9  
**Message:** `refactor: update controller template paths for consistency`

### Commit 4: Testing & Docs
**Tasks:** 10, 11  
**Message:** `docs: document template system and add testing notes`

---

## Migration Guide

### For Developers

**Old Pattern:**
```php
// Full page with everything
include 'header.php';
// page content
include 'footer.php';
```

**New Pattern:**
```php
// Content only - layout handles header/footer automatically
<?= $this->yieldContent() ?>
```

### Template Path Rules

1. **Frontend pages:** `pages/frontend/{name}` + layout `'frontend'`
2. **Auth pages:** `pages/{name}` + layout `'frontend'`
3. **Sub-pages:** `pages/{category}/{name}` + layout `'frontend'`
4. **Full pages:** Any path + layout `null` (no wrapper)
5. **Partials:** `partials/{name}` (included manually)

---

## Success Criteria

- ✅ All pages render without 500 errors
- ✅ Header appears on all pages
- ✅ Footer appears on all pages
- ✅ Consistent template structure
- ✅ Partials are reusable
- ✅ Asset loading works correctly
- ✅ All 202 tests still pass
- ✅ Documentation updated

---

**Estimated Effort:** 4-6 hours  
**Risk Level:** Medium - Breaking changes to template flow  
**Rollback Plan:** Keep backup of old templates, revert commit if needed
