# Frontend Modernization Plan

**Branch:** `feature/frontend-vite-integration`  
**Created:** 2026-03-06  
**Status:** Planning Complete

---

## Settings

- **Testing:** ✅ Yes, include tests for template rendering
- **Logging:** 🔍 Verbose - detailed DEBUG logs for development
- **Documentation:** ✅ Yes, update docs after implementation
- **Priority:** High - Core feature for frontend modernization

---

## Overview

Integrate the existing Vite+TypeScript frontend build system with PHP backend template rendering. The Vite setup already exists in `src/Templates/` with proper build output to `public/assets/`. This plan focuses on ensuring seamless integration between PHP controllers and the modern frontend assets.

### Current State
- ✅ Vite 7.3.1 + TypeScript 5.9.3 configured
- ✅ Build output to `public/assets/` with manifest.json
- ✅ AssetHelper class resolves hashed filenames
- ✅ Page-specific code splitting already implemented
- ⚠️ Mixed template naming conventions need cleanup
- ⚠️ Some controllers use inconsistent layout paths

### Target State
- ✅ All controllers use consistent template paths
- ✅ AssetHelper properly loads Vite-built assets
- ✅ All page templates tested with real data
- ✅ Clean, documented template structure

---

## Tasks

### Phase 1: Audit & Cleanup (Tasks 1-3)

#### Task 1: Audit Template Paths and Controller Usage
**Status:** ⏳ Pending  
**Priority:** High

**Description:**
Review all frontend controllers and their template rendering calls to identify inconsistencies.

**Files to examine:**
- `src/Interfaces/HTTP/Actions/Frontend/*.php` - All frontend controllers
- `src/Interfaces/HTTP/View/TemplateRenderer.php` - Template path resolution logic
- `src/Templates/` directory structure

**Deliverables:**
- Document all template path patterns used
- Identify controllers with inconsistent paths
- List legacy template files that can be removed

**Logging:**
- Log template path resolution in TemplateRenderer for debugging

---

#### Task 2: Standardize Template Naming Conventions
**Status:** ⏳ Pending  
**Priority:** High  
**Blocked By:** Task 1

**Description:**
Establish and apply consistent template naming across the project.

**Actions:**
- Move all templates to `src/Templates/pages/frontend/` structure
- Update controller references to use standardized paths
- Remove duplicate/legacy template files

**Files to modify:**
- Controllers in `src/Interfaces/HTTP/Actions/Frontend/`
- Template files in `src/Templates/`

**Deliverables:**
- Consistent template directory structure
- Updated controller template paths
- Cleanup of unused template files

---

#### Task 3: Verify Vite Build Integration
**Status:** ⏳ Pending  
**Priority:** High

**Description:**
Ensure Vite build process produces all required assets and manifest.json is correctly structured.

**Actions:**
- Run `pnpm build` in `src/Templates/`
- Verify `public/assets/manifest.json` structure
- Test AssetHelper::js() and AssetHelper::css() with built assets
- Check all page-specific bundles are generated

**Files to verify:**
- `src/Templates/vite.config.ts` - Entry points configuration
- `public/assets/manifest.json` - Asset mapping
- `src/Infrastructure/View/AssetHelper.php` - Asset resolution

**Deliverables:**
- Working Vite build producing all required assets
- Verified manifest.json for all page bundles
- Test confirming AssetHelper resolves assets correctly

---

### Phase 2: Controller Updates (Tasks 4-7)

#### Task 4: Update HomeAction Controller
**Status:** ⏳ Pending  
**Priority:** High  
**Blocked By:** Task 2

**Description:**
Update HomeAction to use standardized template paths and verify asset loading.

**Files to modify:**
- `src/Interfaces/HTTP/Actions/Frontend/HomeAction.php`

**Changes:**
```php
// Ensure consistent template path
$content = $this->renderer->render(
    'pages/frontend/home',  // Standardized path
    [
        'articles'  => $articles,
        'pageTitle' => 'Nativa CMS',
        'page'      => 'home',
    ],
    'frontend'  // Layout
);
```

**Testing:**
- Verify homepage renders correctly
- Check all Vite assets load (home.js, home.css)
- Test with real article data from database

**Logging:**
- DEBUG: Log template path resolution
- INFO: Log number of articles loaded

---

#### Task 5: Update BlogAction and Article Controllers
**Status:** ⏳ Pending  
**Priority:** High  
**Blocked By:** Task 2

**Description:**
Update blog and article-related controllers for consistent template rendering.

**Files to modify:**
- `src/Interfaces/HTTP/Actions/Frontend/BlogAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/Article/ShowBlogArticleAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/Article/ListArticlesAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/Article/SearchArticlesAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/Article/ByTagAction.php`

**Changes:**
- Standardize all template paths to `pages/frontend/blog/*`
- Ensure consistent layout usage
- Verify page-specific assets load correctly

**Testing:**
- Blog listing page renders with articles
- Article detail page loads correctly
- Search functionality works with HTMX
- Tag filtering displays articles

---

#### Task 6: Update ContactAction and Form Controllers
**Status:** ⏳ Pending  
**Priority:** High  
**Blocked By:** Task 2

**Description:**
Update contact and dynamic form controllers.

**Files to modify:**
- `src/Interfaces/HTTP/Actions/Frontend/ContactAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/DisplayFormAction.php`

**Changes:**
- Standardize template paths
- Verify HTMX form submission works with new assets
- Test form validation error display

**Testing:**
- Contact form renders correctly
- HTMX submission works
- Validation errors display properly
- Success message shows after submission

**Logging:**
- DEBUG: Log form submission data
- INFO: Log contact submission count
- WARN: Log validation failures

---

#### Task 7: Update PortfolioAction and DocsAction
**Status:** ⏳ Pending  
**Priority:** Medium  
**Blocked By:** Task 2

**Description:**
Update remaining frontend controllers.

**Files to modify:**
- `src/Interfaces/HTTP/Actions/Frontend/PortfolioAction.php`
- `src/Interfaces/HTTP/Actions/Frontend/DocsAction.php`

**Testing:**
- Portfolio page displays correctly
- Documentation page renders
- All page-specific assets load

---

### Phase 3: Asset Loading Verification (Tasks 8-9)

#### Task 8: Verify Critical CSS and Font Loading
**Status:** ⏳ Pending  
**Priority:** Medium

**Description:**
Ensure critical CSS inlining and font loading works correctly.

**Files to verify:**
- `src/Templates/layouts/frontend.php` - Asset inclusion
- `src/Infrastructure/View/AssetHelper.php` - Asset resolution
- `public/assets/` - Built CSS files

**Actions:**
- Verify critical CSS is inlined
- Check non-critical CSS loads asynchronously
- Confirm only essential fonts are preloaded
- Test page load performance

**Testing:**
- Measure initial page load time
- Verify CSS loading strategy in browser DevTools
- Check font loading network requests

---

#### Task 9: Test Alpine.js and HTMX Integration
**Status:** ⏳ Pending  
**Priority:** High

**Description:**
Verify JavaScript interactivity features work correctly.

**Features to test:**
- Alpine.js components (theme toggle, mobile nav)
- HTMX form submissions (contact form, search)
- Scroll animations and parallax effects
- Cookie consent component
- Gallery lightbox (if used)

**Files to verify:**
- `src/Templates/src/frontend/pages/*.ts` - Page-specific TypeScript
- `src/Templates/src/components/` - Reusable components
- `src/Templates/src/ui/alpine.ts` - Alpine.js setup
- `src/Templates/src/vendors/htmx.ts` - HTMX integration

**Testing:**
- All interactive features work in browser
- No JavaScript errors in console
- HTMX requests complete successfully
- Alpine.js reactivity works

---

### Phase 4: Testing & Documentation (Tasks 10-11)

#### Task 10: Write Integration Tests
**Status:** ⏳ Pending  
**Priority:** Medium  
**Blocked By:** Tasks 4-7

**Description:**
Create PHPUnit tests for frontend controllers to verify template rendering.

**Files to create:**
- `tests/Interfaces/HTTP/Actions/Frontend/HomeActionTest.php` (update existing)
- `tests/Interfaces/HTTP/Actions/Frontend/BlogActionTest.php` (new)
- `tests/Interfaces/HTTP/Actions/Frontend/PortfolioActionTest.php` (new)
- `tests/Interfaces/HTTP/Actions/Frontend/DocsActionTest.php` (new)

**Test coverage:**
- Controllers return 200 status
- Correct templates are rendered
- Required data is passed to templates
- Assets are properly included

**Example test structure:**
```php
public function testHandleRendersHomepageWithArticles(): void
{
    $articleManager = $this->createMock(ArticleManager::class);
    $articleManager->method('listLatest')
        ->willReturn([/* mock articles */]);
    
    $renderer = $this->createMock(TemplateRenderer::class);
    $renderer->expects($this->once())
        ->method('render')
        ->with('pages/frontend/home', $this->anything(), 'frontend')
        ->willReturn('<html>Home</html>');
    
    $action = new HomeAction($articleManager, $renderer);
    $response = $action->handle(new Request());
    
    self::assertEquals(200, $response->getStatusCode());
}
```

---

#### Task 11: Update Documentation
**Status:** ⏳ Pending  
**Priority:** Low  
**Blocked By:** All previous tasks

**Description:**
Document the frontend integration for future developers.

**Files to update/create:**
- `docs/frontend-architecture.md` - Frontend build system overview
- `docs/templates.md` - Template structure and conventions
- `docs/assets.md` - Asset management with Vite
- `README.md` - Update build instructions

**Documentation content:**
- Vite build commands (`pnpm build`, `pnpm dev`)
- Template directory structure
- AssetHelper usage examples
- Adding new page-specific assets
- HTMX and Alpine.js integration patterns

---

## Commit Plan

### Commit 1: Template Audit and Cleanup
**Tasks:** 1, 2, 3  
**Message:** `refactor: standardize template paths and clean up legacy files`

### Commit 2: Controller Updates
**Tasks:** 4, 5, 6, 7  
**Message:** `refactor: update frontend controllers with consistent template rendering`

### Commit 3: Asset Loading & Integration
**Tasks:** 8, 9  
**Message:** `feat: verify Vite asset loading and HTMX/Alpine.js integration`

### Commit 4: Tests & Documentation
**Tasks:** 10, 11  
**Message:** `test: add frontend controller integration tests and update docs`

---

## Next Steps

To start implementation, run:
```bash
/skills aif-implement
```

To view tasks:
```bash
/task-manager list
```

---

**Estimated Effort:** 8-12 hours  
**Risk Level:** Low - Vite setup already exists, mostly integration work  
**Dependencies:** Node.js/pnpm installed, existing Vite configuration
