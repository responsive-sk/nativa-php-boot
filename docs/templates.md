# Template System Documentation

## Overview

The Nativa CMS template system uses a **layout + partials** architecture for consistent page rendering across the application.

## Directory Structure

```
src/Templates/
├── layouts/              # Master layout templates
│   ├── frontend.php      # Public pages layout (header + footer)
│   └── admin.php         # Admin panel layout
│
├── partials/             # Reusable components
│   ├── layout/           # Layout components
│   │   ├── header.php    # Navigation header
│   │   └── footer.php    # Footer
│   ├── hero/             # Hero sections
│   │   ├── hero-home.php
│   │   └── hero-blog.php
│   ├── nav/              # Navigation components
│   └── components/       # Reusable UI components
│
├── pages/                # Page templates
│   ├── home.php          # Homepage
│   ├── login.php         # Authentication pages
│   ├── register.php
│   ├── form.php          # Dynamic form page
│   │
│   ├── frontend/         # Content pages (dynamic from DB)
│   │   ├── blog.php
│   │   ├── contact.php
│   │   ├── portfolio.php
│   │   ├── docs.php
│   │   ├── about.php
│   │   ├── services.php
│   │   ├── pricing.php
│   │   │
│   │   ├── blog/         # Blog sub-pages
│   │   │   ├── show.php
│   │   │   └── search.php
│   │   │
│   │   └── articles/     # Articles sub-pages
│   │       ├── index.php
│   │       └── show.php
│   │
│   └── admin/            # Admin pages
│       └── dashboard.php
│
└── errors/               # Error pages (no layout)
    ├── 404.php
    └── 500.php
```

## Template Flow

```
Action Controller
    ↓
TemplateRenderer::render(template, data, layout)
    ↓
Layout (layouts/frontend.php)
    ├─ include partials/layout/header.php
    ├─ include partials/hero/hero-{page}.php (conditional)
    ├─ echo $content (yieldContent)
    └─ include partials/layout/footer.php
    ↓
Page Template (pages/{template}.php)
```

## Usage

### In Controllers

```php
// Standard page with layout
$content = $this->renderer->render(
    'pages/frontend/blog',
    [
        'articles'  => $articles,
        'pageTitle' => 'Blog - Nativa CMS',
        'page'      => 'blog',
    ],
    'frontend'  // Layout name
);

return $this->html($content);
```

### Template Variables

All pages receive these common variables:

| Variable | Type | Description |
|----------|------|-------------|
| `$page` | string | Page identifier (home, blog, contact, etc.) |
| `$pageTitle` | string | Page title for `<title>` tag |
| `$isGuest` | bool | User authentication state |
| `$csrfToken` | string | CSRF token for forms |
| `$metaDescription` | string | Meta description for SEO |

### Partials

Partials are reusable components included in layouts or pages:

```php
// In layout
<?php include $this->getTemplatesPath() . '/partials/layout/header.php'; ?>

// In page
<?php include $this->getTemplatesPath() . '/partials/hero/hero-home.php'; ?>
```

### Helper Methods in Templates

```php
$this->e($value)           // Escape HTML
$this->date($date, $format) // Format date (default: 'M d, Y')
$this->nl2br($text)        // Convert newlines to <br>
$this->isEmpty($value)     // Check if value is empty
$this->url($path)          // Generate full URL
```

## Layouts

### frontend.php

Used for all public-facing pages. Includes:
- Header with navigation
- Optional hero section (homepage only)
- Main content area
- Footer

### admin.php

Used for admin panel pages. Includes:
- Admin sidebar
- Admin header
- Main content area

## Pages

### Content Pages (`pages/frontend/`)

Pages that display dynamic content from the database:
- Blog listing
- Article details
- Portfolio items
- Documentation
- Contact form

### Auth Pages (`pages/`)

Authentication-related pages:
- Login
- Register

### Special Pages

- `home.php` - Homepage (special layout treatment)
- `form.php` - Dynamic form renderer
- `errors/*.php` - Error pages (404, 500)

## Best Practices

### 1. Use Layouts Consistently

```php
// Good - uses layout
$this->renderer->render('pages/frontend/blog', $data, 'frontend');

// Avoid - no layout (unless intentional)
$this->renderer->render('pages/frontend/blog', $data, null);
```

### 2. Partials for Reusability

Extract repeated HTML into partials:

```php
// Instead of repeating navigation code
<?php include $this->getTemplatesPath() . '/partials/layout/header.php'; ?>
```

### 3. Escape Output

```php
// Good - escaped
<h1><?php echo $this->e($pageTitle); ?></h1>

// Bad - vulnerable to XSS
<h1><?php echo $pageTitle; ?></h1>
```

### 4. Use Template Variables

```php
// Pass data to template
$this->renderer->render('template', [
    'articles' => $articles,
    'title'    => 'My Page',
], 'frontend');

// Access in template
<?php foreach ($articles as $article): ?>
    <h2><?php echo $this->e($article->title()); ?></h2>
<?php endforeach; ?>
```

## Adding New Pages

### Step 1: Create Template File

```php
// src/Templates/pages/frontend/my-new-page.php
<?php
/**
 * My New Page Template
 *
 * @var string $pageTitle
 * @var string $page
 */
?>

<section class="my-page">
    <h1><?php echo $this->e($pageTitle); ?></h1>
    <!-- Content -->
</section>
```

### Step 2: Create Action Controller

```php
// src/Interfaces/HTTP/Actions/Frontend/MyNewPageAction.php
final class MyNewPageAction extends Action
{
    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'pages/frontend/my-new-page',
            [
                'pageTitle' => 'My New Page',
                'page'      => 'my-new-page',
            ],
            'frontend'
        );

        return $this->html($content);
    }
}
```

### Step 3: Add Route

```php
// src/Interfaces/HTTP/Kernel.php
$this->router->get('/my-new-page', MyNewPageAction::class);
```

## Asset Integration

Assets are managed by Vite and accessed via `AssetHelper`:

```php
// In layout
$cssBundle = AssetHelper::css('css.css');
$appJs     = AssetHelper::js('app.js');

// Page-specific assets
$pageSpecificCss = AssetHelper::pageCss($page);
$pageSpecificJs  = AssetHelper::js($page);
```

## Testing

Test pages render correctly:

```bash
# Start development server
php bin/cms serve

# Test in browser
http://localhost:8000/
http://localhost:8000/blog
http://localhost:8000/contact
```

## Troubleshooting

### Template Not Found

**Error:** `Template not found: /path/to/template.php`

**Solution:**
1. Check template path in controller
2. Verify file exists in `src/Templates/`
3. Ensure correct naming convention

### Layout Not Rendering

**Issue:** Page shows content but no header/footer

**Solution:**
1. Check layout parameter in `render()` call
2. Verify layout file exists in `layouts/`
3. Check `yieldContent()` is called in layout

### Partials Not Loading

**Issue:** Header/footer missing

**Solution:**
1. Verify partial paths in layout
2. Check file permissions
3. Clear template cache if in production

---

**Last Updated:** 2026-03-06  
**Version:** 2.0 (Refactored template system)
