# PHP 8.4 Enum Migration Plan

**Goal:** Migrate ValueObjects from class-based pattern to native PHP 8.4 enumerators for better type safety and developer experience.

---

## 📊 Current State

### ArticleStatus (Class Pattern)
```php
// src/domain/ValueObjects/ArticleStatus.php
final class ArticleStatus
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';
    
    private const VALID_STATUSES = [
        self::DRAFT,
        self::PUBLISHED,
        self::ARCHIVED,
    ];
    
    public function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException('Invalid article status: ' . $value);
        }
    }
}

// Usage
$status = new ArticleStatus(ArticleStatus::DRAFT);
if ($status->value() === 'draft') { }  // String comparison ❌
```

**Problems:**
- ❌ Manual validation in constructor
- ❌ String comparison (error-prone)
- ❌ No exhaustive matching
- ❌ IDE can't find all references safely
- ❌ No built-in serialization

---

## 🎯 Target State

### ArticleStatus (PHP 8.4 Enum)
```php
// src/domain/ValueObjects/ArticleStatus.php
enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::DRAFT => in_array($newStatus, [self::PUBLISHED, self::ARCHIVED], true),
            self::PUBLISHED => $newStatus === self::ARCHIVED,
            self::ARCHIVED => false,
        };
    }
    
    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }
    
    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }
    
    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }
}

// Usage
$status = ArticleStatus::DRAFT;
if ($status === ArticleStatus::DRAFT) { }  // Type-safe comparison ✅
```

**Benefits:**
- ✅ Native type safety
- ✅ Exhaustive match expressions
- ✅ IDE refactoring support
- ✅ Built-in serialization (`->value`)
- ✅ Self-documenting code

---

## 📋 Migration Checklist

### Phase 1: ArticleStatus (30 min)
- [ ] Convert `ArticleStatus` class to enum
- [ ] Update `Article` entity to use enum
- [ ] Update `ArticleManager` service
- [ ] Update repositories
- [ ] Update controllers/actions
- [ ] Update templates
- [ ] Run tests

### Phase 2: Slug ValueObject (30 min)
- [ ] Convert `Slug` to readonly class (not enum - has behavior)
- [ ] Add `__toString()` method
- [ ] Update all references
- [ ] Run tests

### Phase 3: Email ValueObject (30 min)
- [ ] Convert `Email` to readonly class
- [ ] Add `__toString()` method
- [ ] Update all references
- [ ] Run tests

### Phase 4: TypeScript Generation Update (15 min)
- [ ] Update `generate-types.php` to handle enums
- [ ] Generate proper TypeScript union types
- [ ] Test type generation
- [ ] Update frontend code

### Phase 5: Testing & Documentation (15 min)
- [ ] Run full test suite
- [ ] Update documentation
- [ ] Add migration guide
- [ ] Commit changes

---

## 🔧 Implementation Details

### Article Entity Changes

**Before:**
```php
private ArticleStatus $status;

public function status(): ArticleStatus
{
    return $this->status;
}

public function publish(): void
{
    $this->status = new ArticleStatus(ArticleStatus::PUBLISHED);
}
```

**After:**
```php
private ArticleStatus $status;

public function status(): ArticleStatus
{
    return $this->status;
}

public function publish(): void
{
    $this->status = ArticleStatus::PUBLISHED;
}
```

### Database Serialization

**No changes needed!** Enums serialize to strings automatically:

```php
// Article::toArray()
public function toArray(): array
{
    return [
        'status' => $this->status->value,  // 'draft', 'published', 'archived'
    ];
}

// Article::fromArray()
public static function fromArray(array $data): self
{
    $article->status = ArticleStatus::from($data['status']);
}
```

### TypeScript Generation

**Updated generator will produce:**

```typescript
// Templates/src/types/generated/ArticleStatus.ts
export type ArticleStatus = 'draft' | 'published' | 'archived';

export function isArticleStatus(value: unknown): value is ArticleStatus {
  return typeof value === 'string' && 
    ['draft', 'published', 'archived'].includes(value);
}

// Templates/src/types/generated/Article.ts
export interface Article {
  id: string;
  status: ArticleStatus;  // ✅ Union type, not string
  // ...
}
```

---

## ⚠️ Breaking Changes

### API Responses
**No breaking changes** - enums serialize to strings automatically.

### Database
**No schema changes** - enum values are still stored as strings.

### Frontend TypeScript
**Type improvement** - `status: string` → `status: ArticleStatus` (union type)

---

## 🧪 Testing Strategy

### Unit Tests
```php
// tests/Domain/Model/ArticleStatusTest.php
public function test_can_create_draft_status(): void
{
    $status = ArticleStatus::DRAFT;
    $this->assertSame('draft', $status->value);
    $this->assertTrue($status->isDraft());
}

public function test_status_transitions(): void
{
    $draft = ArticleStatus::DRAFT;
    $this->assertTrue($draft->canTransitionTo(ArticleStatus::PUBLISHED));
    $this->assertFalse($draft->canTransitionTo(ArticleStatus::DRAFT));
}
```

### Integration Tests
```php
// tests/Integration/ArticleManagerTest.php
public function test_publish_article(): void
{
    $article = $this->articleManager->create('Title', 'Content', 'author-id');
    
    $this->articleManager->publish($article->id());
    
    $updated = $this->articleManager->find($article->id());
    $this->assertSame(ArticleStatus::PUBLISHED, $updated->status());
}
```

---

## 📖 Migration Guide for Developers

### Finding Old Code

**Search for:**
```bash
grep -r "new ArticleStatus" src/
grep -r "ArticleStatus::DRAFT" src/
grep -r "->status()->value()" src/
```

### Updating Code

**Before:**
```php
if ($article->status()->value() === 'draft') { }
```

**After:**
```php
if ($article->status() === ArticleStatus::DRAFT) { }
```

**Before:**
```php
$status = new ArticleStatus(ArticleStatus::PUBLISHED);
```

**After:**
```php
$status = ArticleStatus::PUBLISHED;
```

---

## 🚀 Rollback Plan

If issues are found:

1. Revert enum changes
2. Restore class-based ValueObjects
3. Run `composer install` to regenerate types
4. Deploy previous version

**Low risk** - changes are mostly internal, API remains the same.

---

## ✅ Success Criteria

- [ ] All ValueObjects migrated to enums/readonly classes
- [ ] All tests passing
- [ ] TypeScript types correctly generated
- [ ] No breaking changes to API
- [ ] Documentation updated
- [ ] Team trained on new pattern

---

## 🔗 Resources

- [PHP 8.1 Enums RFC](https://wiki.php.net/rfc/enumerations)
- [PHP 8.4 Enum Improvements](https://www.php.net/manual/en/language.types.enumerations.php)
- [Backed Enums](https://www.php.net/manual/en/language.types.enumerations.backed.php)
- [Match Expression](https://www.php.net/manual/en/control-structures.match.php)

---

**Estimated Time:** 2 hours
**Risk Level:** Low (internal refactoring, no API changes)
**Priority:** Medium (improves DX and type safety)
