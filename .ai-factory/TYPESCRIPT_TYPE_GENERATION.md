# TypeScript Type Generation from PHP

**Goal:** Auto-generate TypeScript types from PHP DTOs/Entities for end-to-end type safety.

---

## 📊 Current State

### PHP Backend (Typed ✅)
```php
// src/domain/Model/Article.php
final class Article {
    public function __construct(
        private string $id,
        private string $title,
        private string $slug,
        private ?string $excerpt,
        private string $content,
        private string $authorId,
        private ?string $categoryId,
        private ArticleStatus $status,
        private int $views,
        private array $tags,
        private ?\DateTimeImmutable $publishedAt,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {}
}
```

### TypeScript Frontend (Not Typed ❌)
```typescript
// Templates/src/types/index.ts
interface Article {
  // ❌ Manual, error-prone, out of sync
  id: string;
  title: string;
  slug: string;
  // ... manually maintained
}
```

---

## 🎯 Target State

### Auto-generated TypeScript (✅)
```typescript
// Templates/src/types/generated/Article.ts
// ⚠️ AUTO-GENERATED - DO NOT EDIT

export interface Article {
  id: string;
  title: string;
  slug: string;
  excerpt: string | null;
  content: string;
  authorId: string;
  categoryId: string | null;
  status: 'draft' | 'published' | 'archived';
  views: number;
  tags: string[];
  publishedAt: string | null;  // ISO 8601
  createdAt: string;           // ISO 8601
  updatedAt: string;           // ISO 8601
}
```

---

## 🔧 Implementation Options

### Option 1: `php-to-typescript` (Recommended) ⭐

**Tool:** [`php-to-typescript`](https://github.com/runroom/php-to-typescript) or similar

**Pros:**
- ✅ Parses PHP reflection
- ✅ Handles PHP 8.4 types
- ✅ Generates interfaces + type guards
- ✅ CLI tool, easy CI/CD integration

**Cons:**
- ⚠️ May need custom annotations for complex types

**Setup:**
```bash
cd Templates
pnpm add -D php-to-typescript
```

**Config (`php-to-ts.config.json`):**
```json
{
  "source": "../src/domain/Model",
  "output": "./src/types/generated",
  "namespace": "Domain\\Model",
  "include": ["Article.php", "User.php", "Page.php", "Form.php"],
  "options": {
    "strictNullChecks": true,
    "dateType": "string",
    "arrayPrefix": "readonly"
  }
}
```

**Script (`package.json`):**
```json
{
  "scripts": {
    "types:generate": "php-to-typescript --config php-to-ts.config.json",
    "types:check": "php-to-typescript --check"
  }
}
```

---

### Option 2: Custom PHP Script (Full Control) ⭐⭐

**Create:** `scripts/generate-types.php`

**Pros:**
- ✅ Full control over output format
- ✅ Can handle custom PHP patterns
- ✅ No external dependencies
- ✅ Can generate type guards, validators, API clients

**Cons:**
- ⚠️ More initial work
- ⚠️ Maintenance burden

**Example:**
```php
#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Domain\Model\Article;
use Domain\Model\User;

$classes = [
    Article::class,
    User::class,
];

foreach ($classes as $class) {
    $reflection = new ReflectionClass($class);
    $typescript = generateTypeScriptInterface($reflection);
    file_put_contents(
        __DIR__ . "/../Templates/src/types/generated/{$reflection->getShortName()}.ts",
        $typescript
    );
}

function generateTypeScriptInterface(ReflectionClass $reflection): string {
    $interface = "export interface {$reflection->getShortName()} {\n";
    
    foreach ($reflection->getProperties() as $property) {
        $type = mapPhpToTs($property->getType());
        $name = $property->getName();
        $optional = $property->getType()->allowsNull() ? '?' : '';
        $interface .= "  {$name}{$optional}: {$type};\n";
    }
    
    $interface .= "}\n";
    return $interface;
}

function mapPhpToTs(ReflectionType $type): string {
    // Map PHP types to TypeScript
    // string → string
    // int → number
    // bool → boolean
    // array → unknown[]
    // ?Foo → Foo | null
    // DateTimeImmutable → string (ISO 8601)
}
```

---

### Option 3: OpenAPI/Swagger (API-First)

**Tool:** [`zircote/swagger-php`](https://github.com/zircote/swagger-php) + [`openapi-typescript`](https://github.com/drwpow/openapi-typescript)

**Pros:**
- ✅ Generates API types + endpoints
- ✅ Industry standard
- ✅ Works with any frontend

**Cons:**
- ⚠️ Requires PHP DocBlocks
- ⚠️ More complex setup
- ⚠️ Overkill for just types

**Setup:**
```bash
composer require zircote/swagger-php
pnpm add -D openapi-typescript
```

**Generate:**
```bash
# 1. Generate OpenAPI spec from PHP
./vendor/bin/openapi src/ -o public/openapi.yaml

# 2. Generate TypeScript types
npx openapi-typescript public/openapi.yaml -o Templates/src/types/api.ts
```

---

## 📁 Proposed File Structure

```
Templates/src/types/
├── generated/              # ⚠️ AUTO-GENERATED - DO NOT EDIT
│   ├── Article.ts
│   ├── User.ts
│   ├── Page.ts
│   ├── Form.ts
│   ├── FormSubmission.ts
│   └── index.ts           # Barrel export
│
├── api/                    # API-specific types
│   ├── requests.ts
│   ├── responses.ts
│   └── endpoints.ts
│
├── manual/                 # Manually maintained types
│   ├── ui.ts              # UI component props
│   ├── forms.ts           # Form state types
│   └── utils.ts           # Utility types
│
└── index.ts               # Main export
```

---

## 🔄 CI/CD Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/types.yml
name: Type Safety Check

on: [push, pull_request]

jobs:
  generate-types:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      
      - name: Install dependencies
        run: |
          composer install
          cd Templates && pnpm install
      
      - name: Generate TypeScript types
        run: |
          php scripts/generate-types.php
          cd Templates && pnpm run types:generate
      
      - name: Check for changes
        run: |
          git diff --exit-code Templates/src/types/generated/
          # Fails if types are out of sync
```

### Pre-commit Hook

```bash
#!/bin/bash
# .git/hooks/pre-commit

echo "Generating TypeScript types..."
php scripts/generate-types.php

git add Templates/src/types/generated/

echo "Running TypeScript check..."
cd Templates && pnpm run type-check
```

---

## 📝 Type Mapping Reference

| PHP Type | TypeScript Type | Notes |
|----------|----------------|-------|
| `string` | `string` | Direct mapping |
| `int` | `number` | JS has no int |
| `float` | `number` | JS has no float |
| `bool` | `boolean` | Direct mapping |
| `array` | `unknown[]` or `Record<string, unknown>` | Context dependent |
| `array<string, Foo>` | `Record<string, Foo>` | Associative array |
| `Foo[]` | `Foo[]` | Indexed array |
| `?string` | `string \| null` | Nullable |
| `\DateTimeImmutable` | `string` | ISO 8601 format |
| `ArticleStatus` (enum) | `'draft' \| 'published' \| 'archived'` | Union type |
| `mixed` | `unknown` | Last resort |

---

## 🚀 Implementation Plan

### Phase 1: Setup (30 min)
1. Choose approach (Option 1 or 2)
2. Create script/config
3. Test with Article entity

### Phase 2: Generate All Types (30 min)
1. Add all domain entities
2. Add all DTOs
3. Add all Value Objects
4. Generate and verify

### Phase 3: Integration (30 min)
1. Update existing TypeScript code to use generated types
2. Add to CI/CD
3. Add pre-commit hook
4. Document usage

### Phase 4: Advanced (Optional)
1. Generate API client from DTOs
2. Generate form validators
3. Generate type guards (`isArticle(data)`)

---

## 📖 Usage Examples

### In TypeScript Code

```typescript
import { Article, User } from '@types/generated';

// Type-safe API response
async function fetchArticle(slug: string): Promise<Article> {
  const response = await fetch(`/api/articles/${slug}`);
  return response.json();
}

// Type guard (if generated)
import { isArticle } from '@types/generated';

function handleData(data: unknown) {
  if (isArticle(data)) {
    console.log(data.title); // ✅ Typed!
  }
}
```

### In PHP Actions

```php
// Already type-safe with PHP 8.4!
final class ShowArticleAction extends Action {
    public function handle(Request $request): Response {
        $article = $this->articleManager->findBySlug($slug);
        
        // Return JSON with proper types
        return $this->json($article->toArray());
    }
}
```

---

## ✅ Success Criteria

- [ ] All PHP entities have TypeScript equivalents
- [ ] Types auto-generated on `composer install`
- [ ] CI fails if types are out of sync
- [ ] Frontend code uses generated types (no `any`)
- [ ] Type errors caught in CI, not production

---

## 🔗 Resources

- [PHP-to-TypeScript](https://github.com/runroom/php-to-typescript)
- [openapi-typescript](https://github.com/drwpow/openapi-typescript)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [PHP 8.4 Type System](https://www.php.net/manual/en/language.types.php)

---

**Recommended:** Start with **Option 2 (Custom Script)** for full control, then evaluate if we need Option 1 or 3 later.
