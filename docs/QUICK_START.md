# Quick Reference Guide

## 🚀 Quick Start

```bash
# Install
composer install
cp .env.example .env

# Database
bin/cms migrate
bin/cms seed

# Run
php -S localhost:8000 router.php
```

**Login:** admin@phpcms.local / admin123

---

## 📍 URLs

| Page | URL |
|------|-----|
| Homepage | http://localhost:8000/ |
| Articles | http://localhost:8000/articles |
| Contact | http://localhost:8000/contact |
| Admin | http://localhost:8000/admin |
| Pages List | http://localhost:8000/admin/pages |
| Create Page | http://localhost:8000/admin/pages/create |

---

## 🗂️ File Structure

```
php-cms/
├── src/
│   ├── domain/           # Entities, Value Objects
│   ├── application/      # Services, DTOs
│   ├── infrastructure/   # DB, Queue, Storage
│   └── interfaces/       # Actions, Templates
├── public/              # Web root
├── data/                # SQLite databases
├── storage/             # Uploads, cache, logs
└── docs/                # Documentation
```

---

## 🎯 Common Tasks

### Create a Page

1. Go to `/admin/pages/create`
2. Enter title, content
3. Add SEO settings
4. Click "Create Page"
5. View at `/{slug}`

### Add Content Block

1. Edit page at `/admin/pages/{id}/edit`
2. Click "+ Add Block"
3. Enter type (hero, features, cta)
4. Fill in content
5. Save

### Attach Media

1. Edit page
2. Click "+ Attach Media"
3. Enter media ID
4. Add caption (optional)
5. Save

### Embed Form

1. Edit page
2. Click "+ Embed Form"
3. Enter form ID
4. Choose position
5. Save

---

## 🔧 Commands

```bash
# Database
bin/cms migrate     # Create tables
bin/cms seed        # Seed data

# Server
php -S localhost:8000 router.php

# Testing
composer test
composer test-coverage

# Code Quality
composer phpstan
composer cs-fix
```

---

## 📊 Database Tables

| Table | Purpose |
|-------|---------|
| `articles` | Blog posts |
| `pages` | Static pages |
| `page_blocks` | Content sections |
| `page_media` | Page attachments |
| `page_forms` | Embedded forms |
| `forms` | Form definitions |
| `form_submissions` | Form entries |
| `media` | File uploads |
| `users` | Admin users |
| `outbox` | Event queue |
| `jobs` | Background jobs |

---

## 🎨 Template Variables

### Page Template

```php
<?php
/**
 * @var Page $page
 * @var array $blocks
 * @var array $media
 * @var array $forms
 */
?>

<!-- Page content -->
<h1><?= $page->title() ?></h1>
<?= nl2br($page->content()) ?>

<!-- Blocks -->
<?php foreach ($blocks as $block): ?>
    <section class="block-<?= $block->type() ?>">
        <h2><?= $block->title() ?></h2>
        <?= $block->content() ?>
    </section>
<?php endforeach; ?>

<!-- Media Gallery -->
<?php foreach ($media as $item): ?>
    <img src="<?= $item->url() ?>" alt="<?= $item->caption() ?>" />
<?php endforeach; ?>

<!-- Embedded Forms -->
<?php foreach ($forms as $form): ?>
    <a href="/form/<?= $form->formSlug() ?>">
        <?= $form->title() ?>
    </a>
<?php endforeach; ?>
```

---

## 🐛 Debugging

### Enable Debug Mode

```env
APP_DEBUG=true
```

### View Logs

```bash
tail -f storage/logs/app.log
```

### Check Database

```bash
sqlite3 data/cms.db
SELECT * FROM pages;
```

### Clear Cache

```bash
rm -rf storage/cache/*
```

---

## 📝 Reserved Slugs

Cannot use for pages:
```
admin, articles, contact, form, page
api, storage, assets, public
login, register, dashboard, profile
```

---

## 🔐 Security

### File Uploads

- Max size: 10MB (local), 50MB (cloudinary)
- Allowed: images, videos, PDFs
- Validation: MIME type check

### SQL Injection

- All queries use prepared statements
- No raw SQL with user input

### XSS Prevention

- All output escaped with `htmlspecialchars()`
- Template engine auto-escapes

---

## 📚 Documentation

- [Full Documentation](README.md)
- [AppPaths Usage](APPPATHS_USAGE.md)
- [Actions Pattern](ACTIONS_PATTERN.md)
- [Pages CRUD](PAGES_CRUD_COMPLETE.md)
- [Storage Setup](STORAGE_SETUP.md)

---

*Quick Reference - 2026-02-27*
