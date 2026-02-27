# PHP CMS - Project Context

## Project Overview

Modern **PHP 8.4+ CMS and Blog Platform** with admin panel, built using **Domain-Driven Design (DDD)** architecture. The project implements a clean separation of concerns across four layers: Domain, Application, Infrastructure, and Interfaces.

### Key Features
- **Frontend**: Homepage with articles, article listing/detail, static pages, contact forms, custom dynamic forms
- **Admin Panel**: Dashboard, CRUD for articles/pages/forms, form builder, media library, settings management
- **Database**: SQLite with PDO
- **Frontend Stack**: TailwindCSS + Alpine.js

## Architecture (DDD)

```
php-cms/
├── domain/                 # Domain Layer - Pure business logic, no external dependencies
│   ├── Model/             # Entities (Article, Page, Form, FormSubmission)
│   ├── ValueObjects/      # Value Objects (Slug, ArticleStatus, Email)
│   ├── Services/          # Domain Services
│   ├── Events/            # Domain Events
│   └── Repository/        # Repository interfaces
│
├── application/           # Application Layer - Use cases and application logic
│   ├── Services/         # Application Services
│   ├── DTOs/            # Data Transfer Objects
│   └── Exceptions/      # Application Exceptions
│
├── infrastructure/       # Infrastructure Layer - External concerns
│   ├── Persistence/     # Database (UnitOfWork, Repositories implementations)
│   ├── Storage/         # File storage
│   └── Messaging/       # Email notifications
│
├── interfaces/          # Interfaces Layer - Adapters
│   ├── HTTP/           # Web controllers (Frontend + Admin)
│   │   ├── Frontend/   # Public controllers
│   │   └── Admin/      # Admin controllers
│   ├── CLI/            # Console commands
│   └── Templates/      # Twig view templates
│
├── public/             # Web root
│   └── index.php       # Application entry point
│
├── bin/                # CLI scripts
│   └── cms             # Console entry point
│
└── data/               # SQLite database storage
```

## Building and Running

### Prerequisites
- PHP 8.4+
- Composer
- Extensions: `ext-json`, `ext-mbstring`, `ext-pdo`, `ext-sqlite`

### Installation

```bash
# Install dependencies
composer install

# Create environment file
cp .env.example .env

# Run database migrations
php bin/cms migrate

# Seed test data (optional)
php bin/cms seed

# Start development server
php bin/cms serve
# Or directly:
php -S localhost:8000 -t public
```

**Access:**
- Frontend: http://localhost:8000
- Admin: http://localhost:8000/admin

### Default Admin Credentials
- **Email:** admin@phpcms.local
- **Password:** admin123

### CLI Commands

```bash
php bin/cms migrate     # Create database tables
php bin/cms seed        # Seed test data
php bin/cms serve       # Start dev server (default port 8000)
php bin/cms serve -p 8080  # Start on custom port
```

### Composer Scripts

```bash
composer test           # Run PHPUnit tests
composer test-coverage  # Run tests with HTML coverage report
composer test-c3        # Run Codeception tests (C3 server coverage)
composer phpstan        # Static analysis
composer cs-fix         # Code style fixing
composer rector         # PHP version upgrades/refactoring
composer analyze        # Run all checks (test + phpstan + cs-fix)
```

## Testing

The project uses **PHPUnit** for unit/integration tests and **Codeception** for acceptance tests with C3 code coverage.

### Test Structure
- `tests/Domain/` - Domain layer tests
- `tests/Application/` - Application layer tests
- `tests/Infrastructure/` - Infrastructure layer tests
- `tests/Integration/` - Integration tests

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage (requires Xdebug)
composer test-coverage
# Coverage report: tests/_output/coverage/index.html

# C3 coverage (requires running server)
composer serve          # Terminal 1
composer test-c3        # Terminal 2
```

## Development Conventions

### Coding Standards
- **PSR-4** autoloading with namespace mapping:
  - `Domain\` → `domain/`
  - `Application\` → `application/`
  - `Infrastructure\` → `infrastructure/`
  - `Interfaces\` → `interfaces/`
  - `Tests\` → `tests/`

### Entity Pattern
- Entities use **hex ID generation** (`bin2hex(random_bytes(16))`)
- **Immutable construction** via `create()` factory methods
- **Array hydration** via `fromArray()` static method
- **Value Objects** for domain concepts (Slug, ArticleStatus, Email)
- **State transitions** via domain methods (e.g., `publish()`, `unpublish()`)

### Repository Pattern
- Interfaces defined in `domain/Repository/`
- Implementations in `infrastructure/Persistence/Repositories/`
- **UnitOfWork** pattern for transaction management

### Value Objects
Located in `domain/ValueObjects/`:
- `Slug` - URL-friendly identifiers with auto-generation from titles
- `ArticleStatus` - Status with state transitions (draft/published/archived)
- `Email` - Email validation

### Dependency Injection
- Manual dependency injection (no DI container)
- Controllers instantiated per-request in `Kernel`
- Services instantiated in controllers or passed manually

### Database
- **SQLite** for development/testing
- Raw SQL with **PDO prepared statements**
- Schema migrations in `bin/cms` CLI command
- **JSON columns** for flexible data (form schemas, submissions)

### Routing
- **Custom Router** implementation (`Interfaces\HTTP\Router`)
- Routes defined in `interfaces/HTTP/Kernel.php`
- Support for method override (`_method` parameter, `X-Http-Method-Override` header)
- Controller resolution from `[Class::class, 'method']` arrays
- Pattern matching with named parameters (e.g., `/articles/{slug}`)

> **Note:** `yiisoft/router` and `yiisoft/router-fastroute` are listed in `composer.json` but **not used** - the project uses its own simple router implementation.

### Templates
- **Native PHP templates**
- Templates in `interfaces/Templates/`
- Frontend and Admin template directories

#### TemplateRenderer

`Interfaces\HTTP\View\TemplateRenderer` provides native PHP template rendering with layout support:

```php
$renderer = new TemplateRenderer(
    $basePath . '/interfaces/Templates/frontend',
    $basePath . '/storage/cache/templates',
    $debug
);

$content = $renderer->render(
    'pages/home',           // template path (without .php)
    ['articles' => $articles], // data
    'layouts/base'          // layout (optional)
);
```

**Available helper methods in templates:**
- `$this->e($value)` - Escape HTML
- `$this->date($date, $format)` - Format date (default: 'M d, Y')
- `$this->nl2br($text)` - Convert newlines to `<br>`
- `$this->isEmpty($value)` - Check if value is empty
- `$this->url($path)` - Generate full URL

## Key Files Reference

| File | Purpose |
|------|---------|
| `public/index.php` | Application entry point, bootstrap |
| `interfaces/HTTP/Kernel.php` | HTTP kernel, route registration, request handling |
| `bin/cms` | CLI console entry point |
| `composer.json` | Dependencies, autoload, scripts |
| `phpunit.xml` | PHPUnit configuration |
| `codeception.yml` | Codeception configuration |
| `.env.example` | Environment variables template |

## Environment Variables

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=data/cms.db

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM=noreply@phpcms.local

ADMIN_EMAIL=admin@phpcms.local
ADMIN_PASSWORD=admin123
```

## Tech Stack Summary

| Component | Technology |
|-----------|------------|
| Language | PHP 8.4+ |
| Database | SQLite (PDO) |
| Router | Yiisoft Router + FastRoute |
| HTTP Foundation | Symfony HttpFoundation |
| Console | Symfony Console |
| Templates | Twig |
| CSS Framework | TailwindCSS |
| JS Framework | Alpine.js |
| Testing | PHPUnit + Codeception |
| Static Analysis | PHPStan |
| Code Style | PHP CS Fixer |
| Refactoring | Rector |

## Common Tasks

### Adding a New Entity
1. Create entity in `domain/Model/`
2. Create Value Objects in `domain/ValueObjects/` (if needed)
3. Create Repository interface in `domain/Repository/`
4. Create Application Service in `application/Services/`
5. Create Repository implementation in `infrastructure/Persistence/Repositories/`
6. Create Controller in `interfaces/HTTP/Frontend/` or `interfaces/HTTP/Admin/`
7. Add routes in `interfaces/HTTP/Kernel.php`
8. Create templates in `interfaces/Templates/`
9. Add migration to `bin/cms`

### Adding a New Route
Add to `interfaces/HTTP/Kernel.php`:
```php
$this->router->get('/path', [Controller::class, 'method']);
```

### Running Code Quality Checks
```bash
composer analyze  # Runs tests, phpstan, and cs-fix
```
