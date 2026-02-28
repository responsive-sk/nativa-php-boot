# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Acceptance test suite with Codeception
- C3 code coverage support
- `DeleteMediaAction` for media deletion
- Settings Actions (`ViewSettingsAction`, `UpdateSettingsAction`)
- Article CRUD Actions (Create, Store, Edit, Update, Delete, Publish)
- Session name configuration via `SESSION_NAME` env variable

### Changed
- **BREAKING**: All controllers migrated to Actions pattern
- **BREAKING**: Database path changed from `/data/` to `/storage/data/`
- `AppPaths::data()` now returns `storage/data/` instead of root `/data/`
- All domain classes marked as `final` to prevent inheritance
- Session cookie name changed from `PHPSESSID` to `nativa_session`
- Updated `.env.example` with new database path convention

### Removed
- **BREAKING**: `ArticleController` (Admin) - use Article Actions instead
- **BREAKING**: `SettingsController` (Admin) - use Settings Actions instead
- **BREAKING**: `MediaController` (Admin) - use Media Actions instead
- **BREAKING**: `PageController` (Admin & Frontend) - use Pages Actions instead
- **BREAKING**: `DashboardController` (Admin) - use DashboardAction instead
- **BREAKING**: `FormController` (Admin & Frontend) - use Forms Actions instead
- **BREAKING**: `HomeController` (Frontend) - use HomeAction instead
- **BREAKING**: `ContactController` (Frontend) - use ContactAction instead
- **BREAKING**: `ArticleController` (Frontend) - use Article Actions instead

### Fixed
- `DatabaseConnection` now properly handles `:memory:` SQLite for tests
- `DatabaseConnectionManager` returns existing connections before checking config
- Permission format validation regex in `CreatePermissionAction`
- Magic number in `SessionManager` (replaced `1` with `PHP_SESSION_NONE`)
- Intended URL redirect after login restored
- `EditRoleAction` and `EditPermissionAction` handle() method signatures

### Security
- Added `final` keyword to prevent class inheritance attacks
- Session configuration via environment variables

---

## [2026-02-27] - Controller to Actions Migration

### Added
- Roles & Permissions CRUD Actions
- RBAC management UI
- `PermissionMiddleware` for granular permissions
- `RbacService`, `RoleService`, `PermissionService`
- User-Role and Permission-Role pivot tables
- `User::hasPermission()` with caching
- `User::assignRole()` with duplicate prevention
- `Role::hasPermission()` method

### Changed
- Migrated admin routes to Actions pattern
- Updated authentication flow
- Session management refactored

### Fixed
- Reflection usage in `RbacService` replaced with domain methods
- Silent catch blocks now log errors

---

## [2026-02-26] - AppPaths & Storage Cleanup

### Added
- `AppPaths::instance()` singleton pattern
- Consistent path resolution across application
- Storage directory structure documentation

### Changed
- All hardcoded paths replaced with `AppPaths`
- Database configuration centralized

### Fixed
- Path resolution inconsistencies
- Database connection initialization

---

## [2026-02-25] - Native PHP Templates

### Added
- `TemplateRenderer` for native PHP templates
- Layout support with `admin/layouts/base.php` and `frontend/layouts/base.php`
- Template helper methods (`$this->e()`, `$this->date()`, etc.)

### Removed
- Twig templating engine dependency

### Changed
- All `.twig` templates converted to `.php`
- Template compilation no longer required

---

## [Pre-2026-02-25] - Initial Setup

### Added
- DDD architecture with 4 layers
- Custom Router implementation
- SQLite database with migrations
- Article, Page, Form, Media, Contact management
- Admin panel
- Frontend website
- PHPUnit + Codeception testing

---

## Migration Guides

### Controllers to Actions (2026-02-28)

**Before:**
```php
// Kernel.php
$this->router->get('/admin/articles', [ArticleController::class, 'index']);
```

**After:**
```php
// Kernel.php
$this->router->get('/admin/articles', ArticlesAction::class);
```

### Database Path Change (2026-02-28)

**Before:**
```env
DB_CMS=data/cms.db  # → /project/data/cms.db
```

**After:**
```env
DB_CMS=cms.db  # → /project/storage/data/cms.db
```

### Final Classes (2026-02-28)

**Before:**
```php
class User {
    public function isAdmin(): bool { ... }
}

// Could be extended
class CustomUser extends User { ... }
```

**After:**
```php
final class User {
    public function isAdmin(): bool { ... }
}

// ❌ Compilation error
class CustomUser extends User { ... }
```

---

## Version History

| Date | Version | Major Changes |
|------|---------|---------------|
| 2026-02-28 | 2.0.0 | Final classes, Complete Actions migration, AppPaths cleanup |
| 2026-02-27 | 1.5.0 | RBAC implementation, Roles & Permissions |
| 2026-02-26 | 1.4.0 | AppPaths refactoring |
| 2026-02-25 | 1.3.0 | Native PHP templates (no Twig) |
| Pre-2026 | 1.0.0 | Initial DDD CMS release |

---

*Last updated: 2026-02-28*
