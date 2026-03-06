# Svelte Layout Integration Plan

**Branch:** `feature/svelte-layout`  
**Created:** 2026-03-06  
**Priority:** Experimental - Frontend Modernization

---

## Goal

Replace current Vite+TypeScript frontend with **Svelte** components while keeping PHP backend intact.

### What Stays
- ✅ PHP backend (DDD architecture)
- ✅ All Actions/Controllers
- ✅ Database & Services
- ✅ API endpoints
- ✅ Admin panel (can stay PHP+Twig)

### What Changes
- 🔄 Frontend templates (PHP → Svelte)
- 🔄 Asset build system (Vite TS → Vite Svelte)
- 🔄 Client-side rendering for public pages
- 🔄 HTMX → Svelte reactivity

---

## Architecture Options

### Option 1: Hybrid Approach (Recommended)
**PHP renders initial HTML + Svelte enhances interactive parts**

```
Layout: PHP template with <div id="app"></div>
Svelte: Mounts on #app, handles interactivity
Routes: PHP routes stay the same
Data: PHP passes data via JSON script tags
```

**Pros:**
- Gradual migration
- SEO friendly (initial HTML from PHP)
- Keep existing PHP templates
- Svelte for interactivity only

**Cons:**
- Two templating systems
- Data duplication (PHP + JS)

### Option 2: Full Svelte SPA
**Svelte handles everything, PHP is API only**

```
Layout: Svelte App
Routes: SvelteKit or vanilla Svelte router
Data: Fetch from PHP API endpoints
```

**Pros:**
- Modern SPA experience
- Single templating system
- Better developer experience

**Cons:**
- SEO challenges (need SSR)
- Bigger migration effort
- Need API for everything

### Option 3: Svelte Islands (Best of Both)
**PHP pages with embedded Svelte components**

```
PHP Template:
  <html>
    <header><?php include 'header.php' ?></header>
    <main>
      <div id="article-list"></div>
      <script type="module">
        import ArticleList from '/assets/ArticleList.svelte';
        new ArticleList({ target: document.getElementById('article-list'), props: {...} });
      </script>
    </main>
  </html>
```

**Pros:**
- Progressive enhancement
- Keep PHP templates
- Svelte where needed
- Best performance

**Cons:**
- More complex setup
- Multiple entry points

---

## Recommended: Option 3 (Svelte Islands)

### Implementation Steps

#### Phase 1: Setup (Tasks 1-3)

**Task 1: Install Svelte + Vite**
```bash
cd src/Templates
pnpm remove typescript
pnpm add -D svelte svelte-preprocess vite-plugin-svelte
```

**Task 2: Configure Vite for Svelte**
```typescript
// vite.config.ts
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default {
  plugins: [svelte()],
  build: {
    rollupOptions: {
      input: {
        'core-init': 'src/init.js',
        'core-app': 'src/app.ts',
        // Svelte components
        'article-list': 'src/components/ArticleList.svelte',
        'contact-form': 'src/components/ContactForm.svelte',
      }
    }
  }
}
```

**Task 3: Create Base Svelte Components**
- `ArticleList.svelte` - Article listing with search
- `ContactForm.svelte` - Form with validation
- `Navigation.svelte` - Responsive nav
- `ThemeToggle.svelte` - Dark/light mode

---

#### Phase 2: Integration (Tasks 4-6)

**Task 4: Update AssetHelper for Svelte**
```php
// Add Svelte component helper
public static function svelteComponent(string $name, array $props = []): string
{
    $js = self::js($name);
    $propsJson = htmlspecialchars(json_encode($props), ENT_QUOTES);
    
    return <<<HTML
    <div id="svelte-{$name}"></div>
    <script type="module">
      import Component from '{$js}';
      new Component({
        target: document.getElementById('svelte-{$name}'),
        props: {$propsJson}
      });
    </script>
    HTML;
}
```

**Task 5: Update PHP Templates**
```php
// Before (pure PHP)
<?php foreach ($articles as $article): ?>
  <article><?= $article->title() ?></article>
<?php endforeach; ?>

// After (Svelte island)
<div id="article-list" data-articles='<?= json_encode($articles) ?>'></div>
<script type="module">
  import ArticleList from '/assets/article-list.js';
  new ArticleList({
    target: document.getElementById('article-list'),
    props: {
      articles: <?= json_encode($articles) ?>
    }
  });
</script>
```

**Task 6: Test All Pages**
- Homepage with ArticleList component
- Blog with search/filter
- Contact form with validation
- Portfolio with filtering

---

#### Phase 3: Enhancements (Tasks 7-9)

**Task 7: Add Svelte Store for Global State**
```typescript
// src/stores/theme.ts
import { writable } from 'svelte/store';

export const theme = writable<'light' | 'dark'>('light');
export const user = writable<User | null>(null);
```

**Task 8: Implement Svelte Transitions**
```svelte
<script>
  import { fade, slide } from 'svelte/transition';
</script>

<article transition:slide>
  {title}
</article>
```

**Task 9: Performance Optimization**
- Code splitting per page
- Lazy loading components
- Preload critical components

---

## File Structure

```
src/Templates/
├── src/
│   ├── components/
│   │   ├── ArticleList.svelte
│   │   ├── ContactForm.svelte
│   │   ├── Navigation.svelte
│   │   ├── ThemeToggle.svelte
│   │   └── ui/
│   │       ├── Button.svelte
│   │       ├── Card.svelte
│   │       └── Modal.svelte
│   ├── stores/
│   │   ├── theme.ts
│   │   └── user.ts
│   ├── lib/
│   │   └── api.ts  # API calls to PHP backend
│   └── app.ts
├── pages/
│   ├── home.php  (PHP with Svelte islands)
│   ├── blog.php
│   └── ...
└── layouts/
    └── frontend.php  (PHP layout with Svelte setup)
```

---

## Example Component

```svelte
<!-- ArticleList.svelte -->
<script lang="ts">
  import { onMount } from 'svelte';
  import { fade } from 'svelte/transition';
  
  export let articles: Article[] = [];
  export let searchQuery: string = '';
  
  let filteredArticles = $derived(
    articles.filter(a => 
      a.title.toLowerCase().includes(searchQuery.toLowerCase())
    )
  );
  
  async function loadMore() {
    // Fetch more articles from PHP API
  }
</script>

<div class="article-list">
  {#each filteredArticles as article (article.id)}
    <article in:fade>
      <h2>{article.title}</h2>
      <p>{article.excerpt}</p>
      <a href="/blog/{article.slug}">Read more</a>
    </article>
  {/each}
  
  <button on:click={loadMore}>Load More</button>
</div>

<style>
  .article-list {
    display: grid;
    gap: 2rem;
  }
  
  article {
    padding: 1.5rem;
    border-radius: 8px;
    background: var(--card-bg);
  }
</style>
```

---

## Migration Path

### Week 1: Setup & Components
- Install Svelte
- Create base components
- Test build system

### Week 2: Integration
- Update homepage
- Update blog listing
- Update contact form

### Week 3: Polish
- Add transitions
- Optimize performance
- Test all pages

### Week 4: Documentation
- Document Svelte patterns
- Create component library
- Update build docs

---

## Success Criteria

- ✅ All pages render correctly
- ✅ Svelte components load without errors
- ✅ No console errors
- ✅ Performance same or better than current
- ✅ SEO preserved (initial HTML from PHP)
- ✅ All interactive features work

---

## Risks & Mitigation

**Risk:** Breaking existing functionality  
**Mitigation:** Keep PHP templates as fallback, gradual rollout

**Risk:** SEO impact  
**Mitigation:** Hybrid approach - PHP renders initial HTML

**Risk:** Bundle size  
**Mitigation:** Code splitting, lazy loading

---

**Estimated Effort:** 2-3 weeks  
**Risk Level:** Medium  
**Recommendation:** Start with Option 3 (Svelte Islands)
