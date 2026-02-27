# Feature: Admin Authentication & RBAC System

## Overview

Implement complete authentication and authorization system for admin panel with Role-Based Access Control (RBAC).

## Requirements

### 1. Authentication System
- **Login** - Email/password authentication with session management
- **Register** - Admin user registration (protected by secret key)
- **Logout** - Session termination
- **Remember Me** - Persistent login via cookie
- **Password Reset** - Email-based password recovery
- **Profile Management** - View/edit user profile, change password

### 2. Authorization (RBAC)
- **Roles** - admin, editor, viewer
- **Permissions** - Granular access control for resources
- **Middleware** - Protect admin routes with auth checks
- **UI Helpers** - Show/hide elements based on permissions

### 3. Security Features
- Password hashing (password_hash with PASSWORD_DEFAULT)
- CSRF protection for forms
- Session fixation prevention
- Rate limiting for login attempts
- Secure cookie flags (HttpOnly, Secure, SameSite)

## Tech Stack

- **Language:** PHP 8.4+
- **Database:** SQLite (existing `data/cms.db`)
- **Session:** PHP native sessions with custom handler
- **Password:** `password_hash()` / `password_verify()`
- **Logging:** Verbose (DEBUG level)

## Implementation Plan

### Phase 1: Domain Layer

#### 1.1 User Entity & Value Objects
**Files:**
- `domain/Model/User.php` - User entity with roles
- `domain/ValueObjects/Password.php` - Password value object
- `domain/ValueObjects/Role.php` - Role value object (admin, editor, viewer)

**User Entity Methods:**
```php
- create(string $name, Email $email, Password $password, Role $role): self
- hasRole(Role $role): bool
- hasPermission(string $permission): bool
- changePassword(Password $newPassword): void
- updateProfile(string $name, ?string $avatar): void
```

#### 1.2 Role & Permission Entities
**Files:**
- `domain/Model/Role.php` - Role entity
- `domain/Model/Permission.php` - Permission entity
- `domain/ValueObjects/PermissionName.php` - Permission VO

**Permissions:**
```php
// Admin
'admin.dashboard'      // View dashboard
'admin.users.manage'   // Manage users
'admin.settings.manage' // Manage settings

// Articles
'admin.articles.view'
'admin.articles.create'
'admin.articles.edit'
'admin.articles.delete'
'admin.articles.publish'

// Pages
'admin.pages.view'
'admin.pages.create'
'admin.pages.edit'
'admin.pages.delete'
'admin.pages.publish'

// Forms
'admin.forms.view'
'admin.forms.create'
'admin.forms.edit'
'admin.forms.delete'
'admin.forms.submissions.view'

// Media
'admin.media.view'
'admin.media.upload'
'admin.media.delete'
```

#### 1.3 Repository Interfaces
**Files:**
- `domain/Repository/UserRepositoryInterface.php`
- `domain/Repository/RoleRepositoryInterface.php`
- `domain/Repository/PermissionRepositoryInterface.php`

#### 1.4 Domain Events
**Files:**
- `domain/Events/UserLoggedIn.php`
- `domain/Events/UserLoggedOut.php`
- `domain/Events/UserRegistered.php`
- `domain/Events/PasswordChanged.php`
- `domain/Events/PasswordResetRequested.php`

---

### Phase 2: Application Layer

#### 2.1 Authentication Service
**Files:**
- `application/Services/AuthService.php` - Main auth logic
- `application/Services/SessionManager.php` - Session handling
- `application/Services/TokenManager.php` - Remember me token

**AuthService Methods:**
```php
- authenticate(string $email, string $password): bool
- login(User $user, bool $rememberMe = false): void
- logout(): void
- user(): ?User
- check(): bool
- attempt(array $credentials, bool $rememberMe = false): bool
```

#### 2.2 User Management Service
**Files:**
- `application/Services/UserAppService.php`
- `application/DTOs/CreateUserCommand.php`
- `application/DTOs/UpdateUserCommand.php`
- `application/DTOs/LoginCommand.php`
- `application/DTOs/PasswordResetCommand.php`

#### 2.3 RBAC Service
**Files:**
- `application/Services/RbacService.php`
- `application/DTOs/AssignRoleCommand.php`
- `application/DTOs/GrantPermissionCommand.php`

**RbacService Methods:**
```php
- hasRole(User $user, string $role): bool
- hasPermission(User $user, string $permission): bool
- assignRole(User $user, Role $role): void
- grantPermission(Role $role, Permission $permission): void
```

#### 2.4 Auth Middleware
**Files:**
- `application/Middleware/AuthMiddleware.php` - Check if authenticated
- `application/Middleware/RoleMiddleware.php` - Check role
- `application/Middleware/PermissionMiddleware.php` - Check permission

---

### Phase 3: Infrastructure Layer

#### 3.1 Database Migrations
**Files:**
- `infrastructure/Persistence/Migrations/CreateUsersTable.php`
- `infrastructure/Persistence/Migrations/CreateRolesTable.php`
- `infrastructure/Persistence/Migrations/CreatePermissionsTable.php`
- `infrastructure/Persistence/Migrations/CreateRoleUserTable.php`
- `infrastructure/Persistence/Migrations/CreatePermissionRoleTable.php`
- `infrastructure/Persistence/Migrations/CreatePasswordResetsTable.php`
- `infrastructure/Persistence/Migrations/CreateRememberMeTable.php`

**SQL Schema:**
```sql
-- Users
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role VARCHAR(50) DEFAULT 'viewer',
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at DATETIME,
    last_login_ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Roles
CREATE TABLE roles (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL, -- admin, editor, viewer
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Permissions
CREATE TABLE permissions (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL, -- e.g., 'admin.articles.create'
    description TEXT,
    group_name VARCHAR(50), -- 'articles', 'pages', etc.
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Role-User pivot
CREATE TABLE role_user (
    role_id VARCHAR(36) REFERENCES roles(id) ON DELETE CASCADE,
    user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, user_id)
);

-- Permission-Role pivot
CREATE TABLE permission_role (
    permission_id VARCHAR(36) REFERENCES permissions(id) ON DELETE CASCADE,
    role_id VARCHAR(36) REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
);

-- Password resets
CREATE TABLE password_resets (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Remember me tokens
CREATE TABLE remember_tokens (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Login attempts (for rate limiting)
CREATE TABLE login_attempts (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### 3.2 Repository Implementations
**Files:**
- `infrastructure/Persistence/Repositories/UserRepository.php`
- `infrastructure/Persistence/Repositories/RoleRepository.php`
- `infrastructure/Persistence/Repositories/PermissionRepository.php`

#### 3.3 Session Handler
**Files:**
- `infrastructure/Session/DatabaseSessionHandler.php` - DB-backed sessions

#### 3.4 Security Utilities
**Files:**
- `infrastructure/Security/CsrfTokenManager.php`
- `infrastructure/Security/RateLimiter.php`
- `infrastructure/Security/PasswordResetTokenGenerator.php`

---

### Phase 4: Interfaces Layer

#### 4.1 Auth Actions (Controllers)
**Files:**
- `interfaces/HTTP/Actions/Auth/LoginAction.php` - GET/POST login
- `interfaces/HTTP/Actions/Auth/LogoutAction.php` - POST logout
- `interfaces/HTTP/Actions/Auth/RegisterAction.php` - GET/POST register
- `interfaces/HTTP/Actions/Auth/ForgotPasswordAction.php` - GET/POST
- `interfaces/HTTP/Actions/Auth/ResetPasswordAction.php` - GET/POST
- `interfaces/HTTP/Actions/Auth/ProfileAction.php` - GET/POST profile
- `interfaces/HTTP/Actions/Auth/ChangePasswordAction.php` - POST

#### 4.2 Admin Middleware
**Files:**
- `interfaces/HTTP/Middleware/AdminAuthMiddleware.php`
- `interfaces/HTTP/Middleware/CheckRoleMiddleware.php`
- `interfaces/HTTP/Middleware/CheckPermissionMiddleware.php`

#### 4.3 Templates
**Files:**
- `interfaces/Templates/auth/login.php`
- `interfaces/Templates/auth/register.php`
- `interfaces/Templates/auth/forgot-password.php`
- `interfaces/Templates/auth/reset-password.php`
- `interfaces/Templates/auth/profile.php`
- `interfaces/Templates/admin/layouts/auth-base.php` (optional layout)

#### 4.4 Routes
**Add to `interfaces/HTTP/Kernel.php`:**

```php
// Public Auth Routes
$this->router->get('/login', LoginAction::class);
$this->router->post('/login', LoginAction::class);
$this->router->get('/register', RegisterAction::class);
$this->router->post('/register', RegisterAction::class);
$this->router->get('/forgot-password', ForgotPasswordAction::class);
$this->router->post('/forgot-password', ForgotPasswordAction::class);
$this->router->get('/reset-password/{token}', ResetPasswordAction::class);
$this->router->post('/reset-password', ResetPasswordAction::class);

// Protected Routes (with middleware)
$this->router->get('/logout', LogoutAction::class);
$this->router->get('/profile', ProfileAction::class);
$this->router->post('/profile', ProfileAction::class);
$this->router->post('/change-password', ChangePasswordAction::class);

// Admin Routes - Protected with AdminAuthMiddleware
// All existing /admin/* routes will be protected
```

#### 4.5 Kernel Updates
Update `Kernel::handle()` to support middleware:
```php
public function handle(Request $request): Response
{
    // Run middleware before routing
    $middlewareResponse = $this->runMiddleware($request);
    if ($middlewareResponse !== null) {
        return $middlewareResponse;
    }
    
    // ... existing routing logic
}
```

---

### Phase 5: Database Seeder

#### 5.1 Initial Data
**Files:**
- `infrastructure/Persistence/Seeders/RoleSeeder.php`
- `infrastructure/Persistence/Seeders/PermissionSeeder.php`
- `infrastructure/Persistence/Seeders/UserSeeder.php`

**Default Roles:**
1. **admin** - All permissions
2. **editor** - Content management (articles, pages, forms, media)
3. **viewer** - Read-only access

**Default Admin User:**
- Email: `admin@phpcms.local`
- Password: `admin123`
- Role: admin

---

### Phase 6: Testing

#### 6.1 Unit Tests
**Files:**
- `tests/Domain/Model/UserTest.php`
- `tests/Domain/ValueObjects/PasswordTest.php`
- `tests/Domain/ValueObjects/RoleTest.php`
- `tests/Application/Services/AuthServiceTest.php`
- `tests/Application/Services/RbacServiceTest.php`

#### 6.2 Integration Tests
**Files:**
- `tests/Integration/Http/Auth/LoginTest.php`
- `tests/Integration/Http/Auth/LogoutTest.php`
- `tests/Integration/Http/Auth/RegistrationTest.php`
- `tests/Integration/Http/Auth/PasswordResetTest.php`
- `tests/Integration/Http/Admin/AuthGuardTest.php`
- `tests/Integration/Http/Admin/RbacTest.php`

---

## File Structure Summary

```
src/
├── domain/
│   ├── Model/
│   │   ├── User.php (update)
│   │   ├── Role.php
│   │   └── Permission.php
│   ├── ValueObjects/
│   │   ├── Password.php
│   │   ├── Role.php
│   └── PermissionName.php
│   ├── Repository/
│   │   ├── UserRepositoryInterface.php (update)
│   │   ├── RoleRepositoryInterface.php
│   │   └── PermissionRepositoryInterface.php
│   └── Events/
│       ├── UserLoggedIn.php
│       ├── UserLoggedOut.php
│       ├── UserRegistered.php
│       ├── PasswordChanged.php
│       └── PasswordResetRequested.php
│
├── application/
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── SessionManager.php
│   │   ├── TokenManager.php
│   │   ├── UserAppService.php (update)
│   │   └── RbacService.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── PermissionMiddleware.php
│   └── DTOs/
│       ├── CreateUserCommand.php
│       ├── UpdateUserCommand.php
│       ├── LoginCommand.php
│       ├── PasswordResetCommand.php
│       ├── AssignRoleCommand.php
│       └── GrantPermissionCommand.php
│
├── infrastructure/
│   ├── Persistence/
│   │   ├── Migrations/
│   │   │   ├── CreateUsersTable.php
│   │   │   ├── CreateRolesTable.php
│   │   │   ├── CreatePermissionsTable.php
│   │   │   ├── CreateRoleUserTable.php
│   │   │   ├── CreatePermissionRoleTable.php
│   │   │   ├── CreatePasswordResetsTable.php
│   │   │   ├── CreateRememberMeTable.php
│   │   │   └── CreateLoginAttemptsTable.php
│   │   └── Repositories/
│   │       ├── UserRepository.php
│   │       ├── RoleRepository.php
│   │       └── PermissionRepository.php
│   ├── Session/
│   │   └── DatabaseSessionHandler.php
│   ├── Security/
│   │   ├── CsrfTokenManager.php
│   │   ├── RateLimiter.php
│   │   └── PasswordResetTokenGenerator.php
│   └── Container/Providers/
│       ├── AuthServiceProvider.php
│       └── RbacServiceProvider.php
│
└── interfaces/
    ├── HTTP/
    │   ├── Actions/
    │   │   └── Auth/
    │   │       ├── LoginAction.php
    │   │       ├── LogoutAction.php
    │   │       ├── RegisterAction.php
    │   │       ├── ForgotPasswordAction.php
    │   │       ├── ResetPasswordAction.php
    │   │       ├── ProfileAction.php
    │   │       └── ChangePasswordAction.php
    │   └── Middleware/
    │       ├── AdminAuthMiddleware.php
    │       ├── CheckRoleMiddleware.php
    │       └── CheckPermissionMiddleware.php
    └── Templates/
        └── auth/
            ├── login.php
            ├── register.php
            ├── forgot-password.php
            ├── reset-password.php
            └── profile.php
```

---

## Migration Strategy

### Step 1: Backup
```bash
cp data/cms.db data/cms.db.backup
```

### Step 2: Run Migrations
```bash
php bin/cms migrate --group=auth
```

### Step 3: Seed Initial Data
```bash
php bin/cms seed --class=RoleSeeder
php bin/cms seed --class=PermissionSeeder
php bin/cms seed --class=UserSeeder
```

### Step 4: Update Existing Admin Routes
Add middleware protection to all `/admin/*` routes in `Kernel.php`.

---

## Environment Variables

Add to `.env`:
```env
# Authentication
AUTH_SECRET_KEY=your-32-character-secret-key-here
AUTH_SESSION_LIFETIME=120  # minutes
AUTH_REMEMBER_LIFETIME=43200  # 30 days in minutes
AUTH_MAX_LOGIN_ATTEMPTS=5
AUTH_LOCKOUT_TIME=300  # 5 minutes

# Password Reset
AUTH_RESET_TOKEN_LIFETIME=60  # minutes

# Security
SESSION_SECURE_COOKIE=false  # true in production with HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

---

## Security Checklist

- [ ] Password hashing with `PASSWORD_DEFAULT` (bcrypt)
- [ ] CSRF tokens on all forms
- [ ] Rate limiting on login (5 attempts per 5 min)
- [ ] Session fixation prevention (regenerate ID on login)
- [ ] Secure cookie flags (HttpOnly, Secure, SameSite)
- [ ] Password validation (min 8 chars, mixed case, numbers)
- [ ] Email verification for registration (optional)
- [ ] SQL injection prevention (prepared statements)
- [ ] XSS prevention (htmlspecialchars on output)
- [ ] Brute force protection (account lockout)

---

## Testing Checklist

- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Login rate limiting
- [ ] Logout functionality
- [ ] Registration with valid data
- [ ] Registration validation errors
- [ ] Password reset flow
- [ ] Protected routes redirect to login
- [ ] Role-based access control
- [ ] Permission checks
- [ ] Remember me functionality
- [ ] Session security

---

## Rollback Plan

If issues occur:
1. Restore database: `cp data/cms.db.backup data/cms.db`
2. Revert code changes: `git checkout main`
3. Clear sessions: `rm -rf storage/sessions/*`

---

## Success Criteria

1. ✅ Users can register with email/password
2. ✅ Users can login/logout
3. ✅ Admin routes protected with auth middleware
4. ✅ RBAC working (roles, permissions)
5. ✅ Password reset flow functional
6. ✅ Profile management working
7. ✅ All tests passing
8. ✅ No security vulnerabilities

---

*Created: 2026-02-27*
*Branch: feature/admin-auth-rbac*
