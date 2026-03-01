# Implementation Plan: New Frontend Template Integration

**Branch:** `feature/new-frontend-integration`  
**Created:** 2026-02-28  
**Mode:** Fast Plan

## Settings

- **Testing:** No
- **Logging:** Verbose (DEBUG level throughout)
- **Documentation:** Skip docs update

## Overview

Integrate the new Vite+TypeScript `front` templates with the PHP CMS backend. This plan focuses on **homepage and blog** functionality first, with a path to integrate other pages later.

The new frontend in `src/interfaces/Templates/front/` provides:
- Modern TypeScript/Vite build system
- Pre-built page templates (home, blog, contact, services, pricing, portfolio, docs)
- Alpine.js and HTMX for interactivity
- AssetHelper for production builds with hashed filenames

## Tasks

### Phase 1: Build System & Asset Pipeline

#### Task 1.1: Build Frontend Assets ✅
**File:** `src/interfaces/Templates/front/`

Run the Vite build to generate production assets:
```bash
cd src/interfaces/Templates/front
pnpm install
pnpm run build:prod
```

**Expected output in `public/assets/`:**
- `manifest.json` - Asset version mapping
- `init.[hash].js` - Theme initialization
- `css.[hash].css` - Shared base styles
- `app.[hash].js` - Main application JS
- `home.[hash].js` - Homepage-specific bundle
- `blog.[hash].js` - Blog bundle
- Fonts, images, compressed files (.gz, .br)

**Logging:**
- Log build start/completion
- Log any build warnings or errors
- Log manifest.json contents for debugging

**Status:** ✅ COMPLETED - Build successful, assets in `public/assets/`

---

#### Task 1.2: Verify AssetHelper Integration ✅
**Files:** 
- `src/infrastructure/View/AssetHelper.php` (created)
- `src/interfaces/Templates/front/layouts/home.php` (already uses AssetHelper)

**Actions:**
1. Check if `AssetHelper` class exists and reads `manifest.json`
2. If missing, create `AssetHelper` in `src/infrastructure/View/`
3. Ensure it reads `public/assets/manifest.json` to get hashed filenames
4. Test that `AssetHelper::js()` and `AssetHelper::css()` return correct paths

**Expected behavior:**
```php
AssetHelper::js('app.js')  // Returns: /assets/app.a1b2c3d4.js (from manifest)
AssetHelper::css('css.css') // Returns: /assets/css.e5f6g7h8.css (from manifest)
```

**Logging:**
- DEBUG: Log manifest.json load path and contents
- DEBUG: Log asset resolution (input → output path)
- WARN: Log if manifest.json is missing or unreadable

**Status:** ✅ COMPLETED - AssetHelper created and tested successfully

---

### Phase 2: Template Renderer Updates

#### Task 2.1: Update TemplateRenderer for New Frontend
**File:** `src/interfaces/HTTP/View/TemplateRenderer.php`

**Current behavior:**
- Renders from `frontend/` and `admin/` subdirectories
- Uses file-based caching

**Required changes:**
1. Add support for new `front/` template directory
2. Update `renderTemplate()` to handle new path structure
3. Ensure layout rendering works with new `front/layouts/`
4. Add config option to switch between old/new frontend

**Implementation:**
```php
// Add to renderTemplate() method
if (str_starts_with($template, 'front/')) {
    $templatePath = $this->templatesPath . '/front';
    $template = substr($template, 6); // Remove 'front/' prefix
}
```

**Logging:**
- DEBUG: Log template path resolution
- DEBUG: Log which template directory is being used (frontend vs front)
- WARN: Log if template file not found

---

#### Task 2.2: Create New Frontend Layout
**File:** `src/interfaces/Templates/front/layouts/cms.php`

Create a CMS-specific layout that extends the existing `home.php` layout but adds:
1. Dynamic page title from CMS data
2. Meta description from CMS settings
3. CSRF token injection for forms
4. User authentication state (already present, verify integration)
5. Alpine.js and HTMX script includes (uncomment in layout)

**Key additions:**
```php
// In <head>
<title><?= $this->e($pageTitle ?? 'App') ?></title>
<meta name="description" content="<?= $this->e($metaDescription ?? '') ?>">

<!-- Enable HTMX -->
<script src="https://unpkg.com/htmx.org@1.9.12"></script>

<!-- Enable Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

**Logging:**
- DEBUG: Log layout rendering
- DEBUG: Log meta tag values

---

### Phase 3: Controller Integration

#### Task 3.1: Update HomeAction for New Template
**File:** `src/interfaces/HTTP/Actions/Frontend/HomeAction.php`

**Current:** Renders `frontend/pages/home` with articles  
**New:** Render `front/pages/home` with new template structure

**Changes:**
```php
// Old
$content = $this->renderer->render(
    'pages/home',
    ['articles' => $articles, 'title' => 'Welcome'],
    'layouts/base'
);

// New
$content = $this->renderer->render(
    'front/pages/home',
    [
        'articles' => $articles,
        'title' => 'Welcome',
        'page' => 'home',
        'metaDescription' => 'Latest articles and news',
    ],
    'front/layouts/cms'
);
```

**Logging:**
- DEBUG: Log articles count being passed to template
- DEBUG: Log template render start/end
- INFO: Log homepage render time

---

#### Task 3.2: Create Blog Listing Action
**File:** `src/interfaces/HTTP/Actions/Frontend/BlogAction.php` (NEW)

Create new action for blog listing using new template:

```php
<?php
declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\ArticleManager;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BlogAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        
        $articles = $this->articleManager->listLatest($limit, ($page - 1) * $limit);
        $total = $this->articleManager->countPublished();
        
        $content = $this->renderer->render(
            'front/pages/blog',
            [
                'articles' => $articles,
                'currentPage' => $page,
                'totalPages' => (int) ceil($total / $limit),
                'page' => 'blog',
                'pageTitle' => 'Blog',
                'metaDescription' => 'Latest articles and insights',
            ],
            'front/layouts/cms'
        );
        
        return $this->html($content);
    }

    public static function create(): self
    {
        $container = \Infrastructure\Container\ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
```

**Logging:**
- DEBUG: Log pagination params (page, limit)
- DEBUG: Log articles count returned
- INFO: Log blog page render time

---

#### Task 3.3: Create Blog Article Show Action
**File:** `src/interfaces/HTTP/Actions/Frontend/ShowBlogArticleAction.php` (NEW)

Create action for displaying single blog article:

```php
<?php
declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\ArticleManager;
use Domain\Model\Article;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowBlogArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $slug = $request->attributes->get('slug');
        
        try {
            $article = $this->articleManager->findBySlug($slug);
            
            if (!$article || !$article->isPublished()) {
                return $this->notFound();
            }
            
            // Increment view count
            $this->articleManager->incrementViews($article->getId());
            
            // Get related articles
            $relatedArticles = $this->articleManager->findRelated($article, 3);
            
            $content = $this->renderer->render(
                'front/pages/blog/article',
                [
                    'article' => $article,
                    'relatedArticles' => $relatedArticles,
                    'page' => 'blog',
                    'pageTitle' => $article->getTitle(),
                    'metaDescription' => $article->getExcerpt(),
                ],
                'front/layouts/cms'
            );
            
            return $this->html($content);
        } catch (\Throwable $e) {
            error_log("DEBUG: ShowBlogArticleAction error: " . $e->getMessage());
            return $this->error(500);
        }
    }

    public static function create(): self
    {
        $container = \Infrastructure\Container\ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
```

**Logging:**
- DEBUG: Log article slug being looked up
- DEBUG: Log article found/not found
- INFO: Log view count increment
- ERROR: Log exceptions

---

#### Task 3.4: Update ContactAction for New Template
**File:** `src/interfaces/HTTP/Actions/Frontend/ContactAction.php`

Update existing ContactAction to use new template:

**Changes:**
1. Update template path to `front/pages/contact`
2. Add HTMX support for form submission
3. Add success/error handling with Alpine.js data attributes
4. Update layout to `front/layouts/cms`

**HTMX integration:**
```php
// In template, form should have:
<form hx-post="/contact" hx-target="#contact-form" hx-swap="outerHTML">
```

**Logging:**
- DEBUG: Log form submission data (excluding sensitive info)
- DEBUG: Log validation errors
- INFO: Log successful contact form submissions
- WARN: Log validation failures

---

### Phase 4: Routing

#### Task 4.1: Add New Routes
**File:** `src/interfaces/HTTP/Kernel.php`

Add routes for new blog functionality:

```php
// In registerRoutes() method, add to Frontend Routes section:

// Blog (new)
$this->router->get('/blog', BlogAction::class);
$this->router->get('/blog/{slug}', ShowBlogArticleAction::class);

// Keep existing article routes for backward compatibility
// $this->router->get('/articles', ListArticlesAction::class);
// $this->router->get('/articles/{slug}', ShowArticleAction::class);
```

**Route order matters:**
- More specific routes first (`/blog/{slug}`)
- Catch-all routes last (`/{slug}`)

**Logging:**
- DEBUG: Log route matching (which route matched)
- WARN: Log 404 for unmatched routes

---

### Phase 5: Data Integration

#### Task 5.1: Map CMS Data to New Templates
**Files:** 
- `src/interfaces/Templates/front/pages/home.php`
- `src/interfaces/Templates/front/pages/blog.php`
- `src/interfaces/Templates/front/pages/blog/article.php`

**Update templates to use CMS data:**

**home.php:**
```php
<?php
/**
 * @var array $articles
 * @var string $pageTitle
 */
?>

<!-- Replace static service cards with dynamic content if needed -->
<?php if (!empty($articles)): ?>
<section class="articles-preview">
    <div class="container">
        <h2>Latest Articles</h2>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
            <article class="article-card">
                <h3><?= $this->e($article->getTitle()) ?></h3>
                <p><?= $this->e($article->getExcerpt()) ?></p>
                <a href="/blog/<?= $this->e($article->getSlug()) ?>">Read more →</a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
```

**blog.php:**
```php
<?php
/**
 * @var array $articles
 * @var int $currentPage
 * @var int $totalPages
 */
?>

<div class="blog-grid">
    <?php foreach ($articles as $article): ?>
    <article class="blog-card">
        <h2><?= $this->e($article->getTitle()) ?></h2>
        <p class="meta">
            By <?= $this->e($article->getAuthorName()) ?> | 
            <?= $this->date($article->getPublishedAt()) ?>
        </p>
        <p><?= $this->e($article->getExcerpt()) ?></p>
        <a href="/blog/<?= $this->e($article->getSlug()) ?>">Read more</a>
    </article>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<nav class="pagination">
    <?php if ($currentPage > 1): ?>
    <a href="/blog?page=<?= $currentPage - 1 ?>">Previous</a>
    <?php endif; ?>
    
    <span>Page <?= $currentPage ?> of <?= $totalPages ?></span>
    
    <?php if ($currentPage < $totalPages): ?>
    <a href="/blog?page=<?= $currentPage + 1 ?>">Next</a>
    <?php endif; ?>
</nav>
<?php endif; ?>
```

**Logging:**
- DEBUG: Log template variables being used
- WARN: Log if expected variables are missing

---

#### Task 5.2: Add Search Functionality to Blog
**File:** `src/interfaces/Templates/front/pages/blog.php`

Add search form with HTMX:

```php
<!-- Search form -->
<form class="blog-search" hx-get="/blog/search" hx-target="#blog-results" hx-trigger="keyup changed delay:300ms">
    <input type="search" name="q" placeholder="Search articles..." 
           value="<?= $this->e($searchQuery ?? '') ?>">
</form>

<div id="blog-results">
    <!-- Search results loaded via HTMX -->
</div>
```

**Create SearchAction:**
**File:** `src/interfaces/HTTP/Actions/Frontend/SearchBlogAction.php`

```php
<?php
final class SearchBlogAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 3) {
            return $this->html('<p>Please enter at least 3 characters</p>');
        }
        
        $articles = $this->articleManager->search($query, 10);
        
        $content = $this->renderer->partial('front/partials/blog-search-results', [
            'articles' => $articles,
            'query' => $query,
        ]);
        
        return $this->html($content);
    }

    public static function create(): self
    {
        $container = \Infrastructure\Container\ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
```

**Logging:**
- DEBUG: Log search query
- DEBUG: Log search results count
- WARN: Log short search queries (< 3 chars)

---

### Phase 6: Testing & Validation

#### Task 6.1: Test Homepage Integration
**Checklist:**
- [ ] Homepage loads without errors
- [ ] Latest articles display correctly
- [ ] Links to blog articles work
- [ ] Assets (CSS, JS) load correctly (check browser dev tools)
- [ ] Theme toggle works (dark/light mode)
- [ ] Mobile menu works
- [ ] No console errors in browser

**Test URLs:**
- `http://localhost:8000/`

**Logging verification:**
- Check logs for successful template renders
- Check for any asset loading errors

---

#### Task 6.2: Test Blog Integration
**Checklist:**
- [ ] Blog listing loads
- [ ] Pagination works
- [ ] Article detail pages load
- [ ] Related articles display
- [ ] View count increments
- [ ] Search works (if implemented)
- [ ] HTMX search updates results without page reload

**Test URLs:**
- `http://localhost:8000/blog`
- `http://localhost:8000/blog/{article-slug}`
- `http://localhost:8000/blog?q=search-term`

**Logging verification:**
- Check logs for article views being tracked
- Check for any 404 errors

---

#### Task 6.3: Test Contact Form
**Checklist:**
- [ ] Contact form loads
- [ ] HTMX form submission works
- [ ] Success message displays
- [ ] Error handling works (validation errors)
- [ ] Email notification sent (if configured)
- [ ] Form submission saved to database

**Test URL:**
- `http://localhost:8000/contact`

**Logging verification:**
- Check logs for form submissions
- Check for validation errors

---

#### Task 6.4: Cross-Browser Testing
**Browsers to test:**
- Chrome/Chromium
- Firefox
- Safari (if available)
- Mobile browsers (Chrome Mobile, Safari iOS)

**Check:**
- Layout renders correctly
- JavaScript functionality works
- CSS styles apply correctly
- Forms submit properly

---

### Phase 7: Cleanup & Optimization

#### Task 7.1: Remove or Archive Old Templates
**Files:**
- `src/interfaces/Templates/frontend/pages/home.php` (old)
- `src/interfaces/Templates/frontend/pages/articles/*.php` (old)

**Options:**
1. **Delete** - If confident new templates work
2. **Archive** - Move to `src/interfaces/Templates/frontend-old/`
3. **Keep both** - Maintain backward compatibility during transition

**Recommendation:** Archive during transition, delete after validation

**Logging:**
- INFO: Log which templates are being used (old vs new)

---

#### Task 7.2: Optimize Asset Loading
**File:** `src/interfaces/Templates/front/layouts/cms.php`

**Optimizations:**
1. Add preload hints for critical assets
2. Defer non-critical JavaScript
3. Add `fetchpriority="high"` for hero images
4. Implement lazy loading for below-fold images
5. Add font-display: swap for web fonts

**Example:**
```php
<!-- Preload critical CSS -->
<link rel="preload" href="<?= $cssBundle ?>" as="style">

<!-- Lazy load images -->
<img src="<?= $heroImage ?>" loading="lazy" decoding="async">
```

**Logging:**
- DEBUG: Log asset load times
- WARN: Log slow-loading assets

---

#### Task 7.3: Performance Audit
**Tools:**
- Chrome DevTools Lighthouse
- WebPageTest.org
- GTmetrix

**Metrics to check:**
- First Contentful Paint (FCP) < 1.5s
- Largest Contentful Paint (LCP) < 2.5s
- Time to Interactive (TTI) < 3.8s
- Cumulative Layout Shift (CLS) < 0.1

**Actions based on results:**
- Optimize images if LCP is slow
- Reduce JavaScript if TTI is slow
- Add size attributes if CLS is high

---

## Commit Plan

Since we have more than 5 tasks, here's the commit strategy:

### Commit 1: Build System & Assets
**Tasks:** 1.1, 1.2  
**Message:** `feat(frontend): add Vite build system and AssetHelper integration`

### Commit 2: Template Renderer Updates
**Tasks:** 2.1, 2.2  
**Message:** `feat(templates): update TemplateRenderer for new frontend and create CMS layout`

### Commit 3: Controller Integration
**Tasks:** 3.1, 3.2, 3.3, 3.4  
**Message:** `feat(controllers): integrate homepage, blog, and contact actions with new templates`

### Commit 4: Routing & Data Integration
**Tasks:** 4.1, 5.1, 5.2  
**Message:** `feat(routes): add blog routes and map CMS data to templates`

### Commit 5: Testing & Optimization
**Tasks:** 6.1, 6.2, 6.3, 6.4, 7.1, 7.2, 7.3  
**Message:** `test(frontend): validate integration and optimize performance`

---

## Dependencies & Risks

### Dependencies
- Node.js/Pnpm must be installed for build
- ArticleManager must have methods: `listLatest()`, `findBySlug()`, `search()`
- Database must have articles for testing

### Risks
1. **AssetHelper missing** - May need to create from scratch
2. **Template path conflicts** - Old and new templates may conflict
3. **HTMX/Alpine.js not loading** - Verify script includes in layout
4. **Build output path mismatch** - Ensure Vite outputs to correct `public/assets/`

### Mitigation
- Test each phase before proceeding
- Keep old templates as fallback during transition
- Add comprehensive logging for debugging
- Document any deviations from plan

---

## Next Steps

After this plan is complete:
1. Review integration and gather feedback
2. Plan integration for remaining pages (services, pricing, portfolio, docs)
3. Add admin panel integration with new frontend
4. Implement advanced features (comments, newsletter, social sharing)

---

**Plan created:** 2026-02-28  
**Estimated effort:** 5-7 focused sessions  
**Priority:** High (blocks other frontend enhancements)
