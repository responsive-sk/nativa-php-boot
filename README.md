# PHP CMS - Nativa PHP Boot

Moderný PHP 8.4+ CMS a blog systém s **DDD architektúrou** a **Actions pattern**.

## 🚀 Rýchly Štart

```bash
# Inštalácia
composer install
cp .env.example .env

# Databáza
php src/bin/cms migrate
php src/bin/cms seed

# Server
php -S localhost:8000 router.php
```

**Admin:** http://localhost:8000/admin  
**Login:** admin@phpcms.local / admin123

---

## ✨ Features

### Frontend
- ✅ Homepage s najnovšími článkami
- ✅ Articles/Blog s kategóriami a tagmi
- ✅ **Static Pages** s content blocks
- ✅ Contact form
- ✅ Custom Form Builder
- ✅ **Direct URLs** (`/about-us` nie `/page/about-us`)

### Admin Panel
- ✅ Dashboard s štatistikami
- ✅ Articles CRUD
- ✅ **Pages CRUD** s blocks/media/forms
- ✅ Form Builder
- ✅ Media Library
- ✅ Settings

### Architecture
- ✅ **Domain-Driven Design**
- ✅ **Actions Pattern** (nie Controllers)
- ✅ **CQRS** (Command/Query Bus)
- ✅ **Domain Events**
- ✅ **Outbox Pattern**
- ✅ **Saga Pattern**
- ✅ **Native PHP Templates**

---

## 📚 Dokumentácia

- **[Full Documentation](docs/README.md)** - Kompletaná dokumentácia
- **[Quick Start](docs/QUICK_START.md)** - Rýchly sprievodca
- **[AppPaths Usage](docs/APPPATHS_USAGE.md)** - Path management
- **[Actions Pattern](docs/ACTIONS_PATTERN.md)** - Actions vs Controllers
- **[Pages CRUD](docs/PAGES_CRUD_COMPLETE.md)** - Pages návod

---

## 🏗 Architektúra

```
src/
├── domain/                 # Domain Layer (biznis logika)
│   ├── Model/             # Entities (Article, Page, Form)
│   ├── ValueObjects/      # Value Objects (Slug, Email)
│   ├── Repository/        # Repository interfaces
│   └── Events/            # Domain Events
│
├── application/           # Application Layer
│   ├── Services/         # Application Services
│   ├── DTOs/            # Data Transfer Objects
│   ├── CQRS/            # Commands & Queries
│   └── Saga/            # Saga orchestrators
│
├── infrastructure/       # Infrastructure Layer
│   ├── Persistence/     # Database, Repositories
│   ├── Paths/          # AppPaths (path management)
│   ├── Queue/          # Job queue (SQLite)
│   └── Storage/        # File storage (local/cloudinary)
│
└── interfaces/          # Interfaces Layer
    ├── HTTP/
    │   └── Actions/    # Request handlers
    └── Templates/      # Native PHP templates
```

---

## 🛠 Tech Stack

- **PHP:** 8.4+
- **Database:** SQLite (PDO)
- **Templates:** Native PHP (nie Twig)
- **CSS:** TailwindCSS
- **JS:** Alpine.js
- **HTTP:** Symfony HttpFoundation
- **Console:** Symfony Console
- **Testing:** PHPUnit + Codeception

---

## 📋 Príkazy

```bash
# Databáza
php src/bin/cms migrate     # Vytvoriť tabuľky
php src/bin/cms seed        # Seed testovacích dát

# Server
php -S localhost:8000 router.php

# Testing
composer test              # Spustiť testy
composer test-coverage     # Coverage report

# Code Quality
composer phpstan           # Static analysis
composer cs-fix            # Code style
```

---

## 🎯 URL Štruktúra

```
/                           → Homepage
/articles                   → Article list
/articles/{slug}           → Article detail
/contact                   → Contact form
/form/{slug}              → Custom form
/{slug}                   → Static page (NEW!)
  ├── /about
  ├── /services
  └── /pricing

/admin                      → Admin dashboard
/admin/articles            → Articles management
/admin/pages               → Pages management
/admin/forms               → Form builder
/admin/media               → Media library
```

---

## 📖 Príklady

### Vytvoriť Landing Page

1. Otvoriť `/admin/pages/create`
2. Vyplniť title, content
3. Pridať SEO settings
4. Publikovať
5. View na `/{slug}`

### Pridať Content Block

1. Edit page `/admin/pages/{id}/edit`
2. Kliknúť "+ Add Block"
3. Vybrať typ (hero, features, cta)
4. Vyplniť obsah
5. Uložiť

### Embednúť Form

1. Edit page
2. Kliknúť "+ Embed Form"
3. Vybrať form
4. Nastaviť pozíciu (sidebar, content, bottom)
5. Uložiť

---

## 🔐 Bezpečnosť

- ✅ Prepared statements (SQL injection)
- ✅ XSS prevention (auto-escaping)
- ✅ File upload validation
- ✅ Reserved slugs protection

---

## 📄 License

MIT

---

**Vytvorené s ❤️ pre PHP komunitu**
