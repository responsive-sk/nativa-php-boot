# PHP CMS - Architecture Journey

## Date: 2026-02-28

This document captures the complete refactoring and architecture improvement process of the PHP CMS project.

---

## Initial State

**Project:** Modern PHP 8.4+ CMS and blog system with DDD architecture

**Original Tech Stack:**
- PHP 8.4+
- Twig templates
- SQLite (PDO)
- Symfony HttpFoundation + Console
- Custom Router
- PHPUnit + Codeception

**Original Architecture:**
```
php-cms/
├── domain/                 # Domain Layer
├── application/           # Application Layer
├── infrastructure/        # Infrastructure Layer
├── interfaces/           # Interfaces Layer
└── public/              # Web root
```

---

## Refactoring Process

### Phase 1: Native PHP Templates (Migration from Twig)

**Problem:** Twig dependency was unnecessary, we wanted native PHP templates.

**Solution:**
1. Created `TemplateRenderer` with layout support
2. Converted all `.twig` files to `.php`
3. Removed `twig/twig` from `composer.json`

**Created files:**
```
interfaces/HTTP/View/TemplateRenderer.php
interfaces/Templates/frontend/layouts/base.php
interfaces/Templates/frontend/pages/home.php
interfaces/Templates/frontend/pages/articles/index.php
interfaces/Templates/frontend/pages/articles/show.php
```

**TemplateRenderer key features:**
- Layout inheritance
- Partial templates
- Helper methods: `e()`, `date()`, `nl2br()`, `url()`
- Template caching with versioning

---

### Phase 2: Domain Events Pattern

**Problem:** Entities couldn't communicate with other parts of the system without coupling.

**Solution:** Domain Events implementation

**Created files:**
```
domain/Events/
├── DomainEventInterface.php
├── DomainEvent.php (base class)
├── EventDispatcherInterface.php
├── ArticleCreated.php
├── ArticlePublished.php
├── ArticleUpdated.php
├── ArticleDeleted.php
├── PageCreated.php
├── PageUpdated.php
└── FormSubmitted.php

application/Services/EventDispatcher.php
```

**Pattern:**
```php
// In entity
public function publish(): void
{
    $this->status = ArticleStatus::published();
    $this->recordEvent(new ArticlePublished($this->id, $this->title, $this->publishedAt));
}

// In Application Service
private function dispatchEvents(Article $article): void
{
    foreach ($article->releaseEvents() as $event) {
        $this->eventDispatcher->dispatch($event);
    }
}
```

---

### Phase 3: Repository Pattern Completion

**Problem:** Missing `UserRepositoryInterface` for User entity.

**Solution:** Complete Repository Pattern

**Created files:**
```
domain/Model/User.php
domain/Repository/UserRepositoryInterface.php
infrastructure/Persistence/Repositories/UserRepository.php
application/Services/UserManager.php
```

**UserManager features:**
- `create()` - with password hashing
- `authenticate()` - email + password verify
- `changePassword()` - with hashing
- CRUD operations

---

### Phase 4: Input Validation Layer

**Problem:** No input validation in Application Services.

**Solution:** Validator + DTO pattern

**Created files:**
```
application/Exceptions/ValidationException.php
application/Validation/Validator.php
application/DTOs/
├── CreateArticleCommand.php
├── UpdateArticleCommand.php
├── CreateUserCommand.php
└── AuthenticateUserCommand.php
```

**Validator rules:**
- `required`
- `min:N`, `max:N`
- `email`, `uuid`, `url`
- `alpha`, `numeric`

**Example:**
```php
$command = new CreateArticleCommand(
    title: "Article",
    content: "Content...",
    authorId: "uuid..."
); // Automatically validated in constructor

$article = $articleManager->createFromCommand($command);
```

---

### Phase 5: CQRS Pattern

**Problem:** Mixing read/write operations.

**Solution:** Command Bus + Query Bus

**Created files:**
```
application/CQRS/
├── CommandInterface.php
├── QueryInterface.php
├── CommandBus.php
├── QueryBus.php
└── Article/
    ├── Commands/
    │   ├── CreateArticle.php
    │   └── PublishArticle.php
    ├── Queries/
    │   ├── ListArticles.php
    │   └── GetArticleBySlug.php
    └── Handlers/
        ├── CreateArticleHandler.php
        ├── PublishArticleHandler.php
        ├── ListArticlesHandler.php
        └── GetArticleBySlugHandler.php
```

**Usage:**
```php
// Command
$command = new CreateArticle($title, $content, $authorId);
$article = $commandBus->dispatch($command);

// Query
$query = new GetArticleBySlug($slug);
$article = $queryBus->dispatch($query);
```

---

### Phase 6: Actions Pattern (Refactor Controllers)

**Problem:** MVC Controllers are an anti-pattern in DDD.

**Solution:** Actions pattern - one action = one class

**Created files:**
```
interfaces/HTTP/Actions/
├── ActionInterface.php
├── Action.php (base class)
└── Frontend/
    ├── HomeAction.php
    └── Article/
        ├── ListArticlesAction.php
        ├── ShowArticleAction.php
        ├── SearchArticlesAction.php
        └── ByTagAction.php
```

**Example:**
```php
class ShowArticleAction extends Action
{
    public function handle(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        $article = $this->articleManager->findBySlug($slug);

        return $this->html($this->renderer->render(...));
    }
}
```

---

### Phase 7: Lightweight DI Container

**Problem:** Manual dependency injection in controllers.

**Solution:** Auto-wiring DI Container

**Created files:**
```
infrastructure/Container/
├── Container.php
├── ContainerException.php
├── ContainerFactory.php
├── ServiceProviderInterface.php
└── Providers/
    ├── ArticleServiceProvider.php
    ├── UserServiceProvider.php
    ├── ViewServiceProvider.php
    └── CQRSServiceProvider.php
```

**Features:**
- Auto-wiring via Reflection
- Singleton support
- Service Providers pattern
- Method injection

---

### Phase 8: Multi-Database Dispatcher

**Problem:** Need to work with multiple SQLite databases (cms.db, jobs.db).

**Solution:** DatabaseConnectionManager

**Created files:**
```
infrastructure/Persistence/
├── DatabaseConnectionManager.php
└── MultiDatabaseUnitOfWork.php
```

**Configuration:**
```env
DB_CMS=data/cms.db
DB_JOBS=data/jobs.db
```

**Usage:**
```php
$dbManager = new DatabaseConnectionManager();
$cmsConn = $dbManager->getConnection('cms');
$jobsConn = $dbManager->getConnection('jobs');
```

---

### Phase 9: SQLite Queue System

**Problem:** Need async job queue for background processing.

**Solution:** SQLite-based Queue

**Created files:**
```
infrastructure/Queue/
├── Entities/
│   ├── Job.php
│   └── FailedJob.php
├── QueueRepository.php
├── Worker/
│   ├── Worker.php
│   ├── JobHandler.php
│   └── JobHandlerRegistry.php
└── Handlers/
    └── OutboxProcessor.php
```

**CLI Worker:**
```bash
php bin/queue-worker.php default --tries=5 --timeout=120
```

**Tables:**
```sql
CREATE TABLE jobs (
    id VARCHAR(36) PRIMARY KEY,
    queue VARCHAR(50),
    payload TEXT,
    attempts INTEGER DEFAULT 0,
    reserved_at DATETIME,
    available_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE failed_jobs (...);
```

---

### Phase 10: Outbox Pattern

**Problem:** Reliable event publishing - what if event publish fails after saving entity?

**Solution:** Outbox Pattern - events are saved within the same transaction

**Created files:**
```
infrastructure/Queue/Handlers/OutboxProcessor.php
```

**Pattern:**
```php
// Within transaction
$entityManager->persist($article);
$outboxProcessor->add('ArticleCreated', $eventData);
// Both are committed together

// Background job processes outbox
$outboxProcessor->process(); // Pushes events to queue
```

**Table:**
```sql
CREATE TABLE outbox (
    id VARCHAR(36) PRIMARY KEY,
    event_type VARCHAR(100),
    event_data TEXT,
    processed BOOLEAN DEFAULT FALSE,
    created_at DATETIME,
    processed_at DATETIME
);
```

---

### Phase 11: Saga Pattern

**Problem:** Distributed transactions across bounded contexts.

**Solution:** Saga Orchestrator with rollback support

**Created files:**
```
application/Saga/
├── SagaStepInterface.php
├── SagaStep.php (base class)
├── SagaOrchestrator.php
├── SagaException.php
├── SagaExecutionFailedException.php
└── Article/
    ├── PublishArticleSaga.php
    ├── PublishArticleStep.php
    ├── InvalidateCacheStep.php
    └── QueueNotificationStep.php
```

**Pattern:**
```php
$saga = new PublishArticleSaga($articleManager, $queue);

try {
    $article = $saga->execute($articleId);
    // 1. Publish article
    // 2. Invalidate cache
    // 3. Queue notification
} catch (SagaExecutionFailedException $e) {
    // Automatic rollback of all steps
}
```

---

### Phase 12: slim4-paths Integration

**Problem:** Struggling with paths (`dirname(__DIR__, N)`) throughout the project.

**Solution:** Integrate slim4-paths directly into the app core (not as external package)

**Steps:**
1. Removed `responsive-sk/slim4-paths` from composer.json
2. Moved `slim4-paths-main/src/*` to `infrastructure/Paths/`
3. Updated namespaces to `Infrastructure\Paths`
4. Created `AppPaths` singleton

**Created files:**
```
infrastructure/Paths/
├── Paths.php (modified from slim4-paths)
├── AppPaths.php (singleton wrapper)
├── PresetInterface.php
├── PresetManager.php
├── Presets/
├── Security/
└── Filesystem/
```

**Usage:**
```php
$paths = AppPaths::instance();
$paths->data('cms.db');        // /project/storage/data/cms.db
$paths->templates('frontend');  // /project/src/interfaces/Templates/frontend
$paths->cache('templates');     // /project/storage/cache/templates
$paths->logs('app.log');        // /project/storage/logs/app.log
```

**Benefits:**
- No more `dirname(__DIR__, N)`
- Centralized paths
- Safe path joining
- Framework agnostic
- Test-friendly

---

## Resulting Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      HTTP Request                           │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  Actions (HomeAction, ListArticlesAction, etc.)             │
│  - Single responsibility                                    │
│  - Request/Response handling                                │
└─────────────────────────────────────────────────────────────┘
                            │
            ┌───────────────┴───────────────┐
            │                               │
            ▼                               ▼
┌───────────────────────┐       ┌───────────────────────┐
│    SAGA Pattern       │       │    CQRS Pattern       │
│  - PublishArticleSaga │       │  - CommandBus         │
│  - Rollback support   │       │  - QueryBus           │
└───────────────────────┘       └───────────────────────┘
            │                               │
            └───────────────┬───────────────┘
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              OUTBOX PATTERN                                 │
│  - Events → outbox table (transactional)                    │
│  - OutboxProcessor → Queue                                  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│              SQLite Queue (jobs.db)                         │
│  - QueueRepository                                          │
│  - Worker CLI                                               │
│  - Job Handlers                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Statistics

**Number of created files:** 80+

**Number of modified files:** 30+

**Implemented patterns:**
1. Domain Events
2. Repository Pattern
3. CQRS
4. Outbox Pattern
5. Saga Pattern
6. Actions Pattern
7. Dependency Injection
8. Unit of Work
9. Data Mapper

**Removed dependencies:**
- `twig/twig`
- `responsive-sk/slim4-paths`

---

## Key Learnings

### 1. Domain Events are essential
- Enable decoupled communication
- Support for event sourcing in the future

### 2. CQRS is worth it
- Clear separation of concerns
- Read/Write optimizations independently

### 3. Outbox Pattern guarantees reliability
- Events don't get lost
- Async processing without race conditions

### 4. Saga Pattern solves distributed transactions
- Rollback on failure
- Orchestration of complex workflows

### 5. Actions > Controllers
- Single responsibility
- Testability
- No MVC bloat

### 6. Custom DI Container is sufficient
- No need for complex framework
- Auto-wiring via Reflection

### 7. SQLite Queue is sufficient
- For most projects
- No Redis/RabbitMQ overhead

### 8. slim4-paths solves path pains
- No more `dirname(__DIR__, N)`
- Safe path joining

---

## Next Steps (Future)

1. **Complete Admin CRUDs**
   - Page management
   - Form builder
   - Media library

2. **Testing**
   - Unit tests for Domain layer
   - Integration tests for Application services
   - E2E tests for HTTP endpoints

3. **DevOps**
   - Docker container
   - GitHub Actions CI/CD
   - Deployment scripts

4. **Performance**
   - Query optimizations
   - Cache strategies
   - OPCache configuration

---

## Conclusion

This refactoring showed that a modern PHP application doesn't need heavy frameworks. With DDD, CQRS, and the right patterns, you can create a robust, testable, and maintainable application.

**Biggest wins:**
- No Twig - native PHP templates
- No external DI container - custom lightweight
- No Redis/RabbitMQ - SQLite queue is enough
- No path pains - slim4-paths in the core
- Clear architecture - DDD + CQRS + Sagas

---

## 2026-02-28 - Final Classes & Complete Actions Migration

### Final Classes Implementation

**Decision:** All non-extensible classes now use the `final` keyword.

**Reasons:**
1. **DDD compliance** - Domain models should not be inheritable
2. **Security** - Prevention of business logic override
3. **Performance** - PHP optimizes final classes
4. **Predictability** - Composition over inheritance

**What was changed:**
- Domain Models (12 classes) - final
- Value Objects (6 classes) - final
- Domain Events (14 classes) - final
- Actions (27+ classes) - final
- Application Services - final
- Repositories - final
- Storage Providers - final

**Created documentation:**
- [FINAL_CLASSES.md](FINAL_CLASSES.md) - Comprehensive guide

### Complete Actions Migration

**Status:** 100% Complete - All controllers migrated to Actions

**New Action classes:**
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

**Deleted controllers (11 files):**
- Admin: ArticleController, SettingsController, MediaController, PageController, DashboardController, FormController
- Frontend: HomeController, ArticleController, PageController, ContactController, FormController

**Created documentation:**
- [CONTROLLER_TO_ACTIONS_MIGRATION.md](CONTROLLER_TO_ACTIONS_MIGRATION.md)

### AppPaths Cleanup

**Change:** Databases moved from `/data/` to `/storage/data/`

**Reason:** All runtime data should be under `/storage/`

**Updated:**
- `AppPaths::data()` now returns `storage/data/`
- `.env` - `DB_CMS=cms.db` (not `data/cms.db`)
- `.gitignore` - `/storage/data/*.db`

**Documentation:**
- [APPPATHS_USAGE.md](APPPATHS_USAGE.md) - Updated with new structure

### Acceptance Tests Setup

**Status:** Codeception Acceptance tests ready

**Created:**
- `tests/Acceptance.suite.yml` - Suite configuration
- `tests/Acceptance/LoginCest.php` - Login tests
- `tests/Acceptance/AdminDashboardCest.php` - Dashboard tests
- `tests/Acceptance/RolesCest.php` - Roles tests
- `tests/Acceptance/PermissionsCest.php` - Permissions tests

**Installed:**
- `codeception/module-phpbrowser` ^4.0

**Guide:**
- Tests ready for C3 code coverage

---

Document created: 2026-02-27
Author: AI Assistant + User Collaboration

Last updated: 2026-02-28
