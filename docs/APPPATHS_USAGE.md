# AppPaths Usage Guide

## ✅ All Paths Now Use AppPaths

All hardcoded paths (`__DIR__`, `dirname()`, `../..`) have been replaced with **AppPaths** for consistent path resolution.

---

## 📁 Before vs After

### ❌ Before (Hardcoded)
```php
// LocalStorageProvider
$this->basePath = __DIR__ . '/../../../../storage/uploads';

// InvalidateCacheStep  
$cacheFile = __DIR__ . '/../../../../storage/cache/templates/' . md5($key);

// ArticleController
$basePath = dirname(__DIR__, 3);
$this->renderer = new TemplateRenderer(
    $basePath . '/interfaces/Templates/frontend',
    $basePath . '/storage/cache/templates',
    ...
);

// DatabaseConnection
$basePath = dirname(__DIR__, 3);
$fullPath = $basePath . '/' . $dbPath;
```

### ✅ After (AppPaths)
```php
// LocalStorageProvider
$paths = AppPaths::instance();
$this->basePath = $paths->storage('uploads');

// InvalidateCacheStep
$paths = AppPaths::instance();
$cacheDir = $paths->cache('templates');
$cacheFile = $cacheDir . '/' . md5($key);

// ArticleController
$paths = AppPaths::instance();
$this->renderer = new TemplateRenderer(
    $paths->templates('frontend'),
    $paths->cache('templates'),
    ...
);

// DatabaseConnection
$paths = AppPaths::instance();
$fullPath = $paths->getBasePath() . '/' . $dbPath;
```

---

## 🎯 AppPaths Methods

```php
$paths = AppPaths::instance();

// Base paths
$paths->getBasePath();              // /path/to/project

// Domain directories
$paths->domain();                   // /path/to/project/src/domain
$paths->application();              // /path/to/project/src/application
$paths->infrastructure();           // /path/to/project/src/infrastructure
$paths->interfaces();               // /path/to/project/src/interfaces

// Data & Storage
$paths->data();                     // /path/to/project/data
$paths->data('cms.db');             // /path/to/project/data/cms.db
$paths->storage();                  // /path/to/project/storage
$paths->storage('uploads');         // /path/to/project/storage/uploads
$paths->storage('logs');            // /path/to/project/storage/logs

// Templates
$paths->templates();                // /path/to/project/src/interfaces/Templates
$paths->templates('frontend');      // /path/to/project/src/interfaces/Templates/frontend
$paths->templates('admin');         // /path/to/project/src/interfaces/Templates/admin

// Cache
$paths->cache();                    // /path/to/project/storage/cache
$paths->cache('templates');         // /path/to/project/storage/cache/templates
```

---

## 📝 Files Updated

| File | Change |
|------|--------|
| `LocalStorageProvider.php` | `__DIR__ . '/../../../../'` → `AppPaths::instance()->storage('uploads')` |
| `InvalidateCacheStep.php` | `__DIR__ . '/../../../../'` → `AppPaths::instance()->cache('templates')` |
| `ArticleController.php` | `dirname(__DIR__, 3)` → `AppPaths::instance()` |
| `DatabaseConnection.php` | `dirname(__DIR__, 3)` → `AppPaths::instance()->getBasePath()` |
| `router.php` | `__DIR__` → `AppPaths::instance()->getBasePath()` |

---

## 🚀 Benefits

1. **Consistency** - All paths resolved the same way
2. **Testability** - Easy to mock in tests
3. **Flexibility** - Change base path in one place
4. **No more `../../../`** - Clean, readable code
5. **Environment aware** - Works in dev/prod/docker

---

## ⚠️ Rules

1. **Never use** `__DIR__`, `dirname()`, `getcwd()` in application code
2. **Always use** `AppPaths::instance()` for path resolution
3. **Exception**: CLI scripts (`bin/*`) can use `__DIR__` for bootstrap
4. **Exception**: `router.php` uses `__DIR__` only for initial bootstrap

---

## 🧪 Testing

```bash
# All paths should resolve correctly
cd /path/to/php-cms
php -r "
require 'vendor/autoload.php';
\$paths = Infrastructure\Paths\AppPaths::instance();
echo 'Base: ' . \$paths->getBasePath() . PHP_EOL;
echo 'Storage: ' . \$paths->storage('uploads') . PHP_EOL;
echo 'Templates: ' . \$paths->templates('frontend') . PHP_EOL;
"
```

---

*Last updated: 2026-02-27*
