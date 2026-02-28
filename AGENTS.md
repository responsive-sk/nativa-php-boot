# AGENTS.md

> Project map for AI agents. Keep this file up-to-date as the project evolves.

## Project Overview

Modern PHP 8.4+ CMS and Blog Platform with admin panel, built using Domain-Driven Design (DDD) architecture. Features include article management, static pages, custom form builder, media library, and user management.

## Tech Stack

- **Language:** PHP 8.4+
- **Database:** SQLite (PDO)
- **Template Engine:** Native PHP templates (TemplateRenderer)
- **Frontend:** TailwindCSS + Alpine.js
- **Testing:** PHPUnit + Codeception (C3 coverage)
- **Code Quality:** PHPStan, PHP CS Fixer, Rector
- **Router:** Custom implementation (Interfaces\HTTP\Router)
- **HTTP Foundation:** Symfony HttpFoundation
- **Console:** Symfony Console

## Project Structure

```
nativa/
├── src/                      # Source code (DDD layers)
│   ├── domain/              # Pure business logic (entities, value objects, repository interfaces)
│   ├── application/         # Use cases and application services (DTOs, exceptions)
│   ├── infrastructure/      # External concerns (database, storage, messaging)
│   └── interfaces/          # Adapters (HTTP controllers, CLI, templates)
│
├── public/                   # Web root
│   ├── index.php            # Application entry point
│   └── assets/              # CSS, JS, images
│
├── bin/                      # CLI scripts
│   └── cms                  # Console entry point (migrate, seed, serve)
│
├── tests/                    # Unit & Integration tests
│   ├── Domain/              # Domain layer tests
│   ├── Application/         # Application layer tests
│   ├── Infrastructure/      # Infrastructure layer tests
│   └── Integration/         # Integration tests
│
├── data/                     # SQLite database storage
│   └── cms.db
│
├── storage/                  # Uploaded files
│   ├── articles/
│   ├── forms/
│   └── media/
│
├── .ai-factory/             # AI context files
│   ├── DESCRIPTION.md       # Project specification
│   └── ARCHITECTURE.md      # Architecture guidelines
│
├── docs/                     # Documentation
├── vendor/                   # Composer dependencies
└── .qwen/                    # AI skills and configuration
```

## Key Entry Points

| File | Purpose |
|------|---------|
| `public/index.php` | Application entry point, bootstrap and request handling |
| `src/interfaces/HTTP/Kernel.php` | HTTP kernel, route registration, request handling |
| `bin/cms` | CLI console entry point (migrate, seed, serve commands) |
| `composer.json` | Dependencies, PSR-4 autoload, scripts |
| `.ai-factory/DESCRIPTION.md` | Project specification and tech stack |
| `.ai-factory/ARCHITECTURE.md` | DDD architecture guidelines and code examples |

## Documentation

| Document | Path | Description |
|----------|------|-------------|
| README | README.md | Project landing page |
| CHANGELOG | CHANGELOG.md | Version history and changes |
| Project Spec | .ai-factory/DESCRIPTION.md | Detailed project specification |
| Architecture | .ai-factory/ARCHITECTURE.md | DDD architecture guidelines |

## AI Context Files

| File | Purpose |
|------|---------|
| AGENTS.md | This file — project structure map |
| .ai-factory/DESCRIPTION.md | Project specification and tech stack |
| .ai-factory/ARCHITECTURE.md | Architecture decisions and guidelines |
| QWEN.md | Agent instructions and preferences |

## Development Commands

```bash
# Install dependencies
composer install

# Run database migrations
php bin/cms migrate

# Seed test data
php bin/cms seed

# Start development server
php bin/cms serve
# Or: php -S localhost:8000 -t public

# Run tests
composer test

# Run all code quality checks
composer analyze

# Code style fix
composer cs-fix
```

## Default Admin Credentials

- **Email:** admin@phpcms.local
- **Password:** admin123

## Access Points

- **Frontend:** http://localhost:8000
- **Admin:** http://localhost:8000/admin
