# Templates Root Migration Plan

**Goal:** Centralize all templates under single `Templates/` root with shared Vite build system.

---

## 📐 Target Architecture

```
nativa/
├── Templates/                      ← NEW ROOT for all templates
│   ├── src/                        ← Vite source (TypeScript/CSS)
│   │   ├── core/                   ← Shared utilities (from front/src)
│   │   ├── components/             ← Shared components
│   │   ├── styles/                 ← Global styles + tokens
│   │   ├── frontend/               ← Frontend-specific code
│   │   │   ├── pages/              ← Page entries (home.ts, blog.ts...)
│   │   │   └── use-cases/          ← Page-specific styles
│   │   ├── admin/                  ← Admin-specific code
│   │   │   ├── pages/              ← Admin page entries
│   │   │   └── use-cases/          ← Admin page styles
│   │   ├── app.ts                  ← Shared entry (common code)
│   │   ├── frontend.ts             ← Frontend entry
│   │   └── admin.ts                ← Admin entry
│   │
│   ├── pages/                      ← PHP templates (rendered output)
│   │   ├── frontend/               ← Public pages (from front/pages)
│   │   └── admin/                  ← Admin pages
│   │
│   ├── layouts/                    ← PHP layouts
│   │   ├── frontend.php            ← Frontend layout (from front/layouts/cms.php)
│   │   └── admin.php               ← Admin layout
│   │
│   └── vite.config.ts              ← CENTRAL Vite config
│
├── public/                         ← Vite output
│   └── assets/
│       ├── .vite/manifest.json
│       ├── core/                   ← Shared bundles
│       ├── frontend/               ← Frontend bundles
│       └── admin/                  ← Admin bundles
│
└── src/                            ← PHP backend (DDD)
    └── interfaces/
        └── HTTP/
            ├── Actions/
            └── View/
                └── TemplateRenderer.php  ← Updated paths
```

---

## 📋 Migration Tasks

### Phase 1: Create Structure (15 min)

#### Task 1.1: Create Directories
```bash
cd /home/evan/dev/03/nativa

# Create new structure
mkdir -p Templates/src
mkdir -p Templates/pages/frontend
mkdir -p Templates/pages/admin
mkdir -p Templates/layouts
```

#### Task 1.2: Move Frontend Content
```bash
# Move Vite source code
mv src/interfaces/Templates/front/src/* Templates/src/

# Move PHP pages
mv src/interfaces/Templates/front/pages/* Templates/pages/frontend/

# Move PHP layouts
mv src/interfaces/Templates/front/layouts/* Templates/layouts/

# Move config and docs
mv src/interfaces/Templates/front/vite.config.ts Templates/
mv src/interfaces/Templates/front/package.json Templates/
mv src/interfaces/Templates/front/pnpm-lock.yaml Templates/
mv src/interfaces/Templates/front/tsconfig.json Templates/

# Move documentation
mv src/interfaces/Templates/front/docs Templates/docs
```

---

### Phase 2: Update Vite Config (20 min)

#### Task 2.1: Fix vite.config.ts

**Current:**
```typescript
outDir: resolve(__dirname, "../../../public/assets"),
```

**New:**
```typescript
outDir: resolve(__dirname, "../public/assets"),
```

#### Task 2.2: Update Entry Points

**Current:**
```typescript
input: {
  app: resolve(__dirname, "src/app.ts"),
  home: resolve(__dirname, "src/home.ts"),
  blog: resolve(__dirname, "src/blog.ts"),
  // ...
}
```

**New (organized):**
```typescript
input: {
  // Shared
  "core/init": resolve(__dirname, "src/core/init.ts"),
  "core/app": resolve(__dirname, "src/app.ts"),
  "core/css": resolve(__dirname, "src/css.ts"),
  
  // Frontend pages
  "frontend/home": resolve(__dirname, "src/frontend/pages/home.ts"),
  "frontend/blog": resolve(__dirname, "src/frontend/pages/blog.ts"),
  "frontend/contact": resolve(__dirname, "src/frontend/pages/contact.ts"),
  "frontend/portfolio": resolve(__dirname, "src/frontend/pages/portfolio.ts"),
  "frontend/docs": resolve(__dirname, "src/frontend/pages/docs.ts"),
  "frontend/services": resolve(__dirname, "src/frontend/pages/services.ts"),
  "frontend/pricing": resolve(__dirname, "src/frontend/pages/pricing.ts"),
  
  // Admin pages (future)
  "admin/dashboard": resolve(__dirname, "src/admin/pages/dashboard.ts"),
}
```

#### Task 2.3: Update Output Filenames

```typescript
output: {
  entryFileNames: "[name].[hash].js",
  assetFileNames: (assetInfo) => {
    if (assetInfo.name?.endsWith(".css")) {
      return "[name].[hash].css";
    }
    // ...
  }
}
```

---

### Phase 3: Reorganize Source Code (30 min)

#### Task 3.1: Create Frontend Subdirectory
```bash
cd Templates/src

# Create frontend pages directory
mkdir -p frontend/pages
mkdir -p frontend/use-cases

# Move page entries
mv home.ts frontend/pages/
mv blog.ts frontend/pages/
mv contact.ts frontend/pages/
mv portfolio.ts frontend/pages/
mv docs.ts frontend/pages/
mv services.ts frontend/pages/
mv pricing.ts frontend/pages/

# Move use-cases
mv use-cases/ frontend/
```

#### Task 3.2: Update Imports in Page Entries

**Before (blog.ts):**
```typescript
import "./use-cases/blog/blog.css";
```

**After:**
```typescript
import "../use-cases/blog/blog.css";
// OR
import "./blog.css"; // If CSS is alongside TS
```

#### Task 3.3: Create Admin Placeholder
```bash
mkdir -p src/admin/pages
mkdir -p src/admin/use-cases

# Create admin.ts entry
cat > src/admin.ts << 'EOF'
/* Admin Entry Point
 * Imports: admin page-specific styles
 */

import "./core/admin.css";
EOF
```

---

### Phase 4: Update PHP Templates (20 min)

#### Task 4.1: Update TemplateRenderer.php

**File:** `src/interfaces/HTTP/View/TemplateRenderer.php`

**Current:**
```php
if (str_starts_with($template, 'front/')) {
    $templatePath .= '/front';
    $template = substr($template, 6);
} else {
    $templatePath .= '/frontend';
}
```

**New:**
```php
if (str_starts_with($template, 'frontend/')) {
    $templatePath = $this->templatesPath . '/pages/frontend';
    $template = substr($template, 11);
} elseif (str_starts_with($template, 'admin/')) {
    $templatePath = $this->templatesPath . '/pages/admin';
    $template = substr($template, 6);
} else {
    $templatePath .= '/pages';
}
```

#### Task 4.2: Update Layout Paths

**Current:**
```php
if (str_starts_with($layout, 'front/')) {
    $layoutPath .= '/front/' . substr($layout, 6) . '.php';
}
```

**New:**
```php
if (str_starts_with($layout, 'frontend/')) {
    $layoutPath = $this->templatesPath . '/layouts/frontend.php';
} elseif (str_starts_with($layout, 'admin/')) {
    $layoutPath = $this->templatesPath . '/layouts/admin.php';
}
```

#### Task 4.3: Update All Actions

**Files to update:**
- `src/interfaces/HTTP/Actions/Frontend/HomeAction.php`
- `src/interfaces/HTTP/Actions/Frontend/BlogAction.php`
- `src/interfaces/HTTP/Actions/Frontend/ContactAction.php`
- All other frontend actions

**Change:**
```php
// OLD
'front/pages/home'
'front/layouts/cms'

// NEW
'frontend/home'
'frontend/layouts/frontend'
```

---

### Phase 5: Update AssetHelper (15 min)

#### Task 5.1: Update pageCss() Method

**File:** `src/infrastructure/View/AssetHelper.php`

**Current:**
```php
$pageMap = [
    'home' => 'home.ts',
    'blog' => 'blog.ts',
];
```

**New:**
```php
$pageMap = [
    'home' => 'frontend/home.ts',
    'blog' => 'frontend/blog.ts',
    'contact' => 'frontend/contact.ts',
    'admin/dashboard' => 'admin/dashboard.ts',
];
```

---

### Phase 6: Build & Test (20 min)

#### Task 6.1: Install Dependencies
```bash
cd Templates
pnpm install
```

#### Task 6.2: Build Frontend
```bash
cd Templates
pnpm run build:prod
```

#### Task 6.3: Verify Output
```bash
ls -la public/assets/
# Should see:
# - core/
# - frontend/
# - admin/
```

#### Task 6.4: Test Pages
```bash
# Homepage
curl http://localhost:8000/

# Blog
curl http://localhost:8000/blog

# Article detail
curl http://localhost:8000/blog/welcome-to-php-cms
```

---

## 📁 Files to Change

| File | Change | Priority |
|------|--------|----------|
| `Templates/vite.config.ts` | Fix outDir, entry points | CRITICAL |
| `Templates/src/**/*.ts` | Update imports | CRITICAL |
| `src/interfaces/HTTP/View/TemplateRenderer.php` | Update template paths | CRITICAL |
| `src/infrastructure/View/AssetHelper.php` | Update page map | CRITICAL |
| `src/interfaces/HTTP/Actions/Frontend/*.php` | Update render paths | HIGH |
| `Templates/pages/**/*.php` | Update layout references | MEDIUM |
| `docs/**/*.md` | Update documentation | LOW |

---

## ⚠️ Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Broken imports in TS | Build fails | Test build after each move |
| Wrong template paths | 500 errors | Keep backup, test incrementally |
| Asset paths broken | CSS/JS not loading | Verify manifest.json |
| Git history lost | Can't track changes | Use `git mv` for moves |

---

## ✅ Testing Checklist

- [ ] Vite build completes without errors
- [ ] manifest.json has correct entries
- [ ] Homepage loads with correct CSS/JS
- [ ] Blog listing loads
- [ ] Article detail loads
- [ ] Contact form loads
- [ ] Portfolio page loads
- [ ] Admin dashboard loads (if exists)
- [ ] Theme toggle works
- [ ] Mobile menu works
- [ ] No console errors in browser

---

## 🕐 Estimated Time

| Phase | Time |
|-------|------|
| Phase 1: Create Structure | 15 min |
| Phase 2: Update Vite Config | 20 min |
| Phase 3: Reorganize Source | 30 min |
| Phase 4: Update PHP Templates | 20 min |
| Phase 5: Update AssetHelper | 15 min |
| Phase 6: Build & Test | 20 min |

**Total:** ~2 hours

---

## 🚀 Quick Start Commands

```bash
# 1. Create structure
mkdir -p Templates/{src,pages/{frontend,admin},layouts}

# 2. Move files
mv src/interfaces/Templates/front/src/* Templates/src/
mv src/interfaces/Templates/front/pages/* Templates/pages/frontend/
mv src/interfaces/Templates/front/layouts/* Templates/layouts/
mv src/interfaces/Templates/front/vite.config.ts Templates/
mv src/interfaces/Templates/front/package.json Templates/

# 3. Reorganize source
cd Templates/src
mkdir -p frontend/{pages,use-cases}
mv *.ts frontend/pages/ 2>/dev/null || true
mv use-cases/ frontend/

# 4. Build
cd Templates
pnpm install
pnpm run build:prod

# 5. Test
curl http://localhost:8000/blog
```

---

## 📝 Post-Migration Cleanup

After successful migration:

```bash
# Remove old directory
rm -rf src/interfaces/Templates/front/

# Update documentation
# - AGENTS.md
# - QWEN.md
# - docs/*.md
```

---

**Ready to start?** Say "ideme na to" and I'll begin Phase 1!
