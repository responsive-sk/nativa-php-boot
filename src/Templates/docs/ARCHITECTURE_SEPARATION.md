# =============================================================================
# FRONTEND ARCHITECTURE SEPARATION
# How to separate old/new code and test only the new architecture
# =============================================================================

## Directory Structure

```
src/
├── NEW ARCHITECTURE (✅ Keep & Test)
│   ├── app.ts                 # Main JS entry
│   ├── css.ts                 # Main CSS entry  
│   ├── core/                  # Core utilities + tests
│   ├── ui/                    # UI components + tests
│   ├── storage/               # Storage + tests
│   ├── effects/               # Visual effects + tests
│   ├── navigation/            # Navigation + tests
│   ├── forms/                 # Form utilities
│   ├── components/            # Component barrel exports
│   └── styles/                # CSS (tokens, utilities, components)
│
├── PAGE-SPECIFIC (✅ Keep)
│   ├── home.ts, blog.ts, etc. # Page entries
│   └── use-cases/**/*.css     # Page-specific CSS
│
└── LEGACY (❌ Delete or Migrate)
    ├── shared/                # Old section components
    └── assets/                # Static assets (keep if used)
```

## Git Strategy: Separate Old/New Code

### Option 1: Feature Flag Approach

```typescript
// vite.config.ts
const USE_NEW_ARCH = process.env.NEW_ARCH === 'true';

rollupOptions: {
  input: USE_NEW_ARCH ? {
    // New architecture entries
    app: './src/app.ts',
    css: './src/css.ts',
  } : {
    // Legacy entries
    app: './src/legacy-app.ts',
  }
}
```

```bash
# Build new architecture only
NEW_ARCH=true ppnpm run build

# Build legacy
ppnpm run build:legacy
```

### Option 2: Directory-based Separation

```
src/
├── current/           # New architecture (actively developed)
│   ├── app.ts
│   ├── css.ts
│   ├── core/
│   ├── ui/
│   └── ...
│
├── legacy/            # Old code (frozen, to be deleted)
│   ├── old-app.ts
│   ├── old-components/
│   └── ...
│
└── archive/           # Deprecated code (gitignored)
```

```gitignore
# Ignore legacy code in new builds
src/legacy/**
src/archive/**

# But track legacy for migration
!src/legacy/README.md
```

### Option 3: Git Worktree (Recommended)

```bash
# Main branch: new architecture
git checkout main

# Legacy branch: old code (for reference)
git worktree add ../yii-boot-legacy legacy-branch

# Now you have:
# - yii-boot/ (main) - new code only
# - yii-boot-legacy/ - old code for reference
```

## Testing Strategy

### Unit Tests (New Architecture Only)

```typescript
// vitest.config.ts
export default defineConfig({
  test: {
    include: [
      'src/core/**/*.test.ts',
      'src/ui/**/*.test.ts',
      'src/storage/**/*.test.ts',
      'src/effects/**/*.test.ts',
      'src/navigation/**/*.test.ts',
      // Exclude legacy
      '!src/legacy/**',
      '!src/shared/**',
    ],
  },
});
```

```bash
# Run tests for new architecture only
pnpm test

# Test with coverage
pnpm test -- --coverage

# Coverage report shows only new code coverage
```

### Visual Regression Tests

```typescript
// playwright.config.ts
export default defineConfig({
  testDir: './tests/visual',
  // Test only new pages
  testMatch: [
    'home.spec.ts',
    'blog.spec.ts',
    'docs.spec.ts',
  ],
});
```

```bash
# Run visual tests
pnpm test:visual

# Update baselines after intentional changes
pnpm test:visual:update
```

### Integration Tests

```bash
# Build new architecture
NEW_ARCH=true ppnpm run build

# Start server with new build
php -S localhost:8008 -t public

# Run E2E tests
pnpm test:e2e
```

## Migration Checklist

### Phase 1: Identify Used Code ✅
- [x] Remove unused components/ag/
- [x] Remove components/sections/
- [x] Remove components/ui/
- [x] Remove shared/sections/

### Phase 2: Test Coverage
- [ ] Add tests for remaining utilities
- [ ] Reach 80% coverage target
- [ ] Add visual regression tests

### Phase 3: Performance
- [ ] Implement critical CSS
- [ ] Add bundle size limits
- [ ] Setup performance monitoring

### Phase 4: Documentation
- [ ] Document new architecture
- [ ] Create migration guide
- [ ] Update README

## Commands

```bash
# Build new architecture
ppnpm run build

# Test new architecture only
pnpm test
pnpm test:visual

# Analyze bundle (new code only)
ppnpm run analyze

# Check what's imported
pnpm exec madge --circular src/

# Find unused files
pnpm exec depcheck
```

## Monitoring

### Bundle Size Limits

```json
// package.json
"bundleLimits": {
  "css": "30 KB",
  "js": "20 KB",
  "critical": "14 KB"
}
```

```bash
# Check bundle sizes
ppnpm run build && ls -lh public/assets/*.css
```

### Performance Metrics

```bash
# Lighthouse CI
npx @lhci/cli collect
npx @lhci/cli assert
```

## Rollback Plan

If new architecture has issues:

```bash
# 1. Revert to legacy build
git checkout legacy-branch

# 2. Build legacy
ppnpm run build:legacy

# 3. Deploy
```

## Resources

- [Vite Code Splitting](https://vitejs.dev/guide/build.html#code-splitting)
- [Testing Best Practices](https://vitest.dev/guide/best-practices.html)
- [Git Worktree](https://git-scm.com/docs/git-worktree)
