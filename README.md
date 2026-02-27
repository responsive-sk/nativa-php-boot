# PHP CMS / Blog Platform

Moderný PHP 8.4+ CMS a blog systém s admin panelom.

## Rýchly štart

```bash
# Inštalácia dependencies
composer install

# Vytvorenie .env súboru
cp .env.example .env

# Vytvorenie databázy
php bin/cms migrate

# Vytvorenie testovacích dát
php bin/cms seed

# Spustenie development servera
php bin/cms serve
# Alebo priamo:
php -S localhost:8000 -t public
```

Otvoriť: http://localhost:8000
Admin: http://localhost:8000/admin

## Features

### Frontend
- ✅ Homepage s najnovšími článkami
- ✅ Zoznam článkov (Articles)
- ✅ Detail článku
- ✅ Contact form
- ✅ Statické stránky
- 🔄 Kategórie a tagy
- 🔄 Vyhľadávanie

### Admin Panel
- ✅ Dashboard s štatistikami
- ✅ CRUD článkov
- 🔄 CRUD stránok
- 🔄 Form Builder
- 🔄 Správa formulárov
- 🔄 Media library
- 🔄 Settings

## Štruktúra projektu

```
php-cms/
├── domain/                 # Domain Layer (biznis logika)
│   ├── Model/             # Entities (Article, Page, Form)
│   ├── ValueObjects/      # Value Objects (Slug, Email, Status)
│   └── Repository/        # Repository interfaces
│
├── application/           # Application Layer
│   ├── Services/         # Application Services
│   └── DTOs/            # Data Transfer Objects
│
├── infrastructure/       # Infrastructure Layer
│   ├── Persistence/     # Database, UnitOfWork, Repositories
│   └── Storage/         # File storage
│
├── interfaces/          # Interfaces Layer
│   ├── HTTP/
│   │   ├── Frontend/   # Public controllers
│   │   └── Admin/      # Admin controllers
│   └── Templates/      # View templates
│
├── public/             # Web root
│   └── index.php       # Entry point
│
├── bin/                # CLI scripts
│   └── cms             # Console entry point
│
└── data/               # SQLite database
```

## Architecture

DDD (Domain-Driven Design) architektúra inšpirovaná Python task-managerom:

1. **Domain Layer** - Čistá biznis logika bez závislostí
2. **Application Layer** - Use case-y a aplikačná logika
3. **Infrastructure Layer** - DB, external services
4. **Interfaces Layer** - HTTP, CLI

## Príkazy

```bash
php bin/cms migrate     # Vytvoriť databázové tabuľky
php bin/cms seed        # Vytvoriť testovacie dáta
php bin/cms serve       # Spustiť dev server (port 8000)
```

## Defaultné prihlasovacie údaje

- **Email:** admin@phpcms.local
- **Password:** admin123

## Tech Stack

- **PHP:** 8.4+
- **Database:** SQLite (PDO)
- **Template Engine:** Twig
- **HTTP Foundation:** Symfony
- **Console:** Symfony Console
- **Router:** Yiisoft Router + FastRoute
- **Frontend:** TailwindCSS + Alpine.js

## Development

```bash
# Run tests
composer test

# Run tests with coverage (requires Xdebug)
composer test-coverage

# Run C3 server coverage
composer test-c3

# Static analysis
composer phpstan

# Code style fix
composer cs-fix

# Rector (PHP upgrade)
composer rector
```

## Testing & Coverage

See [docs/COVERAGE.md](docs/COVERAGE.md) for detailed coverage setup.

```bash
# Run all tests
composer test

# Run with coverage report
composer test-coverage
open tests/_output/coverage/index.html

# C3 server-side coverage
composer serve  # Start server in one terminal
composer test-c3  # Run tests in another
```

## License

MIT
