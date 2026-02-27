# PHP CMS / Blog Platform

Moderný PHP 8.4+ CMS a blog systém s admin panelom a form builderom.

Architektúra identická s Python task-managerom (DDD: Domain, Application, Infrastructure, Interfaces).

## Prehľad

Komplexný publikačný systém pre tvorbu blogov, článkov, statických stránok a vlastných formulárov s intuitívnym admin rozhraním.

## Core Features

### Frontend (Public)
- **Homepage** - hlavná stránka s najnovšími článkami
- **Articles/Blog** - zoznam článkov s kategóriami, tagmi
- **Article Detail** - detail článku
- **Static Pages** - vlastné stránky (About, Contact...)
- **Contact Form** - kontaktný formulár
- **Custom Forms** - dynamické formuláre z adminu

### Admin Panel
- **Dashboard** - štatistiky, prehľad
- **Articles CRUD** - správa článkov
- **Pages CRUD** - správa stránok
- **Form Builder** - tvorba formulárov
- **Form Submissions** - odoslané formuláre
- **Media Library** - upload súborov
- **Users** - správa používateľov
- **Settings** - nastavenia webu

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

## Architecture (DDD - rovnaké ako Python task-manager)

```
php-cms/
├── domain/                 # Domain Layer (biznis logika)
│   ├── Model/             # Entities
│   │   ├── Article.php    # Article entity
│   │   ├── Page.php       # Page entity
│   │   ├── Form.php       # Form entity
│   │   ├── User.php       # User entity
│   │   └── Category.php   # Category entity
│   ├── ValueObjects/      # Value Objects
│   │   ├── Slug.php
│   │   ├── Email.php
│   │   ├── Content.php
│   │   └── Status.php
│   ├── Services/          # Domain Services
│   │   ├── ArticleService.php
│   │   ├── FormService.php
│   │   └── SlugService.php
│   ├── Events/            # Domain Events
│   │   ├── ArticleCreated.php
│   │   ├── ArticlePublished.php
│   │   └── FormSubmitted.php
│   └── Repository/        # Repository interfaces
│       ├── ArticleRepository.php
│       ├── PageRepository.php
│       ├── FormRepository.php
│       └── UserRepository.php
│
├── application/           # Application Layer (use cases)
│   ├── Services/         # Application Services
│   │   ├── ArticleAppService.php
│   │   ├── PageAppService.php
│   │   ├── FormAppService.php
│   │   └── MediaAppService.php
│   ├── DTOs/            # Data Transfer Objects
│   │   ├── ArticleDTO.php
│   │   ├── CreateArticleCommand.php
│   │   ├── PageDTO.php
│   │   └── FormDTO.php
│   └── Exceptions/      # Application Exceptions
│       ├── ArticleNotFoundException.php
│       ├── PageNotFoundException.php
│       └── ValidationException.php
│
├── infrastructure/       # Infrastructure Layer
│   ├── Persistence/     # Database
│   │   ├── DatabaseConnection.php
│   │   ├── UnitOfWork.php
│   │   └── Repositories/
│   │       ├── ArticleRepositoryImpl.php
│   │       ├── PageRepositoryImpl.php
│   │       ├── FormRepositoryImpl.php
│   │       └── UserRepositoryImpl.php
│   ├── Storage/         # File storage
│   │   └── LocalStorage.php
│   └── Messaging/       # Email notifications
│       └── Mailer.php
│
├── interfaces/          # Interfaces Layer
│   ├── HTTP/           # Web controllers
│   │   ├── Frontend/
│   │   │   ├── HomeController.php
│   │   │   ├── ArticleController.php
│   │   │   ├── PageController.php
│   │   │   └── FormController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ArticleController.php
│   │       ├── PageController.php
│   │       ├── FormController.php
│   │       └── MediaController.php
│   ├── CLI/            # Console commands
│   │   └── Commands/
│   └── Templates/      # View templates
│       ├── frontend/
│       │   ├── layouts/
│       │   ├── partials/
│       │   └── pages/
│       └── admin/
│           ├── layouts/
│           └── pages/
│
├── public/             # Web root
│   ├── index.php       # Entry point (router)
│   └── assets/         # CSS, JS, images
│
├── bin/                # CLI scripts
│   └── cms             # Console entry point
│
├── data/               # SQLite database
│   └── cms.db
│
├── storage/            # Uploaded files
│   ├── articles/
│   ├── forms/
│   └── media/
│
├── tests/              # Unit & Integration tests
│
└── composer.json       # Dependencies
```

## Database Schema

```sql
-- Users
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    avatar VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categories
CREATE TABLE categories (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    parent_id VARCHAR(36) REFERENCES categories(id),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Articles
CREATE TABLE articles (
    id VARCHAR(36) PRIMARY KEY,
    author_id VARCHAR(36) REFERENCES users(id),
    category_id VARCHAR(36) REFERENCES categories(id),
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    image VARCHAR(255),
    status VARCHAR(50) DEFAULT 'draft',
    views INTEGER DEFAULT 0,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tags
CREATE TABLE tags (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL
);

-- Article-Tag pivot
CREATE TABLE article_tag (
    article_id VARCHAR(36) REFERENCES articles(id) ON DELETE CASCADE,
    tag_id VARCHAR(36) REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (article_id, tag_id)
);

-- Pages
CREATE TABLE pages (
    id VARCHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    template VARCHAR(100) DEFAULT 'default',
    meta_title VARCHAR(255),
    meta_description TEXT,
    is_published BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Forms (schema stored as JSON)
CREATE TABLE forms (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    schema JSON NOT NULL,
    email_notification VARCHAR(255),
    success_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Form Submissions (data stored as JSON)
CREATE TABLE form_submissions (
    id VARCHAR(36) PRIMARY KEY,
    form_id VARCHAR(36) REFERENCES forms(id) ON DELETE CASCADE,
    data JSON NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Contacts
CREATE TABLE contacts (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Media
CREATE TABLE media (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) REFERENCES users(id),
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    mime_type VARCHAR(100),
    size INTEGER,
    path VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Settings
CREATE TABLE settings (
    id VARCHAR(36) PRIMARY KEY,
    key_name VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(50) DEFAULT 'string',
    group_name VARCHAR(100),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## Routes

### Frontend
```php
GET  /                     // Homepage
GET  /articles             // Article list
GET  /articles/{slug}      // Article detail
GET  /category/{slug}      // Category filter
GET  /tag/{slug}           // Tag filter
GET  /search              // Search
GET  /page/{slug}          // Static page
GET  /contact              // Contact page
POST /contact              // Submit contact form
GET  /form/{slug}          // Custom form
POST /form/{slug}          // Submit custom form
```

### Admin
```php
GET  /admin                        // Dashboard
GET  /admin/articles               // Articles list
POST /admin/articles               // Create article
GET  /admin/articles/{id}/edit     // Edit article
PUT  /admin/articles/{id}          // Update article
DELETE /admin/articles/{id}        // Delete article

GET  /admin/pages                  // Pages list
POST /admin/pages                  // Create page
GET  /admin/pages/{id}/edit        // Edit page

GET  /admin/forms                  // Forms list
POST /admin/forms                  // Create form
GET  /admin/forms/{id}/edit        // Form builder
GET  /admin/forms/{id}/submissions // Submissions

GET  /admin/media                  // Media library
POST /admin/media/upload           // Upload file

GET  /admin/settings               // Settings
PUT  /admin/settings               // Update settings
```

## Development Setup

```bash
# Install dependencies
composer install

# Initialize database
php bin/cms migrate

# Seed test data
php bin/cms seed

# Development server
php -S localhost:8000 -t public
```

## License

MIT

## Current Status (2026-02-27)

### Completed
- ✅ **Native PHP Templates** - Migrované z Twig na vlastný `TemplateRenderer`
  - `TemplateRenderer.php` - renderer s layout support
  - Konvertované `HomeController` a `ArticleController`
  - Vytvorené templates: `base.php`, `home.php`, `articles/index.php`, `articles/show.php`
- ✅ **Twig odstránený** - `composer.json` aktualizovaný, vendor vyčistený
- ✅ **Task Manager** - Projekt "Nativa" kompletný (6/6 taskov)

### Architecture Notes
- **DDD Layers**: Domain → Application → Infrastructure → Interfaces
- **Repository Pattern**: Interfaces in Domain, implementations in Infrastructure
- **UnitOfWork**: Transaction management for database operations
- **Value Objects**: Slug, ArticleStatus, Email for domain validation
- **Custom Router**: Simple regex-based router (yiisoft not used)

### Available Skills
- `task-manager` - Task/project management via SQLite
- `ai-factory` - Project context and skill management
- `architecture` - DDD and software architecture patterns
- `php-scanner` - PHP code analysis
- `security-checklist` - Security audit guidelines
- `sqlite-database-expert` - SQLite database operations and optimization

### MCP Servers Configured (`.qwen/settings.json`)
- **SQLite** - Direct database access to `data/cms.db`
- **Git** - Repository operations for `/home/evan/dev/03/php-cms`
- **Filesystem** - Advanced file operations within project
