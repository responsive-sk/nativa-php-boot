# Testing PHP CMS

## Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
vendor/bin/phpunit tests/Domain/
vendor/bin/phpunit tests/Application/
vendor/bin/phpunit tests/Integration/

# Run with coverage (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage
```

## Test Structure

```
tests/
├── Domain/              # Domain layer tests
│   ├── Model/          # Entity tests
│   └── ValueObjects/   # Value Object tests
├── Application/         # Application layer tests
│   └── Services/       # Service tests
├── Infrastructure/      # Infrastructure layer tests
│   └── Persistence/    # Repository tests
└── Integration/         # Integration tests
    └── HttpTest.php    # HTTP endpoint tests
```

## Writing Tests

### Domain Test Example

```php
use PHPUnit\Framework\TestCase;
use Domain\Model\Article;

class ArticleTest extends TestCase
{
    public function testCreateArticle(): void
    {
        $article = Article::create(
            title: 'Test',
            content: 'Content',
            authorId: 'author-123'
        );

        $this->assertSame('Test', $article->title());
        $this->assertSame('test', $article->slug());
        $this->assertTrue($article->isDraft());
    }
}
```

### Service Test Example

```php
use PHPUnit\Framework\TestCase;
use Application\Services\ArticleAppService;

class ArticleAppServiceTest extends TestCase
{
    private ArticleAppService $service;

    protected function setUp(): void
    {
        // Setup service with in-memory database
        $this->service = new ArticleAppService(...);
    }

    public function testCreateArticle(): void
    {
        $article = $this->service->create(
            title: 'Test',
            content: 'Content',
            authorId: 'author-123'
        );

        $this->assertNotNull($article->id());
    }
}
```

## Best Practices

1. **Test in isolation** - Use in-memory SQLite for tests
2. **Test behavior, not implementation** - Focus on what, not how
3. **Use descriptive test names** - `testPublishArticleChangesStatus()`
4. **Arrange-Act-Assert** - Structure tests clearly
5. **Test edge cases** - Null values, empty strings, invalid input

## Coverage Goals

- Domain Layer: 90%+ (critical business logic)
- Application Layer: 80%+ (use cases)
- Infrastructure Layer: 70%+ (integration points)
- Interfaces Layer: 50%+ (controllers)
