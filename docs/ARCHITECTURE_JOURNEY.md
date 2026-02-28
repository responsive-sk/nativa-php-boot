# PHP CMS - Architektonický Journey

## 📅 Dátum: 2026-02-28

Tento dokument zachytáva kompletný proces refactorovania a vylepšovania architektúry PHP CMS projektu.

---

## 🎯 Počiatočný Stav

**Projekt:** Moderný PHP 8.4+ CMS a blog systém s DDD architektúrou

**Pôvodný Tech Stack:**
- PHP 8.4+
- Twig templates
- SQLite (PDO)
- Symfony HttpFoundation + Console
- Custom Router
- PHPUnit + Codeception

**Pôvodná Architektúra:**
```
php-cms/
├── domain/                 # Domain Layer
├── application/           # Application Layer
├── infrastructure/        # Infrastructure Layer
├── interfaces/           # Interfaces Layer
└── public/              # Web root
```

---

## 📋 Postup Refactoringu

### Fáza 1: Native PHP Templates (Migrácia z Twig)

**Problém:** Twig dependency nebola potrebná, chceli sme native PHP templates.

**Riešenie:**
1. Vytvorený `TemplateRenderer` s layout supportom
2. Konvertované všetky `.twig` súbory na `.php`
3. Odstránené `twig/twig` z `composer.json`

**Vytvorené súbory:**
```
interfaces/HTTP/View/TemplateRenderer.php
interfaces/Templates/frontend/layouts/base.php
interfaces/Templates/frontend/pages/home.php
interfaces/Templates/frontend/pages/articles/index.php
interfaces/Templates/frontend/pages/articles/show.php
```

**Kľúčové vlastnosti TemplateRenderer:**
- Layout inheritance
- Partial templates
- Helper metódy: `e()`, `date()`, `nl2br()`, `url()`
- Template caching s versioningom

---

### Fáza 2: Domain Events Pattern

**Problém:** Entity nemohli komunikovať s ostatnými časťami systému bez couplingu.

**Riešenie:** Implementácia Domain Events

**Vytvorené súbory:**
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
// V entite
public function publish(): void
{
    $this->status = ArticleStatus::published();
    $this->recordEvent(new ArticlePublished($this->id, $this->title, $this->publishedAt));
}

// V Application Service
private function dispatchEvents(Article $article): void
{
    foreach ($article->releaseEvents() as $event) {
        $this->eventDispatcher->dispatch($event);
    }
}
```

---

### Fáza 3: Repository Pattern Completion

**Problém:** Chýbalo `UserRepositoryInterface` pre User entitu.

**Riešenie:** Kompletný Repository Pattern

**Vytvorené súbory:**
```
domain/Model/User.php
domain/Repository/UserRepositoryInterface.php
infrastructure/Persistence/Repositories/UserRepository.php
application/Services/UserManager.php
```

**UserManager features:**
- `create()` - s password hashing
- `authenticate()` - email + password verify
- `changePassword()` - s hashovaním
- CRUD operácie

---

### Fáza 4: Input Validation Layer

**Problém:** Žiadna validácia vstupov v Application Services.

**Riešenie:** Validator + DTO pattern

**Vytvorené súbory:**
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

**Príklad:**
```php
$command = new CreateArticleCommand(
    title: "Článok",
    content: "Obsah...",
    authorId: "uuid..."
); // Automaticky validované v konštruktore

$article = $articleManager->createFromCommand($command);
```

---

### Fáza 5: CQRS Pattern

**Problém:** Mixovanie read/write operácií.

**Riešenie:** Command Bus + Query Bus

**Vytvorené súbory:**
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

### Fáza 6: Actions Pattern (Refactor Controllers)

**Problém:** MVC Controllers sú anti-pattern v DDD.

**Riešenie:** Actions pattern - jedna akcia = jedna trieda

**Vytvorené súbory:**
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

**Príklad:**
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

### Fáza 7: Lightweight DI Container

**Problém:** Manual dependency injection v controllers.

**Riešenie:** Auto-wiring DI Container

**Vytvorené súbory:**
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
- Auto-wiring cez Reflection
- Singleton support
- Service Providers pattern
- Method injection

---

### Fáza 8: Multi-Database Dispatcher

**Problém:** Potreba pracovať s viacerými SQLite databázami (cms.db, jobs.db).

**Riešenie:** DatabaseConnectionManager

**Vytvorené súbory:**
```
infrastructure/Persistence/
├── DatabaseConnectionManager.php
└── MultiDatabaseUnitOfWork.php
```

**Konfigurácia:**
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

### Fáza 9: SQLite Queue System

**Problém:** Potreba async job queue pre background processing.

**Riešenie:** SQLite-based Queue

**Vytvorené súbory:**
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

**Tabuľky:**
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

### Fáza 10: Outbox Pattern

**Problém:** Reliable event publishing - čo ak event publish zlyhá po save entity?

**Riešenie:** Outbox Pattern - events sa ukladajú v rámci tej istej transakcie

**Vytvorené súbory:**
```
infrastructure/Queue/Handlers/OutboxProcessor.php
```

**Pattern:**
```php
// V rámci transakcie
$entityManager->persist($article);
$outboxProcessor->add('ArticleCreated', $eventData);
// Obe sa commitnú spolu

// Background job spracuje outbox
$outboxProcessor->process(); // Pushne events do queue
```

**Tabuľka:**
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

### Fáza 11: Saga Pattern

**Problém:** Distributed transactions naprieč bounded contexts.

**Riešenie:** Saga Orchestrator s rollback supportom

**Vytvorené súbory:**
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
    // Automatický rollback všetkých krokov
}
```

---

### Fáza 12: slim4-paths Integration

**Problém:** Boj s cestami (`dirname(__DIR__, N)`) v celom projekte.

**Riešenie:** Integrovať slim4-paths priamo do jadra appky (nie ako externý balík)

**Kroky:**
1. Odstránené `responsive-sk/slim4-paths` z composer.json
2. Presunuté `slim4-paths-main/src/*` do `infrastructure/Paths/`
3. Aktualizované namespace na `Infrastructure\Paths`
4. Vytvorený `AppPaths` singleton

**Vytvorené súbory:**
```
infrastructure/Paths/
├── Paths.php (upravený z slim4-paths)
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
$paths->data('cms.db');        // /project/data/cms.db
$paths->templates('frontend');  // /project/interfaces/Templates/frontend
$paths->cache('templates');     // /project/storage/cache/templates
$paths->logs('app.log');        // /project/storage/logs/app.log
```

**Výhody:**
- ✅ Žiadne `dirname(__DIR__, N)`
- ✅ Centralizované cesty
- ✅ Bezpečné path joining
- ✅ Framework agnostic
- ✅ Test-friendly

---

## 🏗️ Výsledná Architektúra

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

## 📊 Štatistiky

**Počet vytvorených súborov:** 80+

**Počet upravených súborov:** 30+

**Implementované patterny:**
1. Domain Events
2. Repository Pattern
3. CQRS
4. Outbox Pattern
5. Saga Pattern
6. Actions Pattern
7. Dependency Injection
8. Unit of Work
9. Data Mapper

**Odstránené dependency:**
- `twig/twig`
- `responsive-sk/slim4-paths`

---

## 🎓 Kľúčové Learnings

### 1. Domain Events sú nevyhnutné
- Umožňujú decoupled komunikáciu
- Podpora pre event sourcing v budúcnosti

### 2. CQRS sa oplatí
- Clear separation of concerns
- Read/Write optimalizácie nezávisle

### 3. Outbox Pattern garantuje reliabilitu
- Events sa nestratia
- Async processing bez race conditions

### 4. Saga Pattern rieši distributed transactions
- Rollback pri zlyhaní
- Orchestrácia komplexných workflow

### 5. Actions > Controllers
- Single responsibility
- Testovateľnosť
- Žiadne MVC bloat

### 6. Vlastný DI Container stačí
- Nepotrebuješ zložitý framework
- Auto-wiring cez Reflection

### 7. SQLite Queue je dostatočná
- Pre väčšinu projektov
- Žiadny Redis/RabbitMQ overhead

### 8. slim4-paths rieši bolesti s cestami
- Žiadne `dirname(__DIR__, N)`
- Bezpečné path joining

---

## 🚀 Ďalšie Kroky (Budúcnosť)

1. **Dokončiť Admin CRUDs**
   - Page management
   - Form builder
   - Media library

2. **Testing**
   - Unit testy pre Domain layer
   - Integration testy pre Application services
   - E2E testy pre HTTP endpoints

3. **DevOps**
   - Docker kontajner
   - GitHub Actions CI/CD
   - Deployment scripts

4. **Performance**
   - Query optimalizácie
   - Cache stratégie
   - OPCache konfigurácia

---

## 📝 Záver

Tento refactor ukázal, že moderná PHP aplikácia nepotrebuje ťažké frameworky. S DDD, CQRS, a správnymi patternmi možno vytvoriť robustnú, testovateľnú a udržiavateľnú aplikáciu.

**Najväčšie výhry:**
- ✅ Žiadny Twig - native PHP templates
- ✅ Žiadny externý DI container - vlastný lightweight
- ✅ Žiadny Redis/RabbitMQ - SQLite queue stačí
- ✅ Žiadne bolesti s cestami - slim4-paths v jadre
- ✅ Clear architecture - DDD + CQRS + Sagas

---

## 📅 2026-02-28 - Final Classes & Complete Actions Migration

### Final Classes Implementation

**Rozhodnutie:** Všetky non-extensible triedy teraz používajú `final` keyword.

**Dôvody:**
1. **DDD compliance** - Domain modely nemajú byť dediteľné
2. **Bezpečnosť** - Prevencia overrideovania business logic
3. **Performance** - PHP optimalizuje final triedy
4. **Predvídateľnosť** - Kompozícia nad dedičnosťou

**Čo bolo zmenené:**
- ✅ Domain Models (12 tried) - final
- ✅ Value Objects (6 tried) - final
- ✅ Domain Events (14 tried) - final
- ✅ Actions (27+ tried) - final
- ✅ Application Services - final
- ✅ Repositories - final
- ✅ Storage Providers - final

**Vytvorená dokumentácia:**
- [FINAL_CLASSES.md](FINAL_CLASSES.md) - Komplexný sprievodca

### Complete Actions Migration

**Status:** ✅ 100% Complete - Všetky controllery migrované na Actions

**Nové Action triedy:**
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

**Odstránené controllery (11 súborov):**
- ❌ Admin: ArticleController, SettingsController, MediaController, PageController, DashboardController, FormController
- ❌ Frontend: HomeController, ArticleController, PageController, ContactController, FormController

**Vytvorená dokumentácia:**
- [CONTROLLER_TO_ACTIONS_MIGRATION.md](CONTROLLER_TO_ACTIONS_MIGRATION.md)

### AppPaths Cleanup

**Zmena:** Databázy presunuté z `/data/` do `/storage/data/`

**Dôvod:** Všetky runtime dáta majú byť pod `/storage/`

**Aktualizované:**
- ✅ `AppPaths::data()` teraz vracia `storage/data/`
- ✅ `.env` - `DB_CMS=cms.db` (nie `data/cms.db`)
- ✅ `.gitignore` - `/storage/data/*.db`

**Dokumentácia:**
- [APPPATHS_USAGE.md](APPPATHS_USAGE.md) - Aktualizovaný s novou štruktúrou

### Acceptance Tests Setup

**Stav:** ✅ Codeception Acceptance tests pripravené

**Vytvorené:**
- `tests/Acceptance.suite.yml` - Suite konfigurácia
- `tests/Acceptance/LoginCest.php` - Login testy
- `tests/Acceptance/AdminDashboardCest.php` - Dashboard testy
- `tests/Acceptance/RolesCest.php` - Roles testy
- `tests/Acceptance/PermissionsCest.php` - Permissions testy

**Nainštalované:**
- `codeception/module-phpbrowser` ^4.0

**Sprievodca:**
- Testy pripravené pre C3 code coverage

---

*Dokument vytvorený: 2026-02-27*
*Autor: AI Assistant + User Collaboration*

*Posledná aktualizácia: 2026-02-28*
