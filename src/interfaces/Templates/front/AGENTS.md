# Templates - Generic View System

Abstract, reusable view templates for the application.

## IMPORTANT: NO EMOJIS

DO NOT USE EMOJIS IN THIS PROJECT. This includes:

- Code comments
- Layouts and templates
- Any user-facing text

## Contents

1. [Quick Start](#quick-start)
2. [Structure](#structure)
3. [Adding a New Page](#adding-a-new-page)
4. [CSS Architecture](#css-architecture)
5. [Page-specific CSS](#page-specific-css)
6. [Design Tokens](#design-tokens)
7. [Error Handling](#error-handling)

---

## Quick Start

```bash
# Build assets
cd views/templates && pnpm run build

# Start dev server
cd ../.. && APP_ENV=dev php -S localhost:8008 -t public
```

---

## Structure

```
views/templates/
├── src/                        # TS/JS/CSS source (Vite)
│   ├── app.css                 # Main CSS entry (base + shared)
│   ├── app.ts                  # Main JS entry
│   ├── css.ts                 # Vite entry point
│   ├── components/              # Vanilla JS components
│   ├── shared/                 # Reusable (all pages)
│   │   └── sections/          # Sections: hero, cta, footer...
│   ├── styles/                 # Base styles
│   │   ├── components/         # Component CSS (BEM)
│   │   ├── shared/             # Shared styles
│   │   └── use-cases/         # Page-specific CSS
│   ├── utils/                  # JS utilities (theme, mobile menu, nav)
│   ├── types/                  # TypeScript definitions
│   ├── assets/                 # Static assets
│   └── vendors/                # Third-party code
├── pages/                      # PHP page templates
│   ├── home.php
│   ├── services.php
│   ├── pricing.php
│   └── errors/
│       ├── 404.php
│       └── 500.php
├── layouts/
│   └── home.php                # Main layout
└── docs/                       # Documentation
    ├── MIGRATION.md
    ├── MAINTENANCE.md
    ├── PERFORMANCE.md
    └── DESIGN_TOKENS.md
```

---

## Asset Paths

All assets are in:

- `/assets/app.css` - main CSS
- `/assets/app.js` - main JS
- `/assets/use-cases/X.css` - page-specific CSS

---

## Adding a New Page

### Step 1: Create Action

```php
// src/UseCase/PageName/Action.php
declare(strict_types=1);

namespace App\UseCase\PageName;

use App\Infrastructure\View\SimpleRenderer;
use Yiisoft\DataResponse\DataResponse;

final readonly class Action
{
    public function __construct(
        private SimpleRenderer $simpleRenderer,
    ) {}

    public function __invoke(): DataResponse
    {
        return $this->simpleRenderer->render('templates/pages/page-name', ['page' => 'page-name'], 'templates/layouts/home');
    }
}
```

### Step 2: Add Route

```php
// config/site/routes.php
use App\UseCase\PageName\Action;

Route::get('/page-name')
    ->action(Action::class)
    ->name('page-name'),
```

### Step 3: Create PHP Template

```php
<!-- views/templates/pages/page-name.php -->
<?php declare(strict_types=1); ?>

<section class="page-name">
  <div class="page-name__hero">
    <h2>Page Title</h2>
  </div>
</section>
```

### Step 4: Optional - Use Helpers

SimpleRenderer provides helper methods:

```php
<?php
// URL generation
$url = $this->url('route-name', ['id' => 1]);

// Escape HTML
$escaped = $this->escapeHtml($userInput);
?>
```

---

## CSS Architecture

### Design Tokens

All values are defined as CSS custom properties in `app.css`:

```css
:root {
    /* Brand (fixed) */
    --brand-gold: #d4af37;
    --brand-gold-light: #e5c559;
    --brand-gold-dark: #b8941f;

    /* Theme (flip) */
    --color-bg: #0a0a0a;
    --color-bg-alt: #1a1a1a;
    --color-text: #ffffff;

    /* Typography */
    --font-sans: "Inter", system-ui, sans-serif;
    --font-serif: "Playfair Display", Georgia, serif;

    /* Spacing */
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 16px;
    --space-4: 24px;
    --space-5: 32px;
    --space-6: 48px;

    /* Border radius */
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;

    /* Shadows */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
    --shadow-gold: 0 4px 14px rgba(212, 175, 55, 0.3);
}
```

### Light Theme

```css
[data-theme="light"] {
    --color-bg: #ffffff;
    --color-bg-alt: #f8fafc;
    --color-text: #1e293b;
    --color-border: #e2e8f0;
}
```

### Rules

| Folder         | Content                                    | When to Load   |
| -------------- | ------------------------------------------ | -------------- |
| `app.css`      | Base: tokens, reset, utilities, components | Always         |
| `use-cases/X/` | Page-specific                              | Only on page X |

### Benefits

- First paint - only CSS the page needs
- Design tokens - consistent values
- Isolation - no side effects
- Small bundle - Vite automatic chunking

---

## Page-specific CSS

### Build Output

```
public/assets/
├── app.css              # ~16KB (base + shared)
├── app.js               # ~4KB
└── use-cases/
    ├── home.css         # ~0KB (empty)
    ├── services.css     # ~4.5KB
    ├── pricing.css      # ~4.5KB
    ├── contact.css      # ~2KB
    └── not-found.css    # ~1KB
```

### Usage in Layout

```php
<!-- views/templates/layouts/home.php -->
<head>
  <link rel="stylesheet" href="/assets/app.css">

  <?php if ($pageCss): ?>
  <link rel="stylesheet" href="/assets/<?= $pageCss ?>">
  <?php endif; ?>
</head>
```

---

## Error Handling

### 404 Page

- Routed via `NotFoundHandler`
- Template: `views/templates/pages/errors/404.php`
- CSS: `use-cases/not-found.css`

### 500 Page

- Custom `ThrowableResponseFactory` automatically wraps template into full HTML layout
- Template: `views/templates/pages/errors/500.php`
- Implementation:
    - `src/Infrastructure/ErrorHandler/CustomThrowableResponseFactory.php`
    - Registration in `src/Infrastructure/ServiceProvider.php`:
        ```php
        ThrowableResponseFactoryInterface::class => static fn(
            ResponseFactoryInterface $responseFactory
        ) => new CustomThrowableResponseFactory($responseFactory),
        ```

---

## BEM Conventions

```html
<article class="offering-card offering-card--featured">
    <h3 class="offering-card__title">Title</h3>
    <p class="offering-card__description">...</p>
</article>
```

```css
.offering-card {
}
.offering-card__title {
}
.offering-card--featured {
}
```

---

## Lighthouse / A11y

- No `user-scalable=no`
- `maximum-scale >= 5`
- Semantic headings
- Local fonts (no Google Fonts)
