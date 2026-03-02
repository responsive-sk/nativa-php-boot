# Templates Refactoring Plan

## Current Structure Analysis

### ✅ USED (Keep)
- `src/init.js` - Core init
- `src/app.ts` - Shared JS entry
- `src/css.ts` - Shared CSS entry
- `src/frontend/use-cases/auth/` - Login/Register styles
- `src/frontend/use-cases/admin/` - Admin styles
- `src/styles/shared/` - Design tokens
- `src/styles/auth/` - Auth styles (duplicate, can be removed)

### ⚠️ PARTIALLY USED (Review)
- `src/frontend/pages/*.ts` - Vite entry points (keep only entries)
- `src/components/` - Only some components used
- `src/ui/` - Only some UI utils used
- `src/navigation/` - Only some nav utils used
- `src/forms/` - Form enhancements (used?)
- `src/effects/` - Effects (used?)

### ❌ UNUSED (Remove)
- `src/frontend/use-cases/home/` - Only CSS, not in Vite config
- `src/frontend/use-cases/blog/` - Only CSS, not in Vite config
- `src/frontend/use-cases/contact/` - Only CSS
- `src/frontend/use-cases/docs/` - Only CSS
- `src/frontend/use-cases/portfolio/` - Only CSS
- `src/frontend/use-cases/pricing/` - Only CSS
- `src/frontend/use-cases/services/` - Only CSS
- `src/frontend/use-cases/not-found/` - Only CSS
- `src/frontend/use-cases/server-error/` - Only CSS
- `src/icons/` - 17k+ icon files, not used
- `src/assets/images/` - Check if used
- `src/core/` - Check if used
- `src/types/` - Check if used
- `src/vendors/` - Check if used
- `src/storage/` - Check if used

## Proposed New Structure

```
src/
├── init.js              # Core init
├── app.ts               # Shared JS
├── css.ts               # Shared CSS
├── styles/
│   ├── shared/          # Design tokens
│   ├── auth/            # Auth styles (login/register)
│   └── admin/           # Admin styles
└── frontend/
    ├── pages/           # Vite entry points only
    │   ├── home.ts
    │   ├── blog.ts
    │   └── ...
    └── use-cases/       # Remove - merge into pages/
```

## Cleanup Steps

1. Remove `src/icons/` - 17k+ unused icon files
2. Remove `src/frontend/use-cases/*/` - Merge into pages or remove
3. Remove `src/styles/auth/` - Duplicate of use-cases/auth
4. Review and remove unused components/effects/forms
5. Update Vite config to use simplified structure
