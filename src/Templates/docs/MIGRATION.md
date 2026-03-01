# Migration Guide - Luxury to Generic Refactoring

This document describes the complete refactoring process from "luxury" themed naming to generic naming conventions.

## Overview

The project underwent a comprehensive refactoring to remove all "luxury" references and implement clean, generic naming conventions following BEM methodology.

## Changes Made

### 1. CSS Design Tokens

**Before:**

```css
:root {
    --luxury-black: #0a0a0a;
    --luxury-dark: #1a1a2e;
    --luxury-gold: #d4af37;
    --luxury-white-muted: #b0b0b0;
}
```

**After:**

```css
:root {
    --color-black: #0a0a0a;
    --color-dark: #1a1a2e;
    --color-gold: #d4af37;
    --color-white-muted: #b0b0b0;
}
```

### 2. CSS Class Names

**Before:**

```css
.luxury-card {
}
.luxury-header {
}
.luxury-hero {
}
.luxury-sidebar {
}
```

**After:**

```css
.card {
}
.header {
}
.hero {
}
.sidebar {
}
```

### 3. JavaScript/TypeScript Components

**Files Renamed:**

- `luxury-app.js` → `app.js`
- `luxury-landing.js` → `landing.js`
- `luxury-header.ts` → `header.ts`
- `luxury-footer.ts` → `footer.ts`
- `luxury-landing.ts` → `landing.ts`

**Classes Renamed:**

- `LuxuryApp` → `App`
- `LuxuryLanding` → `Landing`
- `luxury-theme` → `app-theme`

### 4. PHP Templates

**Text Content:**

- "Luxury" → "App"
- "hello@luxury.dev" → "hello@app.dev"

**Class Names:**

- `luxury-header` → `header`
- `luxury-logo` → `header__logo`
- `luxury-menu` → `nav`

## Migration Commands

### CSS Token Replacement

```bash
# Replace CSS custom properties
find . -name "*.css" -not -path "./node_modules/*" -not -path "./vendor/*" -exec sed -i 's/--luxury-\([a-zA-Z0-9-]*\)/--color-\1/g' {} \;

# Replace CSS variable usage
find . -name "*.css" -not -path "./node_modules/*" -not -path "./vendor/*" -exec sed -i 's/var(--luxury-\([a-zA-Z0-9-]*\))/var(--color-\1)/g' {} \;
```

### CSS Class Replacement

```bash
# Replace specific class names
find . -name "*.css" -not -path "./node_modules/*" -not -path "./vendor/*" -exec sed -i 's/\.luxury-sidebar/.sidebar/g; s/\.luxury-logo/.header__logo/g; s/\.luxury-menu/.nav/g; s/\.luxury-card/.card/g; s/\.luxury-hero/.hero/g' {} \;
```

### TypeScript/JavaScript Replacement

```bash
# Replace luxury references in TS/JS files
find ./src -name "*.ts" | xargs sed -i 's/luxury/app/g; s/Luxury/App/g; s/luxury-//g; s/LuxuryLanding/Landing/g; s/luxury-theme/app-theme/g'
```

## File Cleanup

### Removed Files

- `src/layout/` - 6 unused Lit Web Components
- `src/shared/components/ui/` - 14 unused Lit Web Components
- `src/lib/` - unused libraries
- `docs/AGENTS_TEMPLATES.md` - outdated documentation
- `docs/BUILD-NOTES.md` - old build notes
- `frontend-design.md` - duplicate documentation
- `use-cases/` - empty directories
- `tests/` - empty test directory

### Build Impact

- `app.js` reduced from 13.91 kB to 12.06 kB (-13%)
- Removed ~40KB of unused TypeScript code
- Cleaner bundle with tree-shaking optimization

## Verification

### Check for Remaining References

```bash
# Check for any remaining luxury references
find ./src -name "*.css" -o -name "*.js" -o -name "*.ts" -o -name "*.php" | xargs grep -l "luxury" | grep -v node_modules | wc -l

# Should return 0
```

### Build Verification

```bash
cd views/templates && pnpm run build

# Should complete without errors
```

## BEM Implementation

All CSS classes now follow BEM (Block Element Modifier) conventions:

```html
<article class="card card--featured">
    <h3 class="card__title">Title</h3>
    <p class="card__description">Description</p>
</article>
```

```css
.card {
}
.card__title {
}
.card--featured {
}
```

## Best Practices

1. **Use generic naming** - Avoid brand-specific prefixes
2. **Follow BEM** - Maintain consistent CSS architecture
3. **Regular cleanup** - Remove unused components and files
4. **Document changes** - Keep migration records for future reference

## Rollback Plan

If rollback is needed:

1. Restore from git commit before migration
2. Run reverse replacement commands
3. Update import statements
4. Rebuild assets

## Future Considerations

- Implement automated linting rules to prevent brand-specific naming
- Set up regular dependency audits
- Consider component library for reusable UI elements
- Implement automated testing for CSS/JS changes
