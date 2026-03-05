# PHP CMS - Quick Reference Guide

## Project Structure

```
php-cms/
├── domain/                 # Domain Layer (business logic)
│   ├── Model/             # Entities
│   ├── ValueObjects/      # Value Objects
│   ├── Repository/        # Repository interfaces
│   ├── Services/          # Domain Services
│   └── Events/            # Domain Events
│
├── application/           # Application Layer
│   ├── Services/         # Application Services (Managers)
│   ├── DTOs/            # Data Transfer Objects
│   ├── CQRS/            # Commands & Queries
│   ├── Saga/            # Saga orchestrators
│   └── Exceptions/      # Application Exceptions
│
├── infrastructure/       # Infrastructure Layer
│   ├── Persistence/     # Database, UoW, Repositories
│   ├── Paths/          # Path management
│   ├── Queue/          # SQLite Queue
│   ├── Container/      # DI Container
│   └── Storage/        # File storage
│
├── interfaces/          # Interfaces Layer
│   ├── HTTP/
│   │   ├── Actions/    # Request handlers
│   │   └── View/       # TemplateRenderer
│   └── Templates/      # Native PHP templates
│
├── public/             # Web root
├── data/               # SQLite databases
├── storage/            # Uploaded files, logs, cache
└── bin/                # CLI scripts
```

---

## Quick Commands

```bash
# Installation
composer install

# Database
php bin/cms migrate     # Create tables
php bin/cms seed        # Seed test data

# Server
php bin/cms serve       # Dev server (port 8000)
php -S localhost:8000 -t public

# Queue Worker
php bin/queue-worker.php default --tries=5

# Testing
composer test
composer test-coverage
```

---

## Key Classes

### Paths (AppPaths)
```php
use Infrastructure\Paths\AppPaths;

$paths = AppPaths::instance();
$paths->data('cms.db');           // /project/storage/data/cms.db
$paths->templates('frontend');     // /project/src/interfaces/Templates/frontend
$paths->cache('templates');        // /project/storage/cache/templates
$paths->logs('app.log');           // /project/storage/logs/app.log
```

### Database
```php
use Infrastructure\Persistence\DatabaseConnectionManager;

$db = new DatabaseConnectionManager();
$cmsConn = $db->getConnection('cms');
$jobsConn = $db->getConnection('jobs');
```

### DI Container
```php
use Infrastructure\Container\ContainerFactory;

$container = ContainerFactory::create();
$articleManager = $container->get(ArticleManager::class);
```

### CQRS
```php
// Command
$command = new CreateArticle($title, $content, $authorId);
$article = $commandBus->dispatch($command);

// Query
$query = new GetArticleBySlug($slug);
$article = $queryBus->dispatch($query);
```

### Domain Events
```php
// In entity
$article->publish(); // → ArticlePublished event

// In Application Service
$articleManager->publish($id); // → dispatch events
```

### Outbox Pattern
```php
// Automatically in ArticleManager
$articleManager->create(...); // → Event → Outbox → Queue
```

### Saga Pattern
```php
$saga = new PublishArticleSaga($articleManager, $queue);
try {
    $article = $saga->execute($articleId);
} catch (SagaExecutionFailedException $e) {
    // Automatic rollback
}
```

---

## Environment Variables

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CMS=data/cms.db
DB_JOBS=data/jobs.db

# Admin
ADMIN_EMAIL=admin@phpcms.local
ADMIN_PASSWORD=admin123

# Queue
QUEUE_CONNECTION=sqlite

# Cache
TEMPLATE_CACHE_VERSION=
```

---

## Template Usage

```php
// In Action
$content = $this->renderer->render(
    'pages/home',
    ['articles' => $articles, 'title' => 'Welcome'],
    'layouts/base'
);

// In template
<?= $this->e($title) ?>
<?= $this->date($article->publishedAt()) ?>
<?= $this->nl2br($article->content()) ?>
```

---

## Testing

```bash
# Unit tests
vendor/bin/phpunit tests/Domain

# Integration tests
vendor/bin/phpunit tests/Integration

# Coverage
composer test-coverage
```

---

## Debugging

```php
// Enable debug
APP_DEBUG=true

// Logs
tail -f storage/logs/app.log

// Database
sqlite3 data/cms.db
SELECT * FROM articles;

// Queue
sqlite3 data/jobs.db
SELECT * FROM jobs;
```

---

## Pattern Quick Reference

| Pattern | Purpose | Location |
|---------|---------|----------|
| Domain Events | Decoupled communication | domain/Events/ |
| Repository | Data access abstraction | domain/Repository/ |
| CQRS | Read/Write separation | application/CQRS/ |
| Outbox | Reliable events | infrastructure/Queue/ |
| Saga | Distributed transactions | application/Saga/ |
| Actions | Request handling | interfaces/HTTP/Actions/ |
| DI Container | Dependency injection | infrastructure/Container/ |

---

Quick Reference - 2026-02-27
