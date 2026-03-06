# Actions Refactor Plan

**Branch:** `refactor/actions-clean-minimal`  
**Created:** 2026-03-06  
**Priority:** HIGH - Code quality and maintainability

---

## Current State Analysis

### Total Actions: 44 files

**By Category:**
- Frontend Actions: 11 files
- Admin Actions: 21 files
- Auth Actions: 3 files
- Base Actions: 2 files (Action.php, ActionInterface.php)
- Admin Sub-categories:
  - Article: 6 files
  - Roles: 3 files
  - Permissions: 3 files
  - Settings: 2 files
  - Media: 2 files
  - Standalone: 5 files

### Identified Issues

1. **Inconsistent Error Handling:**
   - Some actions use try-catch (PortfolioAction)
   - Some don't (DocsAction)
   - Mixed error response formats

2. **Redundant Code:**
   - Repeated `create()` methods in every action
   - Repeated dependency injection patterns
   - Duplicate template rendering logic

3. **Inconsistent Naming:**
   - Template paths: `'frontend/contact'` vs `'pages/frontend/contact'`
   - Variable names: `$title` vs `$pageTitle`
   - Layout usage: some with layout, some without

4. **Too Many Responsibilities:**
   - Actions doing validation, business logic, AND rendering
   - ContactAction has 147 lines (should be ~50)
   - DisplayFormAction has 160 lines

5. **Missing Type Safety:**
   - Mixed type hints in request params
   - Inconsistent null handling
   - Missing return types in some methods

---

## Target Architecture

### Principles

1. **Single Responsibility:** Actions only handle HTTP concerns
2. **Minimal Code:** Max 50 lines per action, max 3 methods
3. **Consistent Patterns:** Same structure across all actions
4. **Type Safe:** Strict types, proper type hints
5. **No Business Logic:** Delegate to services/DTOs

### Standard Action Pattern

```php
<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\[Category];

use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * [Description] Action.
 */
final class [Name]Action extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
        // Only required services
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        // GET requests
        if ($request->isGet()) {
            return $this->show($request);
        }

        // POST requests
        if ($request->isPost()) {
            return $this->submit($request);
        }

        return $this->error('Method not allowed', 405);
    }

    private function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'pages/[category]/[template]',
            [
                'pageTitle' => 'Page Title',
                'page'      => '[identifier]',
            ],
            'frontend'
        );

        return $this->html($content);
    }

    public static function create(): self
    {
        $container = \Infrastructure\Container\ContainerFactory::create();

        return new self(
            $container->get(TemplateRenderer::class),
            // Inject required services
        );
    }
}
```

### Helper Methods in Base Action

Add to `Action.php`:

```php
// Request method helpers
protected function isGet(Request $request): bool
{
    return 'GET' === $request->getMethod();
}

protected function isPost(Request $request): bool
{
    return 'POST' === $request->getMethod();
}

// Template rendering helper
protected function renderPage(
    string $template,
    array $data = [],
    ?string $layout = 'frontend'
): Response {
    $content = $this->renderer->render($template, $data, $layout);
    return $this->html($content);
}
```

---

## Refactor Tasks

### Phase 1: Base Action Improvements (Tasks 1-2)

#### Task 1: Enhance Base Action Class
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Action.php`

**Changes:**
- Add request method helpers (`isGet()`, `isPost()`, etc.)
- Add template rendering helper (`renderPage()`)
- Add JSON response helper with consistent format
- Add flash message helpers
- Improve error handling helpers

**Deliverables:**
- Enhanced `Action.php` with 10+ helper methods
- All helpers properly typed
- PHPDoc comments for all methods

---

#### Task 2: Create Action Trait for Common Logic
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Concerns/HandlesRequests.php`

**Purpose:** Extract common request handling patterns

**Traits to create:**
- `HandlesRequests` - Request method checks
- `RendersTemplates` - Template rendering helpers
- `HandlesErrors` - Error response helpers
- `HasContainer` - Container access for `create()` method

---

### Phase 2: Frontend Actions Refactor (Tasks 3-8)

#### Task 3: Refactor Simple Frontend Actions
**Status:** ⏳ Pending

**Files:**
- `PortfolioAction.php` (48 lines → ~35 lines)
- `DocsAction.php` (47 lines → ~35 lines)
- `AboutAction.php` (if exists)
- `ServicesAction.php` (if exists)
- `PricingAction.php` (if exists)

**Pattern:**
```php
final class PortfolioAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        return $this->renderPage(
            'pages/frontend/portfolio',
            [
                'pageTitle' => 'Portfolio',
                'page'      => 'portfolio',
            ]
        );
    }

    public static function create(): self
    {
        return new self(
            \Infrastructure\Container\ContainerFactory::create()
                ->get(TemplateRenderer::class)
        );
    }
}
```

**Changes:**
- Remove try-catch (let errors bubble up)
- Use `renderPage()` helper
- Simplify `create()` method
- Consistent formatting

---

#### Task 4: Refactor HomeAction
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Frontend/HomeAction.php`

**Current:** 53 lines  
**Target:** ~40 lines

**Changes:**
- Simplify article fetching
- Use `renderPage()` helper
- Consistent error handling

---

#### Task 5: Refactor BlogAction
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Frontend/BlogAction.php`

**Current:** 68 lines  
**Target:** ~45 lines

**Changes:**
- Extract pagination logic to service
- Simplify template rendering
- Better error handling

---

#### Task 6: Refactor ContactAction
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Frontend/ContactAction.php`

**Current:** 147 lines  
**Target:** ~60 lines

**Changes:**
- Extract form submission logic to service
- Extract HTMX response logic to separate method
- Simplify validation error handling
- Use DTO for form data

**Split into:**
- `ContactAction::show()` - Display form
- `ContactAction::submit()` - Handle submission
- `ContactAction::handleHtmxSubmit()` - HTMX specific logic

---

#### Task 7: Refactor Article Actions
**Status:** ⏳ Pending

**Files:**
- `ShowBlogArticleAction.php` (68 lines → ~45)
- `ShowArticleAction.php` (53 lines → ~40)
- `ListArticlesAction.php` (48 lines → ~40)
- `ListArticlesApiAction.php` (72 lines → ~50)
- `SearchArticlesAction.php` (58 lines → ~45)
- `ByTagAction.php` (52 lines → ~40)

**Pattern:**
```php
final class ShowBlogArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        
        $article = $this->articleManager->findBySlug($slug);
        
        if (null === $article || $article->isDraft()) {
            return $this->notFound('Article not found');
        }

        $this->articleManager->incrementViewCount($article->id());
        $related = $this->articleManager->findRelated($article, 3);

        return $this->renderPage(
            'frontend/blog/show',
            [
                'article'         => $article,
                'relatedArticles' => $related,
                'pageTitle'       => $article->title(),
                'page'            => 'blog',
            ]
        );
    }

    public static function create(): self { /* ... */ }
}
```

---

#### Task 8: Refactor DisplayPageAction and DisplayFormAction
**Status:** ⏳ Pending

**Files:**
- `DisplayPageAction.php` (73 lines → ~45)
- `DisplayFormAction.php` (160 lines → ~70)

**Changes:**
- Extract dynamic page rendering logic
- Extract form submission handling
- Better error messages

---

### Phase 3: Auth Actions Refactor (Tasks 9-10)

#### Task 9: Refactor LoginAction
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Auth/LoginAction.php`

**Current:** 126 lines  
**Target:** ~70 lines

**Changes:**
- Extract authentication logic to service
- Simplify session management
- Better error messages
- Consistent redirect handling

---

#### Task 10: Refactor LogoutAction and RegisterAction
**Status:** ⏳ Pending

**Files:**
- `LogoutAction.php` (44 lines → ~30)
- `RegisterAction.php` (82 lines → ~50)

**Changes:**
- Simplify logout logic
- Extract registration validation

---

### Phase 4: Admin Actions Refactor (Tasks 11-15)

#### Task 11: Refactor DashboardAction
**Status:** ⏳ Pending

**File:** `src/Interfaces/HTTP/Actions/Admin/DashboardAction.php`

**Current:** 48 lines  
**Target:** ~35 lines

---

#### Task 12: Refactor Article Admin Actions
**Status:** ⏳ Pending

**Files:**
- `CreateArticleAction.php`
- `StoreArticleAction.php`
- `EditArticleAction.php`
- `UpdateArticleAction.php`
- `DeleteArticleAction.php`
- `PublishArticleAction.php`

**Pattern:**
- Use form request validation
- Extract business logic to services
- Consistent flash messages
- Standard redirect patterns

---

#### Task 13: Refactor Pages Admin Actions
**Status:** ⏳ Pending

**Files:**
- `CreatePageAction.php`
- `EditPageAction.php` (184 lines → ~80)
- `DeletePageAction.php`
- `PagesAction.php`

---

#### Task 14: Refactor Forms Admin Actions
**Status:** ⏳ Pending

**Files:**
- `CreateFormAction.php`
- `EditFormAction.php`
- `FormsAction.php`
- `FormSubmissionsAction.php`

---

#### Task 15: Refactor RBAC Admin Actions
**Status:** ⏳ Pending

**Files:**
- Roles: `CreateRoleAction.php`, `EditRoleAction.php`, `ListRolesAction.php`
- Permissions: `CreatePermissionAction.php`, `EditPermissionAction.php`, `ListPermissionsAction.php`

---

### Phase 5: Testing & Validation (Tasks 16-17)

#### Task 16: Add Static Analysis
**Status:** ⏳ Pending

**Actions:**
- Run PHPStan on all refactored actions
- Ensure level 8+ compliance
- Fix all type errors
- Add PHPDoc where needed

---

#### Task 17: Update Tests
**Status:** ⏳ Pending

**Actions:**
- Update existing action tests
- Add tests for refactored actions
- Ensure 80%+ coverage
- Test error scenarios

---

## Code Quality Rules

### Line Limits
- **Max 50 lines** per simple action (show only)
- **Max 80 lines** per complex action (CRUD with submit)
- **Max 3 methods** per action (handle, show, submit)

### Naming Conventions
- **Template paths:** `'pages/{category}/{name}'`
- **Layout names:** `'frontend'` or `'admin'`
- **Variables:** camelCase, descriptive names
- **Methods:** show(), submit(), handle()

### Error Handling
- **Validation errors:** Return 400 with message
- **Not found:** Return 404 with message
- **Server errors:** Log error, return 500
- **Unauthorized:** Return 401 or redirect

### Type Safety
- **Strict types:** `declare(strict_types = 1);`
- **Type hints:** All parameters and returns
- **Null handling:** Explicit `?Type` or `|null`
- **Arrays:** Typed arrays `array<string, mixed>`

---

## Commit Plan

### Commit 1: Base Action Enhancements
**Tasks:** 1, 2  
**Message:** `refactor(actions): add helper methods and traits to base Action class`

### Commit 2: Simple Frontend Actions
**Tasks:** 3  
**Message:** `refactor(actions): simplify portfolio, docs, and static page actions`

### Commit 3: Complex Frontend Actions
**Tasks:** 4, 5, 6, 7, 8  
**Message:** `refactor(actions): refactor home, blog, contact, and article actions`

### Commit 4: Auth Actions
**Tasks:** 9, 10  
**Message:** `refactor(actions): simplify login, logout, and register actions`

### Commit 5: Admin Actions
**Tasks:** 11, 12, 13, 14, 15  
**Message:** `refactor(actions): refactor all admin CRUD actions`

### Commit 6: Testing & QA
**Tasks:** 16, 17  
**Message:** `test(actions): add tests and fix static analysis issues`

---

## Success Metrics

- ✅ All actions under 80 lines
- ✅ Consistent patterns across all actions
- ✅ Zero PHPStan errors (level 8+)
- ✅ All tests passing
- ✅ 80%+ test coverage
- ✅ Reduced code duplication by 50%
- ✅ Improved readability score

---

## Risk Mitigation

**Risk:** Breaking existing functionality  
**Mitigation:**
1. Run full test suite after each commit
2. Manual testing of critical paths
3. Gradual rollout (frontend → auth → admin)

**Risk:** Merge conflicts  
**Mitigation:**
1. Work in feature branch
2. Frequent rebases on main
3. Small, focused commits

---

**Estimated Effort:** 8-12 hours  
**Risk Level:** Medium  
**Rollback Plan:** Revert commits if tests fail
