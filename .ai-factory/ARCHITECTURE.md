# Architecture: Domain-Driven Design (DDD)

## Overview

Your PHP CMS follows **Domain-Driven Design (DDD)** with a clean layered architecture. This approach separates pure business logic (Domain) from application use cases (Application) and external concerns (Infrastructure), making the codebase maintainable, testable, and adaptable to change.

## Decision Rationale

- **Project type:** PHP CMS and Blog Platform with admin panel
- **Tech stack:** PHP 8.4+, SQLite, Native PHP templates, TailwindCSS + Alpine.js
- **Key factor:** Complex business logic with multiple domain entities (Articles, Pages, Forms, Users) requiring clear boundaries and testability

## Folder Structure

```
nativa/
├── src/
│   ├── domain/                 # Domain Layer - Pure business logic
│   │   ├── Model/             # Entities (Article, Page, Form, User)
│   │   ├── ValueObjects/      # Value Objects (Slug, Email, Status)
│   │   ├── Services/          # Domain Services
│   │   ├── Events/            # Domain Events
│   │   └── Repository/        # Repository interfaces
│   │
│   ├── application/           # Application Layer - Use cases
│   │   ├── Services/         # Application Services
│   │   ├── DTOs/            # Data Transfer Objects
│   │   └── Exceptions/      # Application Exceptions
│   │
│   ├── infrastructure/       # Infrastructure Layer - External concerns
│   │   ├── Persistence/     # Database (UnitOfWork, Repositories)
│   │   ├── Storage/         # File storage
│   │   └── Messaging/       # Email notifications
│   │
│   └── interfaces/          # Interfaces Layer - Adapters
│       ├── HTTP/           # Web controllers (Frontend + Admin)
│       ├── CLI/            # Console commands
│       └── Templates/      # View templates
│
├── public/                   # Web root
│   └── index.php            # Application entry point
│
├── bin/                      # CLI scripts
│   └── cms                  # Console entry point
│
├── tests/                    # Unit & Integration tests
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   └── Integration/
│
└── data/                     # SQLite database
    └── cms.db
```

## Dependency Rules

- ✅ **Domain → nothing** (pure business logic, no external dependencies)
- ✅ **Application → Domain** (uses entities and value objects)
- ✅ **Infrastructure → Domain + Application** (implements interfaces)
- ✅ **Interfaces → Application + Infrastructure** (orchestrates use cases)
- ❌ **Domain → Application/Infrastructure** (forbidden)
- ❌ **Application → Infrastructure** (forbidden)
- ❌ **Infrastructure → Interfaces** (forbidden)

## Layer/Module Communication

1. **HTTP Request Flow:**
   - `public/index.php` → Router → Controller (Interfaces)
   - Controller → Application Service (Application)
   - Application Service → Domain Service/Entity (Domain)
   - Application Service → Repository Interface (Domain)
   - Repository Implementation (Infrastructure) → Database

2. **Domain Events:**
   - Entities raise events (e.g., `ArticleCreated`, `FormSubmitted`)
   - Event handlers in Application/Infrastructure layer process them
   - Enables loose coupling between bounded contexts

3. **UnitOfWork Pattern:**
   - Application services use UnitOfWork for transaction management
   - Ensures consistency across multiple repository operations

## Key Principles

1. **Entities have identity** - Use hex ID generation (`bin2hex(random_bytes(16))`)
2. **Value Objects are immutable** - Slug, Email, ArticleStatus enforce validation at construction
3. **Repositories are interfaces in Domain** - Implementations in Infrastructure
4. **Application Services orchestrate** - They coordinate Domain objects and Infrastructure
5. **Controllers are thin** - Delegate business logic to Application Services
6. **Native PHP templates** - Use `TemplateRenderer` with layout support

## Code Examples

### Entity Pattern (Domain Layer)

```php
<?php

namespace Domain\Model;

use Domain\ValueObjects\Slug;
use Domain\ValueObjects\ArticleStatus;

class Article
{
    private string $id;
    private string $authorId;
    private ?string $categoryId;
    private string $title;
    private Slug $slug;
    private ?string $excerpt;
    private string $content;
    private ?string $image;
    private ArticleStatus $status;
    private int $views;
    private ?\DateTimeImmutable $publishedAt;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(
        string $authorId,
        string $title,
        string $content,
        ?string $categoryId = null,
        ?string $excerpt = null
    ): self {
        $article = new self();
        $article->id = bin2hex(random_bytes(16));
        $article->authorId = $authorId;
        $article->title = $title;
        $article->slug = Slug::fromString($title); // Auto-generate from title
        $article->content = $content;
        $article->categoryId = $categoryId;
        $article->excerpt = $excerpt;
        $article->status = ArticleStatus::draft();
        $article->views = 0;
        $article->createdAt = new \DateTimeImmutable();
        $article->updatedAt = new \DateTimeImmutable();
        return $article;
    }

    public function publish(): void
    {
        if ($this->status->isPublished()) {
            throw new \DomainException('Article is already published');
        }
        $this->status = ArticleStatus::published();
        $this->publishedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function unpublish(): void
    {
        $this->status = ArticleStatus::draft();
        $this->publishedAt = null;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
```

### Value Object (Domain Layer)

```php
<?php

namespace Domain\ValueObjects;

class Slug
{
    private string $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(string $title): self
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return new self($slug);
    }

    private function validate(string $value): void
    {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException('Invalid slug format');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }
}
```

### Repository Interface (Domain Layer)

```php
<?php

namespace Domain\Repository;

use Domain\Model\Article;

interface ArticleRepository
{
    public function findById(string $id): ?Article;
    public function findBySlug(string $slug): ?Article;
    public function findAllPublished(int $limit = 10, int $offset = 0): array;
    public function save(Article $article): void;
    public function delete(Article $article): void;
}
```

### Application Service

```php
<?php

namespace Application\Services;

use Domain\Model\Article;
use Domain\Repository\ArticleRepository;
use Infrastructure\Persistence\UnitOfWork;

class ArticleAppService
{
    public function __construct(
        private ArticleRepository $articleRepo,
        private UnitOfWork $unitOfWork
    ) {}

    public function createArticle(array $data): Article
    {
        $article = Article::create(
            authorId: $data['author_id'],
            title: $data['title'],
            content: $data['content'],
            categoryId: $data['category_id'] ?? null,
            excerpt: $data['excerpt'] ?? null
        );

        $this->unitOfWork->begin();
        try {
            $this->articleRepo->save($article);
            $this->unitOfWork->commit();
        } catch (\Exception $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }

        return $article;
    }

    public function publishArticle(string $id): void
    {
        $article = $this->articleRepo->findById($id);
        if (!$article) {
            throw new ArticleNotFoundException($id);
        }

        $this->unitOfWork->begin();
        try {
            $article->publish();
            $this->articleRepo->save($article);
            $this->unitOfWork->commit();
        } catch (\Exception $e) {
            $this->unitOfWork->rollback();
            throw $e;
        }
    }
}
```

### Controller (Interfaces Layer)

```php
<?php

namespace Interfaces\HTTP\Admin;

use Application\Services\ArticleAppService;
use Interfaces\HTTP\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController
{
    public function __construct(
        private ArticleAppService $articleService
    ) {}

    public function store(Request $request): Response
    {
        $data = $request->request->all();
        $article = $this->articleService->createArticle($data);
        return Router::redirect('/admin/articles/' . $article->getId());
    }
}
```

### Repository Implementation (Infrastructure Layer)

```php
<?php

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Article;
use Domain\Repository\ArticleRepository;
use Infrastructure\Persistence\DatabaseConnection;

class ArticleRepositoryImpl implements ArticleRepository
{
    public function __construct(
        private DatabaseConnection $db
    ) {}

    public function findById(string $id): ?Article
    {
        $stmt = $this->db->getConnection()->prepare(
            'SELECT * FROM articles WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data) : null;
    }

    private function hydrate(array $data): Article
    {
        return Article::fromArray($data);
    }
}
```

## Anti-Patterns

- ❌ **Business logic in controllers** - Controllers should only handle HTTP concerns
- ❌ **Direct database access in Application/Domain** - Always use repositories
- ❌ **Infrastructure dependencies in Domain** - No PDO, no file system, no HTTP in Domain
- ❌ **Anemic domain models** - Entities should have behavior, not just getters/setters
- ❌ **Skipping UnitOfWork** - Always wrap write operations in transactions
- ❌ **Raw arrays instead of DTOs** - Use DTOs for data transfer between layers
- ❌ **Template logic beyond presentation** - Keep templates dumb (only display logic)
