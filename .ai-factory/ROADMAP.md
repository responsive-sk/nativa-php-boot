# Project Roadmap

> Modern PHP 8.4+ CMS and Blog Platform with DDD architecture, admin panel, form builder, and RBAC system.

## Milestones

### Foundation & Core Architecture
- [x] **DDD Architecture Setup** — Four-layer architecture (Domain, Application, Infrastructure, Interfaces) with proper namespace mapping
- [x] **Database Schema & Migrations** — Complete SQLite schema with users, articles, pages, forms, media, settings, contacts, RBAC tables
- [x] **Custom Router Implementation** — Regex-based router with method override support and route params
- [x] **Template Renderer** — Native PHP template engine with layout support (migrated from Twig)
- [x] **Container & Dependency Injection** — Manual DI container for service instantiation

### Domain Layer
- [x] **Core Entities** — Article, Page, Form, FormSubmission, User, Contact, Media, PageBlock, PageForm, PageMedia
- [x] **Value Objects** — Slug, Email, Content, Status for domain validation
- [x] **Repository Interfaces** — Contracts for all repositories in domain layer
- [x] **Domain Services** — ArticleService, FormService, SlugService for business logic
- [x] **Domain Events** — ArticleCreated, ArticlePublished, FormSubmitted event system
- [x] **RBAC Model** — Role and Permission entities with value objects

### Application Layer
- [x] **Application Services** — ArticleManager, PageManager, FormManager, UserManager, MediaManager, ContactManager
- [x] **DTOs & Commands** — Data transfer objects and command objects for use cases
- [x] **Event Dispatcher** — Application-level event dispatching system
- [x] **Authentication Service** — Session-based auth with login/logout
- [x] **Session Management** — Secure session handling with CSRF protection
- [x] **Form Validation Service** — Validation rules and validators
- [x] **RBAC Services** — RoleService, PermissionService, RbacService for access control
- [x] **Rate Limiter** — Login rate limiting for security
- [x] **Token Manager** — Token generation and validation

### Infrastructure Layer
- [x] **Database Connection** — SQLite PDO connection with singleton pattern
- [x] **UnitOfWork Pattern** — Transaction management for database operations
- [x] **Repository Implementations** — All repositories with proper SQL queries
- [x] **File Storage** — Local storage for media uploads
- [x] **Email/Mailer** — Email notification system

### Frontend (Public Interface)
- [x] **Homepage** — Latest articles display with featured content
- [x] **Article Listing** — Paginated article list with categories and tags
- [x] **Article Detail** — Single article view with related content
- [x] **Tag/Category Filtering** — Filter articles by tag or category
- [x] **Search Functionality** — Article search with query parameters
- [x] **Static Pages** — Dynamic page rendering from database
- [x] **Contact Form** — Contact form with email notifications
- [x] **Custom Forms** — Dynamic form rendering from form builder
- [x] **Error Pages** — 403, 404, 500 error templates

### Admin Panel
- [x] **Dashboard** — Statistics and overview
- [x] **Article CRUD** — Complete article management with create, edit, delete, publish
- [x] **Page CRUD** — Static page management
- [x] **Form Builder** — Dynamic form creation with JSON schema
- [x] **Form Submissions** — View and manage form submissions
- [x] **Media Library** — File upload and media management
- [x] **Settings Management** — Site settings CRUD
- [x] **Role Management** — Create, edit, delete roles
- [x] **Permission Management** — Create, edit, delete permissions

### Security & Quality
- [x] **Security Hardening** — Comprehensive security measures (CSP, CSRF, rate limiting, input validation)
- [x] **PHPStan Configuration** — Zero-error static analysis with baseline
- [x] **Psalm Configuration** — Type checking configuration
- [x] **PHP CS Fixer** — Code style automation
- [x] **Rector** — PHP version upgrades and refactoring
- [x] **PHPUnit Tests** — Unit and integration tests
- [x] **Codeception Tests** — Acceptance tests with C3 coverage

## Completed

| Milestone | Date |
|-----------|------|
| DDD Architecture Setup | 2026-02-27 |
| Database Schema & Migrations | 2026-02-27 |
| Custom Router Implementation | 2026-02-27 |
| Template Renderer | 2026-02-27 |
| Container & Dependency Injection | 2026-02-27 |
| Core Entities | 2026-02-27 |
| Value Objects | 2026-02-27 |
| Repository Interfaces | 2026-02-27 |
| Domain Services | 2026-02-27 |
| Domain Events | 2026-02-27 |
| RBAC Model | 2026-02-27 |
| Application Services | 2026-02-27 |
| DTOs & Commands | 2026-02-27 |
| Event Dispatcher | 2026-02-27 |
| Authentication Service | 2026-02-27 |
| Session Management | 2026-02-27 |
| Form Validation Service | 2026-02-27 |
| RBAC Services | 2026-02-27 |
| Rate Limiter | 2026-02-27 |
| Token Manager | 2026-02-27 |
| Database Connection | 2026-02-27 |
| UnitOfWork Pattern | 2026-02-27 |
| Repository Implementations | 2026-02-27 |
| File Storage | 2026-02-27 |
| Email/Mailer | 2026-02-27 |
| Homepage | 2026-02-27 |
| Article Listing | 2026-02-27 |
| Article Detail | 2026-02-27 |
| Tag/Category Filtering | 2026-02-27 |
| Search Functionality | 2026-02-27 |
| Static Pages | 2026-02-27 |
| Contact Form | 2026-02-27 |
| Custom Forms | 2026-02-27 |
| Error Pages | 2026-02-27 |
| Dashboard | 2026-02-27 |
| Article CRUD | 2026-02-27 |
| Page CRUD | 2026-02-27 |
| Form Builder | 2026-02-27 |
| Form Submissions | 2026-02-27 |
| Media Library | 2026-02-27 |
| Settings Management | 2026-02-27 |
| Role Management | 2026-02-27 |
| Permission Management | 2026-02-27 |
| Security Hardening | 2026-02-27 |
| PHPStan Configuration | 2026-02-27 |
| Psalm Configuration | 2026-02-27 |
| PHP CS Fixer | 2026-02-27 |
| Rector | 2026-02-27 |
| PHPUnit Tests | 2026-02-27 |
| Codeception Tests | 2026-02-27 |

## Next Phase - Enhancements & Production Readiness

### Frontend Modernization
- [ ] **New Frontend Template Integration** — Integrate the new Vite+TypeScript `front` templates with PHP backend
  - Connect PHP TemplateRenderer to build output in `public/assets`
  - Map PHP pages to new frontend pages (home, blog, contact, docs, about, services, portfolio)
  - Integrate Alpine.js and HTMX from new frontend with existing backend functionality
  - Update controllers to use new template paths
  - Ensure build assets (CSS, JS, fonts, images) are properly served
  - Test all page templates with real data from backend

### Content Enhancement
- [ ] **Rich Text Editor** — Integrate WYSIWYG editor (TinyMCE/CKEditor) for article/page content
- [ ] **Markdown Support** — Optional Markdown rendering with preview
- [ ] **Media Embeds** — Embed videos, social media, external content
- [ ] **SEO Features** — Meta tags, Open Graph, sitemap.xml, robots.txt
- [ ] **RSS/Atom Feeds** — Syndication feeds for articles

### User Experience
- [ ] **Comments System** — Article comments with moderation
- [ ] **Newsletter Integration** — Email subscription and campaigns
- [ ] **Social Sharing** — Share buttons and social media integration
- [ ] **Related Content** — Automatic related articles/pages suggestions
- [ ] **Reading Time** — Estimated reading time for articles

### Admin Enhancements
- [ ] **WYSIWYG Form Builder** — Drag-and-drop form builder UI
- [ ] **Bulk Operations** — Bulk delete, publish, archive for articles/pages
- [ ] **Revision History** — Content versioning and rollback
- [ ] **Scheduled Publishing** — Auto-publish articles at scheduled times
- [ ] **User Activity Log** — Audit trail for admin actions

### Performance & Scalability
- [ ] **Caching Layer** — Redis/Memcached integration for query caching
- [ ] **Query Optimization** — Index optimization and query profiling
- [ ] **Lazy Loading** — Optimize template rendering
- [ ] **CDN Integration** — Static asset CDN support
- [ ] **Image Optimization** — Automatic image resizing and WebP conversion

### Production Deployment
- [ ] **Docker Configuration** — Dockerfile and docker-compose for deployment
- [ ] **CI/CD Pipeline** — GitHub Actions/GitLab CI with automated testing
- [ ] **Environment Management** — Production environment configuration
- [ ] **Backup Strategy** — Automated database and media backups
- [ ] **Monitoring & Logging** — Error tracking, performance monitoring
- [ ] **Documentation** — User guide and developer documentation

### Advanced Features
- [ ] **Multi-language Support** — i18n/l10n for content and admin
- [ ] **REST API** — RESTful API for headless CMS usage
- [ ] **GraphQL API** — GraphQL endpoint for flexible queries
- [ ] **Webhooks** — Outgoing webhooks for integrations
- [ ] **Plugin System** — Extensibility via plugins/modules
- [ ] **Theme System** — Customizable frontend themes
- [ ] **Analytics Dashboard** — Built-in analytics and reporting

---

**Progress:** 50/51 core milestones completed (98%)  
**Next Priority:** Frontend Modernization — New Frontend Template Integration
