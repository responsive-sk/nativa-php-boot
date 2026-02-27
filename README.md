# Nativa PHP Boot

Moderný PHP CMS a blog platform s **DDD architektúrou**.

## 🚀 Rýchly Štart

```bash
# Klonovanie
git clone https://github.com/responsive-sk/nativa-php-boot.git
cd nativa-php-boot

# Inštalácia
composer install

# Vytvorenie .env
cp .env.example .env

# Databáza
php src/bin/cms migrate
php src/bin/cms seed

# Dev server
php -S localhost:8000 -t public
```

Otvoriť: http://localhost:8000

**Admin:**
- URL: http://localhost:8000/admin
- Email: admin@phpcms.local
- Password: admin123

## 🏗 Architektúra

```
src/
├── domain/                 # Domain Layer (entities, value objects, events)
├── application/           # Application Layer (services, DTOs, CQRS, Sagas)
├── infrastructure/        # Infrastructure Layer (DB, Queue, Paths, Container)
└── interfaces/           # Interfaces Layer (HTTP Actions, Templates)
```

### Implementované Patterny

- ✅ **Domain-Driven Design** (DDD)
- ✅ **Domain Events**
- ✅ **Repository Pattern**
- ✅ **CQRS** (Command/Query Bus)
- ✅ **Outbox Pattern**
- ✅ **Saga Pattern** (s rollbackom)
- ✅ **Actions Pattern** (nie Controllers)
- ✅ **Dependency Injection** (auto-wiring)
- ✅ **Input Validation** (DTOs + Validator)

## 📚 Dokumentácia

- [Architecture Journey](docs/ARCHITECTURE_JOURNEY.md) - Kompletý príbeh refactorovania
- [Quick Reference](docs/QUICK_REFERENCE.md) - Rýchla referenčná príručka

## 🛠 Tech Stack

- **PHP:** 8.4+
- **Database:** SQLite (PDO)
- **Templates:** Native PHP (TemplateRenderer)
- **HTTP:** Symfony HttpFoundation
- **Console:** Symfony Console
- **Router:** Custom (Yiisoft Router available)
- **Queue:** SQLite-based
- **Testing:** PHPUnit + Codeception

## 📦 Príkazy

```bash
# Databáza
php src/bin/cms migrate     # Vytvoriť tabuľky
php src/bin/cms seed        # Seed testovacích dát

# Queue Worker
php src/bin/queue-worker.php default --tries=5

# Testing
composer test
composer test-coverage

# Code Quality
composer phpstan
composer cs-fix
composer rector
```

## 🔧 Konfigurácia

### Environment Variables

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CMS=data/cms.db
DB_JOBS=data/jobs.db

ADMIN_EMAIL=admin@phpcms.local
ADMIN_PASSWORD=admin123
```

## 📁 Štruktúra

```
nativa-php-boot/
├── src/                   # Všetok kód
│   ├── domain/           # Domain layer
│   ├── application/      # Application layer
│   ├── infrastructure/   # Infrastructure layer
│   ├── interfaces/       # Interfaces layer
│   └── bin/             # CLI scripts
├── public/               # Web root
├── data/                 # SQLite databases
├── storage/             # Logs, cache, uploads
├── docs/                # Documentation
└── tests/               # Tests
```

## 🎯 Features

### Frontend
- ✅ Homepage s najnovšími článkami
- ✅ Article listing
- ✅ Article detail
- ✅ Tag filtering
- ✅ Search
- 🔄 Contact form
- 🔄 Custom forms

### Admin Panel
- ✅ Dashboard
- ✅ Articles CRUD
- 🔄 Pages CRUD
- 🔄 Form Builder
- 🔄 Media Library
- 🔄 Settings

## 🧪 Testing

```bash
# Unit tests
vendor/bin/phpunit tests/Domain

# Integration tests
vendor/bin/phpunit tests/Integration

# Coverage
composer test-coverage
```

## 📄 License

MIT

---

**Pozri aj:**
- [Architecture Journey](docs/ARCHITECTURE_JOURNEY.md) - Ako sme refactorovali appku
- [Quick Reference](docs/QUICK_REFERENCE.md) - Rýchla referenčná príručka
