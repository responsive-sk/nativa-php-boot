# AppPaths Usage Guide

## All Paths Now Use AppPaths

All hardcoded paths (`__DIR__`, `dirname()`, `../..`) have been replaced with **AppPaths** for consistent path resolution.

---

## Project Directory Structure

```
project/
├── src/
│   ├── domain/                 # Domain layer
│   ├── application/            # Application layer
│   ├── infrastructure/         # Infrastructure layer
│   └── interfaces/             # Interfaces layer
├── storage/                    # ALL runtime data
│   ├── data/                   # SQLite databases
│   │   ├── cms.db             # Main application database
│   │   └── jobs.db            # Queue/jobs database
│   ├── cache/                  # Template cache
│   ├── logs/                   # Application logs
│   └── uploads/                # User uploaded files
├── templates/                  # Compiled templates
└── ...
```

### IMPORTANT: Database Location

**All SQLite databases MUST be stored in `storage/data/`:**

- CORRECT: `$paths->data('cms.db')` → `/project/storage/data/cms.db`
- WRONG: `/project/data/cms.db` (no root `data/` directory)

The `data()` method returns `storage/data/`, NOT root `/data/`.

---

## AppPaths Methods

```php
$paths = AppPaths::instance();

// Base paths
$paths->getBasePath();              // /path/to/project

// Domain directories
$paths->domain();                   // /path/to/project/src/domain
$paths->application();              // /path/to/project/src/application
$paths->infrastructure();           // /path/to/project/src/infrastructure
$paths->interfaces();               // /path/to/project/src/interfaces

// Storage & Data (ALL under /storage/)
$paths->storage();                  // /path/to/project/storage
$paths->storage('uploads');         // /path/to/project/storage/uploads
$paths->storage('logs');            // /path/to/project/storage/logs
$paths->storage('cache');           // /path/to/project/storage/cache

$paths->data();                     // /path/to/project/storage/data
$paths->data('cms.db');             // /path/to/project/storage/data/cms.db
$paths->data('jobs.db');            // /path/to/project/storage/data/jobs.db

// Templates
$paths->templates();                // /path/to/project/src/interfaces/Templates
$paths->templates('frontend');      // /path/to/project/src/interfaces/Templates/frontend
$paths->templates('admin');         // /path/to/project/src/interfaces/Templates/admin

// Cache
$paths->cache();                    // /path/to/project/storage/cache
$paths->cache('templates');         // /path/to/project/storage/cache/templates
```

---

## Environment Configuration

**.env example:**
```env
# Database paths are relative to storage/data/
# Since data() returns storage/data/, just use the filename
DB_CMS=cms.db          # → storage/data/cms.db
DB_JOBS=jobs.db        # → storage/data/jobs.db

# Or use absolute paths
DB_CMS=/var/lib/php-cms/cms.db
```

**DO NOT use `data/cms.db`** - this would create `storage/data/data/cms.db`!

---

## Benefits

1. **Consistency** - All paths resolved the same way
2. **Testability** - Easy to mock in tests
3. **Flexibility** - Change base path in one place
4. **No more ../../../** - Clean, readable code
5. **Single storage location** - All runtime data under `/storage/`

---

## Rules

1. **Never use** `__DIR__`, `dirname()`, `getcwd()` in application code
2. **Always use** `AppPaths::instance()` for path resolution
3. **Exception**: CLI scripts (`bin/*`) can use `__DIR__` for bootstrap
4. **Exception**: `router.php` uses `__DIR__` only for initial bootstrap
5. **ALWAYS**: Store databases in `storage/data/`, NOT root `/data/`

---

## Testing

```bash
# All paths should resolve correctly
cd /path/to/php-cms
php -r "
require 'vendor/autoload.php';
\$paths = Infrastructure\Paths\AppPaths::instance();
echo 'Base: ' . \$paths->getBasePath() . PHP_EOL;
echo 'Storage: ' . \$paths->storage('uploads') . PHP_EOL;
echo 'Data: ' . \$paths->data('cms.db') . PHP_EOL;
echo 'Templates: ' . \$paths->templates('frontend') . PHP_EOL;
"
```

**Expected output:**
```
Base: /path/to/php-cms
Storage: /path/to/php-cms/storage/uploads
Data: /path/to/php-cms/storage/data/cms.db
Templates: /path/to/php-cms/src/interfaces/Templates/frontend
```
