# TypeScript Best Practices Guide

**Pre:** Frontend developers working with PHP CMS Templates

---

## 📚 Table of Contents

1. [Generated Types](#generated-types)
2. [Using Types in Components](#using-types-in-components)
3. [Form Handling](#form-handling)
4. [HTTP Requests](#http-requests)
5. [Error Handling](#error-handling)
6. [Code Organization](#code-organization)

---

## 🎯 Generated Types

### Location
```
Templates/src/types/generated/
├── Article.ts
├── User.ts
├── Role.ts
├── ArticleStatus.ts
└── index.ts  ← Barrel export
```

### Import Types

```typescript
// ✅ DO: Use barrel export
import { Article, User, Role, ArticleStatus } from '@types/generated';

// ❌ DON'T: Import from specific files
import { Article } from '../types/generated/Article';
```

### Type Guards

```typescript
import { Article, isArticle } from '@types/generated';

// API response validation
async function fetchArticle(id: string): Promise<Article> {
  const response = await fetch(`/api/articles/${id}`);
  const data = await response.json();
  
  if (!isArticle(data)) {
    throw new Error('Invalid article data');
  }
  
  return data;
}

// Usage
const article = await fetchArticle('123');
console.log(article.title); // ✅ Type-safe!
```

---

## 🧩 Using Types in Components

### Alpine.js Components

```html
<!-- Templates/src/frontend/pages/blog.php -->
<div x-data="{ 
  articles: [] as Article[],
  async loadArticles() {
    const response = await fetch('/api/articles');
    const data = await response.json();
    this.articles = data.filter(isArticle);
  }
}">
  <template x-for="article in articles" :key="article.id">
    <div>
      <h3 x-text="article.title"></h3>
      <span x-text="article.status"></span>
    </div>
  </template>
</div>

<script>
import { Article, isArticle } from '@types/generated';
</script>
```

### TypeScript Components

```typescript
// Templates/src/components/ArticleCard.ts
import { Article, ArticleStatus } from '@types/generated';

interface ArticleCardProps {
  article: Article;
  showExcerpt?: boolean;
  showStatus?: boolean;
}

export function ArticleCard({ 
  article, 
  showExcerpt = true, 
  showStatus = false 
}: ArticleCardProps): string {
  return `
    <article class="article-card">
      <h2>${escapeHtml(article.title)}</h2>
      
      ${showStatus ? `
        <span class="status status--${article.status}">
          ${article.status}
        </span>
      ` : ''}
      
      ${showExcerpt && article.excerpt ? `
        <p class="excerpt">${escapeHtml(article.excerpt)}</p>
      ` : ''}
      
      <a href="/blog/${article.slug}">Read more →</a>
    </article>
  `;
}

function escapeHtml(text: string): string {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
```

---

## 📝 Form Handling

### Simple Form with Validation

```typescript
// Templates/src/forms/contact.form.ts
import { validate, required, email, minLength } from '@forms/validator';

interface ContactFormData {
  name: string;
  email: string;
  message: string;
}

const rules: Partial<Record<keyof ContactFormData, (v: unknown) => string | null>> = {
  name: required,
  email: email,
  message: minLength(10),
};

// HTMX form handling
document.querySelector('#contact-form')?.addEventListener('htmx:validation:validate', function(evt) {
  const formData = new FormData(this as HTMLFormElement);
  const data = Object.fromEntries(formData) as unknown as ContactFormData;
  
  const errors = validate(data, rules);
  
  if (Object.keys(errors).length > 0) {
    evt.preventDefault();
    evt.detail.issue = Object.values(errors)[0];
  }
});
```

### Form State with Alpine

```html
<form 
  x-data="{
    errors: {} as Partial<Record<keyof ContactFormData, string>>,
    submitting: false,
    
    validate(field: keyof ContactFormData, value: string) {
      const rule = rules[field];
      if (rule) {
        this.errors[field] = rule(value) ?? undefined;
      }
    },
    
    get isValid() {
      return Object.keys(this.errors).length === 0;
    }
  }"
  hx-post="/api/contact"
  hx-indicator=".htmx-indicator"
>
  <input 
    name="name" 
    type="text"
    @blur="validate('name', $el.value)"
    :class="{ 'error': errors.name }"
  >
  <span x-show="errors.name" x-text="errors.name"></span>
  
  <input 
    name="email" 
    type="email"
    @blur="validate('email', $el.value)"
    :class="{ 'error': errors.email }"
  >
  <span x-show="errors.email" x-text="errors.email"></span>
  
  <textarea 
    name="message"
    @blur="validate('message', $el.value)"
    :class="{ 'error': errors.message }"
  ></textarea>
  <span x-show="errors.message" x-text="errors.message"></span>
  
  <button type="submit" :disabled="!isValid || submitting">
    Send Message
  </button>
  
  <div class="htmx-indicator">Sending...</div>
</form>
```

---

## 🌐 HTTP Requests

### Simple HTTP Helper

```typescript
// Templates/src/core/http.ts
export interface HttpError extends Error {
  status: number;
  statusText: string;
}

export async function http<T>(
  url: string,
  options: RequestInit = {}
): Promise<T> {
  const defaultOptions: RequestInit = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  };

  const response = await fetch(url, { ...defaultOptions, ...options });

  if (!response.ok) {
    const error: HttpError = new Error(
      `HTTP ${response.status}: ${response.statusText}`
    ) as HttpError;
    error.status = response.status;
    error.statusText = response.statusText;
    throw error;
  }

  return response.json();
}

// Convenience methods
export const api = {
  get: <T>(url: string) => http<T>(url, { method: 'GET' }),
  post: <T>(url: string, data: unknown) => 
    http<T>(url, { method: 'POST', body: JSON.stringify(data) }),
  put: <T>(url: string, data: unknown) => 
    http<T>(url, { method: 'PUT', body: JSON.stringify(data) }),
  delete: <T>(url: string) => http<T>(url, { method: 'DELETE' }),
};
```

### Usage Examples

```typescript
import { api } from '@core/http';
import { Article, ArticleStatus } from '@types/generated';

// Fetch articles
const articles = await api.get<Article[]>('/api/articles');

// Fetch single article
const article = await api.get<Article>(`/api/articles/${articleId}`);

// Create article
const newArticle = await api.post<Article>('/api/articles', {
  title: 'My New Post',
  content: 'Content here...',
  status: ArticleStatus.DRAFT,
});

// Update article
const updated = await api.put<Article>(
  `/api/articles/${articleId}`,
  { title: 'Updated Title' }
);

// Delete article
await api.delete(`/api/articles/${articleId}`);
```

---

## ⚠️ Error Handling

### Global Error Handler

```typescript
// Templates/src/core/error-handler.ts
import { HttpError } from '@core/http';

export function handleError(error: unknown): void {
  if (error instanceof HttpError) {
    switch (error.status) {
      case 401:
        showNotification('Please log in to continue', 'error');
        window.location.href = '/login';
        break;
      case 403:
        showNotification('You do not have permission to do this', 'error');
        break;
      case 404:
        showNotification('Resource not found', 'error');
        break;
      case 500:
        showNotification('Something went wrong. Please try again.', 'error');
        break;
      default:
        showNotification(error.message, 'error');
    }
  } else if (error instanceof Error) {
    showNotification(error.message, 'error');
  } else {
    showNotification('An unexpected error occurred', 'error');
  }
}

function showNotification(message: string, type: 'success' | 'error'): void {
  // Implementation depends on your notification system
  console.log(`[${type.toUpperCase()}] ${message}`);
}
```

### Try-Catch Pattern

```typescript
import { api } from '@core/http';
import { handleError } from '@core/error-handler';
import { Article } from '@types/generated';

async function loadArticles(): Promise<Article[]> {
  try {
    return await api.get<Article[]>('/api/articles');
  } catch (error) {
    handleError(error);
    return [];
  }
}

// Usage in Alpine component
document.addEventListener('alpine:init', () => {
  Alpine.data('blogPage', () => ({
    articles: [] as Article[],
    loading: true,
    
    async init() {
      try {
        this.articles = await loadArticles();
      } catch (error) {
        // Error already handled by handleError
      } finally {
        this.loading = false;
      }
    }
  }));
});
```

---

## 📁 Code Organization

### Recommended Structure

```
Templates/src/
├── components/           # Reusable UI components
│   ├── ArticleCard.ts
│   ├── UserAvatar.ts
│   └── index.ts
│
├── core/                 # Core utilities
│   ├── http.ts
│   ├── error-handler.ts
│   └── utils.ts
│
├── forms/                # Form helpers
│   ├── validator.ts
│   └── validators/
│       ├── required.ts
│       ├── email.ts
│       └── index.ts
│
├── types/
│   ├── generated/        # ⚠️ AUTO-GENERATED
│   │   ├── Article.ts
│   │   └── index.ts
│   └── manual/           # Manual types
│       ├── ui.ts
│       └── utils.ts
│
├── frontend/             # Frontend-specific code
│   ├── pages/
│   │   ├── home.ts
│   │   ├── blog.ts
│   │   └── contact.ts
│   └── use-cases/
│       └── ...
│
└── admin/                # Admin-specific code (future)
    └── ...
```

### Import Paths

Configure TypeScript paths in `tsconfig.json`:

```json
{
  "compilerOptions": {
    "baseUrl": "./src",
    "paths": {
      "@types/*": ["types/*"],
      "@types/generated": ["types/generated/index.ts"],
      "@core/*": ["core/*"],
      "@forms/*": ["forms/*"],
      "@components/*": ["components/*"]
    }
  }
}
```

### Usage

```typescript
// ✅ Clean imports
import { Article } from '@types/generated';
import { api } from '@core/http';
import { validate, required } from '@forms/validator';
import { ArticleCard } from '@components/ArticleCard';
```

---

## 🎯 Quick Reference

### Type Safety Checklist

- [ ] Import types from `@types/generated`
- [ ] Use type guards for API responses
- [ ] Type all function parameters and return values
- [ ] Use interfaces for component props
- [ ] Avoid `any` - use `unknown` instead

### HTTP Best Practices

- [ ] Use `api.get/post/put/delete` helpers
- [ ] Always handle errors
- [ ] Type all API responses
- [ ] Use generated types for request/response

### Form Best Practices

- [ ] Validate on blur + submit
- [ ] Show clear error messages
- [ ] Disable submit button while validating
- [ ] Use HTMX for server-side validation

---

## 📖 Examples

### Complete Example: Blog Page

```typescript
// Templates/src/frontend/pages/blog.ts
import { api } from '@core/http';
import { handleError } from '@core/error-handler';
import { Article, isArticle } from '@types/generated';

interface BlogPageState {
  articles: Article[];
  loading: boolean;
  page: number;
  totalPages: number;
}

export async function initBlogPage(): Promise<BlogPageState> {
  const state: BlogPageState = {
    articles: [],
    loading: true,
    page: 1,
    totalPages: 1,
  };

  try {
    const response = await api.get<{
      articles: unknown[];
      page: number;
      totalPages: number;
    }>('/api/articles?page=1');

    state.articles = response.articles.filter(isArticle);
    state.page = response.page;
    state.totalPages = response.totalPages;
  } catch (error) {
    handleError(error);
  } finally {
    state.loading = false;
  }

  return state;
}

// Initialize Alpine component
document.addEventListener('alpine:init', () => {
  Alpine.data('blogPage', () => ({
    ...await initBlogPage(),
    
    async loadPage(page: number) {
      this.loading = true;
      try {
        const response = await api.get(`/api/articles?page=${page}`);
        this.articles = response.articles.filter(isArticle);
        this.page = page;
      } catch (error) {
        handleError(error);
      } finally {
        this.loading = false;
      }
    }
  }));
});
```

---

**Last Updated:** 2026-03-01
**Version:** 1.0
