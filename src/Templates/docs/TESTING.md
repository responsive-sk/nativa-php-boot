# Testing Architecture

Comprehensive testing setup for both PHP backend and TypeScript frontend.

## Overview

```
yii-boot/
├── tests/                          # PHP Backend Tests (Codeception)
│   ├── Unit/                       # Unit tests
│   │   ├── Domain/                 # Domain layer tests
│   │   ├── Infrastructure/         # Infrastructure layer tests
│   │   └── UseCase/                # Application layer tests
│   ├── Functional/                 # Functional tests
│   ├── Site/                       # Integration tests
│   └── Console/                    # CLI command tests
│
└── views/templates/                # Frontend Source
    ├── src/
    │   ├── core/                   # Core utilities + tests
    │   │   └── csrf.test.ts
    │   ├── storage/                # Storage utilities + tests
    │   │   ├── storage.test.ts
    │   │   └── cookieManager.test.ts
    │   ├── ui/                     # UI components + tests
    │   │   ├── notifications.test.ts
    │   │   └── toast.test.ts
    │   ├── effects/                # Visual effects + tests
    │   │   └── motion.test.ts
    │   ├── navigation/             # Navigation + tests
    │   │   └── smoothScroll.test.ts
    │   └── forms/                  # Form utilities
    │
    ├── tests/                      # Frontend test configuration
    │   └── setup.ts                # Test setup & mocks
    └── vitest.config.ts            # Vitest configuration
```

## PHP Backend Testing (Codeception)

### Running Tests

```bash
# Run all tests
APP_ENV=test composer test

# Run specific suite
./vendor/bin/codecept run Unit
./vendor/bin/codecept run Functional
./vendor/bin/codecept run Site

# Run single test file
./vendor/bin/codecept run Unit SearchServiceTest

# Run with coverage
./vendor/bin/codecept run --coverage
```

### Test Structure

**Unit Tests** - Test individual classes in isolation

```php
<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Post;

use App\Domain\Post\Post;
use App\Domain\Post\PostId;
use Codeception\Test\Unit;

final class PostTest extends Unit
{
    public function testCreatePost(): void
    {
        $id = PostId::generate();
        $post = new Post($id, 'Title', 'Content', PostStatus::Published);
        
        $this->assertEquals('Title', $post->getTitle());
    }
}
```

### Available Test Suites

| Suite | Purpose | Location |
|-------|---------|----------|
| Unit | Domain logic, services, value objects | `tests/Unit/` |
| Functional | Request/response, forms, databases | `tests/Functional/` |
| Site | Full application integration | `tests/Site/` |
| Console | CLI commands | `tests/Console/` |

## Frontend Testing (Vitest)

### Running Tests

```bash
cd views/templates

# Run all tests
pnpm test

# Run in watch mode
pnpm test -- --watch

# Run specific test file
pnpm test -- src/storage/storage.test.ts

# Run with coverage
pnpm test -- --coverage

# Run tests matching pattern
pnpm test -- --grep "Notification"
```

### Test Configuration

**vitest.config.ts**
```typescript
import { defineConfig } from "vitest/config";

export default defineConfig({
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: ["./tests/setup.ts"],
    include: ["src/**/*.test.ts"],
    coverage: {
      provider: "v8",
      reporter: ["text", "json", "html"],
    },
  },
});
```

### Test Structure

**Unit Tests** - Test utilities and components

```typescript
import { describe, it, expect, beforeEach } from "vitest";
import { CookieManager } from "./cookieManager";

describe("CookieManager", () => {
  beforeEach(() => {
    localStorage.clear();
  });

  it("should return null when no consent", () => {
    expect(CookieManager.getConsent()).toBeNull();
  });

  it("should store consent preferences", () => {
    CookieManager.setConsent({
      essential: true,
      analytics: false,
    });
    expect(CookieManager.hasConsent()).toBe(true);
  });
});
```

### Mocked APIs

The test setup (`tests/setup.ts`) provides mocks for:

- **localStorage** - In-memory storage
- **IntersectionObserver** - Mock observer
- **matchMedia** - Media query mocking
- **requestAnimationFrame** - Animation frame mocking
- **scrollTo** - Scroll mocking

### Writing Tests

#### Testing Storage Utilities

```typescript
describe("safeSetItem", () => {
  it("should set value for key", () => {
    safeSetItem("key", "value");
    expect(safeGetItem("key")).toBe("value");
  });

  it("should handle errors gracefully", () => {
    // Mock localStorage to throw
    localStorage.setItem = vi.fn(() => {
      throw new Error("Disabled");
    });

    expect(() => safeSetItem("key", "value")).not.toThrow();
  });
});
```

#### Testing UI Components

```typescript
describe("NotificationManager", () => {
  it("should create notification", () => {
    manager.success("Operation completed");
    
    const notification = document.querySelector(".alert-success");
    expect(notification).toBeTruthy();
    expect(notification?.textContent).toContain("Operation completed");
  });

  it("should auto-remove after duration", () => {
    vi.useFakeTimers();
    manager.show({ type: "success", message: "Test", duration: 100 });
    
    vi.advanceTimersByTime(100);
    expect(document.querySelector(".alert")).toBeFalsy();
    
    vi.useRealTimers();
  });
});
```

#### Testing Effects

```typescript
describe("fadeIn", () => {
  it("should animate element", async () => {
    const element = document.createElement("div");
    document.body.appendChild(element);

    vi.useFakeTimers();
    const promise = fadeIn(element, { duration: 300 });
    vi.advanceTimersByTime(300);
    await promise;

    expect(element.animate).toHaveBeenCalled();
    vi.useRealTimers();
  });
});
```

## Test Coverage Goals

### Backend (PHP)

| Component | Target | Current |
|-----------|--------|---------|
| Domain Layer | 90% | - |
| UseCase Layer | 85% | - |
| Infrastructure | 80% | - |
| Presentation | 70% | - |

### Frontend (TypeScript)

| Module | Target | Current |
|--------|--------|---------|
| Storage | 90% | ✅ |
| UI Components | 85% | ✅ |
| Effects | 80% | ✅ |
| Navigation | 80% | ✅ |
| Core | 90% | ✅ |

## Best Practices

### General

1. **Test names should describe behavior**
   ```typescript
   // ❌ Bad
   it("should work", () => { });

   // ✅ Good
   it("should return null when no consent given", () => { });
   ```

2. **Arrange-Act-Assert pattern**
   ```typescript
   it("should update visit count", () => {
     // Arrange
     CookieManager.setUserInfo({ visitCount: 5 });

     // Act
     const info = CookieManager.incrementVisit();

     // Assert
     expect(info.visitCount).toBe(6);
   });
   ```

3. **Test edge cases**
   - Empty inputs
   - Null/undefined values
   - Error conditions
   - Boundary values

### Backend (PHP)

```php
// ✅ Use descriptive assertions
$this->assertEquals(expected, actual);
$this->assertTrue(condition);
$this->assertCount(3, $array);

// ✅ Test exceptions
$this->expectException(InvalidArgumentException::class);

// ✅ Use data providers for multiple scenarios
/**
 * @dataProvider validationProvider
 */
public function testValidation($input, $expected): void {
    // ...
}
```

### Frontend (TypeScript)

```typescript
// ✅ Test user interactions
it("should close on button click", () => {
  showToast("Test");
  document.querySelector(".toast-close")?.click();
  expect(document.querySelector(".app-toast")?.classList.contains("fade-out")).toBe(true);
});

// ✅ Test async code
it("should load data", async () => {
  await loadData();
  expect(container.textContent).toContain("Loaded");
});

// ✅ Use fake timers for time-based tests
vi.useFakeTimers();
// ... test code ...
vi.useRealTimers();
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test-php:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install
      - run: APP_ENV=test composer test

  test-frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: pnpm/action-setup@v2
      - run: cd views/templates && pnpm install
      - run: cd views/templates && pnpm test
```

## Test Files Checklist

### Backend

- [x] `tests/Unit/Infrastructure/View/AssetHelperTest.php`
- [x] `tests/Unit/Domain/Post/PostTest.php`
- [x] `tests/Unit/Infrastructure/SearchServiceTest.php`
- [x] `tests/Unit/UseCase/Post/CreatePostTest.php`

### Frontend

- [x] `src/storage/storage.test.ts`
- [x] `src/storage/cookieManager.test.ts`
- [x] `src/ui/notifications.test.ts`
- [x] `src/ui/toast.test.ts`
- [x] `src/effects/motion.test.ts`
- [x] `src/navigation/smoothScroll.test.ts`
- [x] `src/core/csrf.test.ts`

## Commands Quick Reference

```bash
# PHP Backend
composer test                    # Run all tests
composer test -- --coverage      # With coverage
./vendor/bin/codecept run Unit   # Unit tests only

# Frontend
cd views/templates
pnpm test                        # Run tests
pnpm test -- --watch             # Watch mode
pnpm test -- --coverage          # With coverage
pnpm test -- src/storage/*.test.ts  # Specific files
```

## Resources

- [Vitest Documentation](https://vitest.dev/)
- [Codeception Documentation](https://codeception.com/)
- [Testing Library](https://testing-library.com/)
- [PHP Testing Best Practices](https://phptherightway.com/pages/Testing.html)
