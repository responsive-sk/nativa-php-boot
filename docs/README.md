# PHP CMS - Complete Documentation

## 📖 Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture](#architecture)
3. [Features](#features)
4. [Getting Started](#getting-started)
5. [Admin Panel](#admin-panel)
6. [Frontend](#frontend)
7. [Database Schema](#database-schema)
8. [API Reference](#api-reference)

---

## Project Overview

**PHP CMS** is a modern, flat-file content management system built with PHP 8.4+ using Domain-Driven Design (DDD) architecture.

### Tech Stack

- **Language:** PHP 8.4+
- **Database:** SQLite (PDO)
- **Frontend:** TailwindCSS + Alpine.js
- **HTTP:** Symfony HttpFoundation
- **Console:** Symfony Console
- **Testing:** PHPUnit + Codeception

### Key Features

- ✅ **Articles/Blog** - Full blog system with categories and tags
- ✅ **Static Pages** - CMS pages with content blocks
- ✅ **Form Builder** - Dynamic form creation
- ✅ **Media Library** - File upload and management
- ✅ **Contact Forms** - Built-in contact functionality
- ✅ **Native PHP Templates** - No Twig dependency
- ✅ **CQRS Pattern** - Command/Query separation
- ✅ **Domain Events** - Event-driven architecture
- ✅ **Outbox Pattern** - Reliable event publishing
- ✅ **Saga Pattern** - Distributed transactions

---

## Architecture

### DDD Layer Structure

```
src/
├── domain/                 # Domain Layer (business logic)
│   ├── Model/             # Entities
│   ├── ValueObjects/      # Value Objects
│   ├── Repository/        # Repository interfaces
│   ├── Services/          # Domain Services
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
│   ├── Paths/          # Path management (AppPaths)
│   ├── Queue/          # Job queue
│   ├── Storage/        # File storage providers
│   └── Container/      # DI Container
│
└── interfaces/          # Interfaces Layer
    ├── HTTP/
    │   ├── Actions/    # Request handlers
    │   └── View/       # Template renderer
    └── Templates/      # View templates
```

### Design Patterns

| Pattern | Usage |
|---------|-------|
| **Repository** | Data access abstraction |
| **Unit of Work** | Transaction management |
| **Domain Events** | Decoupled communication |
| **CQRS** | Read/Write separation |
| **Outbox** | Reliable event publishing |
| **Saga** | Distributed transactions |
| **Strategy** | Storage providers (local/cloudinary) |
| **Actions** | Request handling (vs Controllers) |

---

## Features

### 1. Articles/Blog

**Features:**
- Create, edit, delete articles
- Publish/unpublish workflow
- Categories and tags
- Search functionality
- Draft/Published status

**Routes:**
```
GET  /articles              # List all articles
GET  /articles/{slug}      # Article detail
GET  /tag/{slug}           # Filter by tag
GET  /search?q=...         # Search articles
```

### 2. Static Pages

**Features:**
- Create custom pages (About, Services, etc.)
- Content blocks (hero, features, CTA, testimonials)
- Media gallery
- Embedded forms
- SEO settings (meta title/description)
- Direct URL access (`/about-us` not `/page/about-us`)

**Admin Routes:**
```
GET  /admin/pages          # List pages
GET  /admin/pages/create   # Create page
GET  /admin/pages/{id}/edit  # Edit page
POST /admin/pages/{id}/edit  # Update page
DELETE /admin/pages/{id}   # Delete page
```

**Frontend Routes:**
```
GET  /{slug}              # Page display (catch-all)
```

**Reserved Slugs:**
```
admin, articles, article, tags, tag, search
contact, form, forms, page, pages
api, storage, static, assets, public
login, logout, register, signup, signin
dashboard, profile, settings, account
```

### 3. Content Blocks

**Block Types:**
- `hero` - Hero section with title + CTA
- `features` - Feature grid
- `cta` - Call-to-action section
- `text_image` - Text + Image layout
- `testimonials` - Testimonials carousel

**Block Structure:**
```php
PageBlock {
    id: string
    pageId: string
    type: string (hero, features, cta, etc.)
    title: ?string
    content: ?string
    data: array (JSON)
    sortOrder: int
    isActive: bool
}
```

### 4. Form Builder

**Features:**
- Create custom forms with dynamic fields
- Field types: text, email, textarea, select, checkbox
- Email notifications
- Form submissions storage
- Embed forms on pages

**Field Types:**
```
- Text input
- Email input
- Textarea
- Select dropdown
- Checkbox
```

**Routes:**
```
GET  /form/{slug}          # Display form
POST /form/{slug}          # Submit form
```

### 5. Media Library

**Features:**
- Upload images, videos, PDFs
- Local storage or Cloudinary
- Duplicate detection (SHA256 hash)
- Attach to pages
- Gallery display

**Storage Providers:**
- `local` - File system storage
- `cloudinary` - Cloud storage API

**Configuration (.env):**
```env
MEDIA_PROVIDER=local
STORAGE_PATH=storage/uploads
STORAGE_URL=/storage/uploads

# Cloudinary (optional)
CLOUDINARY_CLOUD_NAME=your-cloud
CLOUDINARY_API_KEY=key
CLOUDINARY_API_SECRET=secret
```

### 6. Contact Forms

**Features:**
- Built-in contact form
- Email notifications via queue
- Spam protection
- Auto-reply option

---

## Getting Started

### Installation

```bash
# Clone repository
git clone https://github.com/responsive-sk/nativa-php-boot.git
cd nativa-php-boot

# Install dependencies
composer install

# Create .env file
cp .env.example .env

# Run migrations
php src/bin/cms migrate

# Seed test data
php src/bin/cms seed

# Start development server
php -S localhost:8000 router.php
```

### Default Admin Credentials

```
Email: admin@phpcms.local
Password: admin123
```

### Development Commands

```bash
# Run tests
composer test

# Run with coverage
composer test-coverage

# Static analysis
composer phpstan

# Code style
composer cs-fix
```

---

## Admin Panel

### Dashboard

**URL:** `/admin`

**Features:**
- Statistics overview
- Quick actions (create article, page, form)
- Recent activity

### Articles Management

**URL:** `/admin/articles`

**Actions:**
- List all articles
- Create new article
- Edit existing article
- Publish/unpublish
- Delete article

### Pages Management

**URL:** `/admin/pages`

**Actions:**
- List all pages
- Create new page
- Edit page (with blocks/media/forms)
- Delete page

**Edit Page Features:**
- Content blocks management
- Media gallery attachment
- Form embedding
- SEO settings

### Forms Management

**URL:** `/admin/forms`

**Actions:**
- List all forms
- Create form with builder
- Edit form fields
- View submissions

### Media Library

**URL:** `/admin/media`

**Actions:**
- Upload files
- View gallery
- Delete media
- Attach to pages

---

## Frontend

### URL Structure

```
/                           → Homepage
/articles                   → Article listing
/articles/{slug}           → Article detail
/contact                   → Contact form
/form/{slug}              → Custom form
/{slug}                   → Static page
```

### Template Structure

```
src/interfaces/Templates/
├── frontend/
│   ├── layouts/
│   │   └── base.php          # Base layout
│   └── pages/
│       ├── home.php          # Homepage
│       ├── articles/
│       │   ├── index.php     # Article list
│       │   └── show.php      # Article detail
│       ├── contact.php       # Contact form
│       ├── form.php          # Custom form
│       └── default.php       # Static page template
│
└── admin/
    ├── layouts/
    │   └── base.php          # Admin layout
    └── pages/
        ├── dashboard.php
        ├── articles/
        ├── pages/
        ├── forms/
        └── media/
```

---

## Database Schema

### Core Tables

```sql
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

-- Page Blocks
CREATE TABLE page_blocks (
    id VARCHAR(36) PRIMARY KEY,
    page_id VARCHAR(36) REFERENCES pages(id) ON DELETE CASCADE,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255),
    content TEXT,
    data JSON,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Page Media
CREATE TABLE page_media (
    id VARCHAR(36) PRIMARY KEY,
    page_id VARCHAR(36) REFERENCES pages(id) ON DELETE CASCADE,
    media_id VARCHAR(36) REFERENCES media(id) ON DELETE CASCADE,
    caption VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Page Forms
CREATE TABLE page_forms (
    id VARCHAR(36) PRIMARY KEY,
    page_id VARCHAR(36) REFERENCES pages(id) ON DELETE CASCADE,
    form_id VARCHAR(36) REFERENCES forms(id) ON DELETE CASCADE,
    title VARCHAR(255),
    position VARCHAR(50) DEFAULT 'sidebar',
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Forms
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

-- Form Submissions
CREATE TABLE form_submissions (
    id VARCHAR(36) PRIMARY KEY,
    form_id VARCHAR(36) REFERENCES forms(id) ON DELETE CASCADE,
    data JSON NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
    url VARCHAR(512) NOT NULL,
    provider VARCHAR(50) DEFAULT 'local',
    hash VARCHAR(64),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

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
```

---

## API Reference

### Pages API

**List Pages**
```
GET /admin/pages
Response: array<Page>
```

**Create Page**
```
POST /admin/pages
Body: {
    title: string
    content: string
    template?: string
    metaTitle?: string
    metaDescription?: string
    isPublished?: boolean
}
Response: { success: boolean, page: Page }
```

**Update Page**
```
POST /admin/pages/{id}/edit
Body: {
    title?: string
    content?: string
    template?: string
    metaTitle?: string
    metaDescription?: string
}
Response: { success: boolean, page: Page }
```

**Add Content Block**
```
POST /admin/pages/{id}/edit
Body: {
    _action: "add_block"
    type: string (hero, features, cta, etc.)
    title?: string
    content?: string
    sortOrder?: int
}
Response: { success: boolean }
```

**Attach Media**
```
POST /admin/pages/{id}/edit
Body: {
    _action: "attach_media"
    mediaId: string
    caption?: string
}
Response: { success: boolean }
```

**Embed Form**
```
POST /admin/pages/{id}/edit
Body: {
    _action: "embed_form"
    formId: string
    title?: string
    position?: string (sidebar, content, bottom)
}
Response: { success: boolean }
```

---

## Configuration

### Environment Variables (.env)

```env
# Application
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
CACHE_DRIVER=file
TEMPLATE_CACHE_VERSION=

# Media Storage
MEDIA_PROVIDER=local
STORAGE_PATH=storage/uploads
STORAGE_URL=/storage/uploads

# Cloudinary (optional)
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_UPLOAD_PRESET=
```

---

## Development Guidelines

### Code Style

- **PSR-12** coding standards
- **Strict types** (`declare(strict_types=1)`)
- **Type hints** for all methods
- **PHPDoc** for complex methods

### Testing

```bash
# Unit tests
vendor/bin/phpunit tests/Domain

# Integration tests
vendor/bin/phpunit tests/Integration

# Coverage report
composer test-coverage
```

### Git Workflow

```bash
# Create feature branch
git checkout -b feature/your-feature

# Commit changes
git commit -m "feat: add your feature"

# Push and create PR
git push origin feature/your-feature
```

---

## Troubleshooting

### Common Issues

**1. Pages not displaying**
- Check if page is published (`is_published = TRUE`)
- Verify slug doesn't conflict with reserved slugs
- Clear template cache (`storage/cache/templates/`)

**2. Media not loading**
- Check `storage/uploads/` directory permissions
- Verify symlink exists (`public/storage → storage/uploads`)
- Check file exists in database

**3. Forms not submitting**
- Check form is published
- Verify form slug is correct
- Check email notification settings

---

## Contributing

1. Fork the repository
2. Create feature branch
3. Make changes
4. Run tests
5. Submit pull request

---

## License

MIT License - See LICENSE file for details

---

*Last updated: 2026-02-27*
