# Svelte Hybrid Integration

## Overview

This directory contains **Svelte components** that enhance PHP templates with interactivity.

## Architecture

```
PHP Template (home-hybrid.php)
    ↓
Renders initial HTML (SEO friendly)
    ↓
<div id="article-list-container" data-articles='[...]'>
    <!-- PHP rendered content -->
</div>
    ↓
Svelte Component (ArticleList.svelte)
    ↓
Mounts on container, adds:
- Live search
- Filtering
- Animations
- Load more
```

## How It Works

### 1. PHP Renders Initial Content

```php
<div id="article-list" data-articles='<?= json_encode($articles) ?>'>
    <?php foreach ($articles as $article): ?>
        <article>
            <h3><?= $article->title() ?></h3>
            <p><?= $article->excerpt() ?></p>
        </article>
    <?php endforeach; ?>
</div>
```

### 2. Svelte Enhances It

```javascript
import ArticleList from '/assets/article-list.js';

const container = document.getElementById('article-list');
const articles = JSON.parse(container.dataset.articles);

new ArticleList({
    target: container,  // Mount on existing element
    props: { articles }
});
```

### 3. Svelte Takes Over

Svelte component:
- Reads existing DOM (optional)
- Adds reactivity
- Adds search/filter
- Adds animations
- Handles user interactions

## File Structure

```
svelte/
├── components/
│   ├── ArticleList.svelte    # Article listing with search
│   ├── ContactForm.svelte    # Form with validation
│   └── ThemeToggle.svelte    # Dark/light mode toggle
├── pages/
│   └── home-hybrid.php       # Example hybrid page
└── README.md                 # This file
```

## Building

### Install Dependencies

```bash
cd src/Templates
pnpm install
```

### Development

```bash
pnpm run dev --config vite-svelte.config.js
```

### Production Build

```bash
pnpm run build --config vite-svelte.config.js
```

Output goes to `public/assets/` with hashed filenames.

## Usage in PHP Templates

### Step 1: Import Svelte Component

```php
<?php
$articleListJs = AssetHelper::js('article-list');
?>
```

### Step 2: Create Container with Data

```php
<div 
    id="article-list"
    data-articles='<?= htmlspecialchars(json_encode($articles)) ?>'
>
    <!-- PHP rendered fallback -->
</div>
```

### Step 3: Initialize Svelte

```html
<script type="module">
    import ArticleList from '<?= $articleListJs ?>';
    
    const container = document.getElementById('article-list');
    const articles = JSON.parse(container.dataset.articles);
    
    new ArticleList({
        target: container,
        props: { articles }
    });
</script>
```

## Benefits

### SEO
✅ PHP renders initial HTML  
✅ Search engines can crawl content  
✅ No JavaScript required for basic content  

### Performance
✅ Progressive enhancement  
✅ Works without JS  
✅ Fast initial page load  

### User Experience
✅ Rich interactivity with Svelte  
✅ Live search and filtering  
✅ Smooth animations  
✅ Form validation  

### Developer Experience
✅ Keep PHP templates  
✅ Gradual migration  
✅ Best of both worlds  

## Components

### ArticleList.svelte

**Props:**
- `articles: Article[]` - Array of articles
- `searchQuery: string` - Current search query

**Features:**
- Live search filtering
- Tag display
- Responsive grid
- Empty state handling

### ContactForm.svelte

**Props:**
- `actionUrl: string` - Form submission URL
- `csrfToken: string` - CSRF token

**Features:**
- Client-side validation
- AJAX submission
- Success/error states
- Loading spinner

### ThemeToggle.svelte

**Props:**
- None

**Features:**
- Dark/light mode toggle
- Persists to localStorage
- System preference detection

## Migration Guide

### From Pure PHP

**Before:**
```php
<?php foreach ($articles as $article): ?>
    <article><?= $article->title() ?></article>
<?php endforeach; ?>
```

**After:**
```php
<div id="article-list" data-articles='<?= json_encode($articles) ?>'>
    <?php foreach ($articles as $article): ?>
        <article><?= $article->title() ?></article>
    <?php endforeach; ?>
</div>

<script type="module">
    import ArticleList from '/assets/article-list.js';
    new ArticleList({
        target: document.getElementById('article-list'),
        props: { articles: <?= json_encode($articles) ?> }
    });
</script>
```

## Troubleshooting

### Svelte component not loading
- Check manifest.json exists in `public/assets/`
- Verify AssetHelper::js() returns correct path
- Check browser console for errors

### Data not passed to Svelte
- Ensure `data-*` attributes are properly JSON encoded
- Use `htmlspecialchars()` to prevent XSS
- Check JSON is valid with `JSON.parse()`

### Styles not applying
- Svelte scopes styles automatically
- Use CSS variables for theming
- Check specificity conflicts

## Next Steps

1. ✅ Create Svelte components
2. ✅ Create hybrid PHP template
3. ⏳ Build and test
4. ⏳ Add more components (Navigation, Footer)
5. ⏳ Implement page transitions
6. ⏳ Add Svelte stores for global state

---

**Status:** Experimental  
**Last Updated:** 2026-03-06
