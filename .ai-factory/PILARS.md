# Project Pillars

Core principles and non-negotiable standards for the Nativa PHP CMS codebase.

---

## Build for Speed (Frontend)

### Core Philosophy

> **"Core + Page-Specific"**
>
> Split bundles into minimal core (loaded on every page) and page-specific features (loaded only when needed).

### Why It Matters

- Every 100ms faster = +1% conversion
- Lighthouse 100/100/100/100 = better SEO ranking
- Core Web Vitals = Google ranking factor
- Fast LCP/FID = lower bounce rate

### Implementation

**Core Bundle (<10KB gzipped):**
- Theme initialization
- CSRF token management
- Mobile menu navigation
- Smooth scroll
- Notifications system

**Page-Specific Bundles:**
- Homepage: GSAP animations, cookie consent, gallery
- Docs: Search, syntax highlighting, back-to-top
- Blog: Article interactions, reading progress
- Contact: Form validation, map integration

### Dark/Light Theme Consistency

**Critical Rule:**
> Theme must be consistent across the entire page. No mixing dark header with light content.

**Implementation:**
1. Theme detected BEFORE CSS loads (`init.js` in `<head>`)
2. All colors use CSS variables (`var(--color-bg)`)
3. No inline theme overrides

### Performance Targets

| Metric | Target | Strategy |
|--------|--------|----------|
| Lighthouse Performance | 100 | Code splitting, lazy loading |
| Lighthouse Accessibility | 100 | Semantic HTML, ARIA |
| Lighthouse Best Practices | 100 | HTTPS, no deprecated APIs |
| Lighthouse SEO | 100 | Meta tags, structured data |
| Core Bundle Size | <10KB | Minimal dependencies |
| Page Bundle Size | <20KB | Feature-specific loading |

---

## Type Safety (Backend)

### Core Philosophy

> **"Validate and cast at the input, no mixed inside"**
>
> This is our **fundamental principle** for writing clean, predictable, and maintainable code.

### Strict Types Declaration

```php
<?php

declare(strict_types=1);

namespace Application\Services;
```

**We use `declare(strict_types=1);` in all files** except:
- Template files (`.php` in `Templates/`)
- Entry points (`public/index.php`, `bin/cms`)

### Type Boundaries

| Layer | Type Mode | Approach to `mixed` |
|-------|-----------|---------------------|
| **Domain (VO, Entity)** | Strict | No `mixed` |
| **DTO** | Strict | No `mixed` |
| **Application Services** | Strict | No `mixed` |
| **Infrastructure (DB, HTTP)** | Strict with validation | Validate at input |
| **Interfaces (Controllers)** | Strict with validation | Validate at input |

### The Mixed Type Boundary Rule

```php
// BAD - mixed crosses boundaries
final class StoreArticleAction
{
    public function __invoke(Request $request): Response
    {
        $data = $request->getRequest(); // array<string, mixed>
        
        $article = $this->articleManager->create(
            title: $data['title'],        // mixed
            content: $data['content'],    // mixed
            excerpt: $data['excerpt'] ?? null, // mixed
        );
    }
}

// GOOD - validation at boundaries
final class StoreArticleAction
{
    public function __invoke(Request $request): Response
    {
        /** @var array<string, mixed> $data */
        $data = $request->getRequest();
        
        // Validation and cast at boundary
        $title = isset($data['title']) && is_string($data['title']) 
            ? $data['title'] 
            : throw new ValidationException('Title is required');
            
        $content = isset($data['content']) && is_string($data['content']) 
            ? $data['content'] 
            : '';
            
        $excerpt = isset($data['excerpt']) && is_string($data['excerpt']) 
            ? $data['excerpt'] 
            : null;
        
        // Inside the app we work with clean types
        $article = $this->articleManager->create(
            title: $title,      // string
            content: $content,  // string
            excerpt: $excerpt,  // string|null
        );
    }
}
```

### Input Validation Pattern

```php
// BAD - implicit cast
$email = trim((string) $value);

// GOOD - explicit validation
if (!is_string($value)) {
    throw new ValidationException('Email must be a string');
}
$email = trim($value);
```

### Array Type Annotations

```php
// BAD - no value type
/** @var array */
private array $data;

// GOOD - explicit value type
/** @var array<string, mixed> */
private array $data;

// EVEN BETTER - specific shape
/** @var array{path: string, url: string, size: int} */
private array $fileData;
```

### Value Objects & DTOs as Type Boundaries

```php
// VO guarantees valid state
final class Email
{
    public function __construct(
        public readonly string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }
    }
}

// DTO with precise types
final class CreateArticleCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly ?string $excerpt = null,
        public readonly string $authorId,
    ) {}
}
```

### Static Analysis Configuration

```neon
# phpstan.neon
parameters:
    level: 9  # Maximum level for strict type checking
    paths:
        - src/
    excludePaths:
        - src/Templates/*  # Templates excluded (presentation layer)
    reportUnmatchedIgnoredErrors: false
```

**Tools:**
- **PHPStan level 9** - Maximum strictness
- **Psalm** - Cross-check for type inference
- **Baseline** - Gradual reduction of technical debt

### Code Quality Gates

1. **New code** - Must pass level 9 without exceptions
2. **Existing code** - Fix at least 1 type issue on every touch
3. **System boundaries** - All inputs validated (`$_SERVER`, `$_POST`, `$_SESSION`)
4. **Domain layer** - No `mixed`, no casts

---

## Domain-Driven Design

### Layer Dependency Rules

- **Domain** - No dependencies (pure business logic)
- **Application** - Depends on Domain only
- **Infrastructure** - Implements Domain/Application interfaces
- **Interfaces** - Orchestrates Application + Infrastructure

### Forbidden Dependencies

- Domain -/-> Application/Infrastructure
- Application -/-> Infrastructure
- Infrastructure -/-> Interfaces

### Key Principles

1. **Entities have identity** - Hex ID generation (`bin2hex(random_bytes(16))`)
2. **Value Objects are immutable** - Validation at construction
3. **Repositories are interfaces in Domain** - Implementations in Infrastructure
4. **Application Services orchestrate** - Coordinate Domain objects and Infrastructure
5. **Controllers are thin** - Delegate to Application Services
6. **Native PHP templates** - Use `TemplateRenderer` with layout support

---

## Security

### Input Validation

- All user input validated at system boundaries
- CSRF tokens on all state-changing forms
- Rate limiting on authentication endpoints

### Data Protection

- Passwords hashed with `password_hash()` (default algorithm)
- Session-based authentication with secure cookies
- SQL injection prevented via PDO prepared statements

### Output Escaping

- All output escaped with `htmlspecialchars()` in templates
- Content-Type headers set explicitly
- No user data in headers without validation

---

## Code Organization

### File Structure

```
src/
├── domain/           # Pure business logic
├── application/      # Use cases and orchestration
├── infrastructure/   # External concerns (DB, files, email)
├── interfaces/       # Adapters (HTTP, CLI)
└── Templates/        # View templates (excluded from strict types)
```

### Naming Conventions

- **Entities** - Singular, domain concepts (`Article`, `User`)
- **Value Objects** - Domain concepts (`Slug`, `Email`, `Status`)
- **Services** - Capability-based (`ArticleManager`, `FormValidationService`)
- **Repositories** - Entity + `Repository` (`ArticleRepository`)
- **DTOs** - Noun + `DTO` or `Command` (`CreateArticleCommand`)
- **Exceptions** - What + `Exception` (`ArticleNotFoundException`)

### Testing Strategy

- **Domain** - Unit tests (pure logic, no dependencies)
- **Application** - Integration tests (with in-memory repositories)
- **Infrastructure** - Integration tests (with real DB)
- **Interfaces** - Acceptance tests (Codeception)

---

## Performance

### Database

- SQLite for development and small deployments
- UnitOfWork pattern for transaction management
- Prepared statements for all queries

### Caching

- OPcache enabled in production
- Template compilation with cache invalidation
- Asset versioning for static files

### Assets

- Vite build system with code splitting
- Hashed filenames for cache busting
- Gzip/Brotli compression for production

---

## Documentation

### Living Documents

- `ARCHITECTURE.md` - System architecture and patterns
- `PILARS.md` - Core principles (this file)
- `ROADMAP.md` - Future milestones
- `DESCRIPTION.md` - Project overview

### Historical Documents

- Audit reports (removed after issues resolved)
- Migration plans (removed after completion)
- Implementation plans (removed after completion)

### Code Documentation

- PHPDoc for public methods with complex types
- Inline comments for "why", not "what"
- README for setup and development workflow
