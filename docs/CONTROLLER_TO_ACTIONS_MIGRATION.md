# Controller to Actions Migration Plan

## Current State Audit

### Already Migrated to Actions (100% complete)
| Feature | Status | Actions |
|---------|--------|---------|
| **Auth** | Done | `LoginAction`, `LogoutAction` |
| **Dashboard** | Done | `DashboardAction` |
| **Pages (Admin)** | Done | `PagesAction`, `CreatePageAction`, `EditPageAction`, `DeletePageAction` |
| **Forms (Admin)** | Done | `FormsAction`, `CreateFormAction`, `EditFormAction`, `FormSubmissionsAction` |
| **Articles (Admin)** | Done | `ArticlesAction` |
| **Media (Admin)** | Done | `MediaAction` |
| **Roles (Admin)** | Done | `ListRolesAction`, `CreateRoleAction`, `EditRoleAction` |
| **Permissions (Admin)** | Done | `ListPermissionsAction`, `CreatePermissionAction`, `EditPermissionAction` |
| **Home (Frontend)** | Done | `HomeAction` |
| **Articles (Frontend)** | Done | `ListArticlesAction`, `ShowArticleAction`, `ByTagAction`, `SearchArticlesAction` |
| **Contact (Frontend)** | Done | `ContactAction` |
| **Forms (Frontend)** | Done | `DisplayFormAction` |
| **Pages (Frontend)** | Done | `DisplayPageAction` |

### Controllers to Remove (Legacy)
| Controller | Location | Methods | Replacement |
|------------|----------|---------|-------------|
| `ArticleController` (Admin) | `interfaces/HTTP/Admin/` | `create`, `store`, `edit`, `update`, `destroy`, `publish` | Create `Admin/Article/*Action` classes |
| `SettingsController` (Admin) | `interfaces/HTTP/Admin/` | `index`, `update` | Create `Admin/Settings/*Action` classes |
| `MediaController` (Admin) | `interfaces/HTTP/Admin/` | `destroy` | Extend `MediaAction` with delete |
| `PageController` (Admin) | `interfaces/HTTP/Admin/` | (covered by Actions) | **DELETE** |
| `DashboardController` (Admin) | `interfaces/HTTP/Admin/` | (covered by Action) | **DELETE** |
| `FormController` (Admin) | `interfaces/HTTP/Admin/` | (covered by Actions) | **DELETE** |
| `HomeController` (Frontend) | `interfaces/HTTP/Frontend/` | (covered by Action) | **DELETE** |
| `ArticleController` (Frontend) | `interfaces/HTTP/Frontend/` | (covered by Actions) | **DELETE** |
| `PageController` (Frontend) | `interfaces/HTTP/Frontend/` | (covered by Action) | **DELETE** |
| `ContactController` (Frontend) | `interfaces/HTTP/Frontend/` | (covered by Action) | **DELETE** |
| `FormController` (Frontend) | `interfaces/HTTP/Frontend/` | (covered by Action) | **DELETE** |

---

## Migration Tasks

### Priority 1: Complete Article Admin Actions (Priority: 8)
Create dedicated Action classes for Article CRUD operations:

```
src/interfaces/HTTP/Actions/Admin/Article/
├── ListArticlesAction.php       (already exists as ArticlesAction)
├── CreateArticleAction.php      (NEW - from ArticleController::create)
├── StoreArticleAction.php       (NEW - from ArticleController::store)
├── EditArticleAction.php        (NEW - from ArticleController::edit)
├── UpdateArticleAction.php      (NEW - from ArticleController::update)
├── DeleteArticleAction.php      (NEW - from ArticleController::destroy)
└── PublishArticleAction.php     (NEW - from ArticleController::publish)
```

**Routes to update:**
```php
// Current (TODO comments in Kernel.php)
$this->router->get('/admin/articles/create', [ArticleController::class, 'create']);
$this->router->post('/admin/articles', [ArticleController::class, 'store']);
// etc.

// After migration
$this->router->get('/admin/articles/create', CreateArticleAction::class);
$this->router->post('/admin/articles', StoreArticleAction::class);
// etc.
```

### Priority 2: Settings Actions (Priority: 7)
```
src/interfaces/HTTP/Actions/Admin/Settings/
├── ViewSettingsAction.php       (NEW - from SettingsController::index)
└── UpdateSettingsAction.php     (NEW - from SettingsController::update)
```

### Priority 3: Media Delete Action (Priority: 6)
```
src/interfaces/HTTP/Actions/Admin/Media/
└── DeleteMediaAction.php        (NEW - extend MediaAction)
```

### Priority 4: Cleanup Legacy Controllers (Priority: 5)
Delete these files after migration:
```
src/interfaces/HTTP/Admin/
├── ArticleController.php        (after Priority 1)
├── SettingsController.php       (after Priority 2)
├── MediaController.php          (after Priority 3)
├── PageController.php           (safe to delete now)
├── DashboardController.php      (safe to delete now)
├── FormController.php           (safe to delete now)

src/interfaces/HTTP/Frontend/
├── HomeController.php           (safe to delete now)
├── ArticleController.php        (safe to delete now)
├── PageController.php           (safe to delete now)
├── ContactController.php        (safe to delete now)
├── FormController.php           (safe to delete now)
```

---

## Action Pattern Template

### Single Action Class (for simple operations)
```php
<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleService;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListArticlesAction extends Action
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $articles = $this->articleService->getAllArticles();

        $content = $this->renderer->render(
            'admin/articles/index',
            ['title' => 'Articles', 'articles' => $articles],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ArticleService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
```

### Multi-Method Action Class (for CRUD operations)
```php
<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleService;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateArticleAction extends Action
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/articles/create',
            ['title' => 'Create Article', 'error' => null],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function store(Request $request): Response
    {
        try {
            // Handle store logic
            return $this->redirect('/admin/articles');
        } catch (\RuntimeException $e) {
            $content = $this->renderer->render(
                'admin/articles/create',
                ['title' => 'Create Article', 'error' => $e->getMessage()],
                'admin/layouts/base'
            );

            return $this->html($content, 400);
        }
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return $this->show($request);
        }

        if ($request->getMethod() === 'POST') {
            return $this->store($request);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ArticleService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
```

---

## Migration Checklist

- [ ] **Priority 1**: Create Article CRUD Actions
  - [ ] `CreateArticleAction.php`
  - [ ] `StoreArticleAction.php`
  - [ ] `EditArticleAction.php`
  - [ ] `UpdateArticleAction.php`
  - [ ] `DeleteArticleAction.php`
  - [ ] `PublishArticleAction.php`
  - [ ] Update routes in `Kernel.php`
  - [ ] Test all article operations

- [ ] **Priority 2**: Create Settings Actions
  - [ ] `ViewSettingsAction.php`
  - [ ] `UpdateSettingsAction.php`
  - [ ] Update routes in `Kernel.php`
  - [ ] Test settings save/load

- [ ] **Priority 3**: Create Media Delete Action
  - [ ] `DeleteMediaAction.php`
  - [ ] Update routes in `Kernel.php`
  - [ ] Test media deletion

- [ ] **Priority 4**: Delete Legacy Controllers
  - [ ] Delete `src/interfaces/HTTP/Admin/ArticleController.php`
  - [ ] Delete `src/interfaces/HTTP/Admin/SettingsController.php`
  - [ ] Delete `src/interfaces/HTTP/Admin/MediaController.php`
  - [ ] Delete `src/interfaces/HTTP/Admin/PageController.php`
  - [ ] Delete `src/interfaces/HTTP/Admin/DashboardController.php`
  - [ ] Delete `src/interfaces/HTTP/Admin/FormController.php`
  - [ ] Delete `src/interfaces/HTTP/Frontend/HomeController.php`
  - [ ] Delete `src/interfaces/HTTP/Frontend/ArticleController.php`
  - [ ] Delete `src/interfaces/HTTP/Frontend/PageController.php`
  - [ ] Delete `src/interfaces/HTTP/Frontend/ContactController.php`
  - [ ] Delete `src/interfaces/HTTP/Frontend/FormController.php`

- [ ] **Priority 5**: Update Documentation
  - [ ] Update `docs/ARCHITECTURE_JOURNEY.md`
  - [ ] Update `docs/QUICK_REFERENCE.md`
  - [ ] Add Actions pattern examples to `docs/APPPATHS_USAGE.md`

---

## Progress Tracking

| Phase | Status | Completion |
|-------|--------|------------|
| Auth Actions | Complete | 100% |
| Dashboard Actions | Complete | 100% |
| Pages Actions | Complete | 100% |
| Forms Actions | Complete | 100% |
| Articles Actions | Partial | 20% (only list) |
| Media Actions | Partial | 80% (missing delete) |
| Settings Actions | Not Started | 0% |
| Roles/Permissions | Complete | 100% |
| Frontend Actions | Complete | 100% |

**Overall Progress: 100% Complete**

---

## Migration Complete! (2026-02-28)

All controllers have been successfully migrated to the Actions pattern:

### Completed Migrations
- **Article CRUD** - 6 Actions (Create, Store, Edit, Update, Delete, Publish)
- **Settings** - 2 Actions (View, Update)
- **Media** - Delete Action added
- **Legacy Controllers** - All 11 deleted

### New Action Files Created
```
src/interfaces/HTTP/Actions/Admin/Article/
├── CreateArticleAction.php
├── StoreArticleAction.php
├── EditArticleAction.php
├── UpdateArticleAction.php
├── DeleteArticleAction.php
└── PublishArticleAction.php

src/interfaces/HTTP/Actions/Admin/Settings/
├── ViewSettingsAction.php
└── UpdateSettingsAction.php

src/interfaces/HTTP/Actions/Admin/Media/
└── DeleteMediaAction.php
```

### Deleted Controller Files
```
src/interfaces/HTTP/Admin/
├── ArticleController.php       DELETED
├── SettingsController.php      DELETED
├── MediaController.php         DELETED
├── PageController.php          DELETED
├── DashboardController.php     DELETED
└── FormController.php          DELETED

src/interfaces/HTTP/Frontend/
├── HomeController.php          DELETED
├── ArticleController.php       DELETED
├── PageController.php          DELETED
├── ContactController.php       DELETED
└── FormController.php          DELETED
```

### Updated Files
- `src/interfaces/HTTP/Kernel.php` - All routes now use Actions
- `docs/CONTROLLER_TO_ACTIONS_MIGRATION.md` - This document

---

Last updated: 2026-02-28
