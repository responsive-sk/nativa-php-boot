# Maintenance Guide

This guide covers maintenance procedures for keeping the codebase clean and optimized.

## Regular Cleanup Tasks

### Identify Unused Files

#### Find Unused CSS Classes

```bash
# Find CSS classes that might be unused
grep -r "\.class-name" src/ --include="*.css" --include="*.php" --include="*.js" --include="*.ts"
```

#### Find Unused JavaScript/TypeScript Files

```bash
# Check if TS/JS files are imported anywhere
find src/ -name "*.ts" -o -name "*.js" | while read file; do
  basename=$(basename "$file" .ts)
  if ! grep -r "import.*$basename" src/ --include="*.ts" --include="*.js" > /dev/null; then
    echo "Potentially unused: $file"
  fi
done
```

#### Check for Unused Components

```bash
# Find components not referenced in templates
find src/components -name "*.ts" -o -name "*.js" | while read file; do
  component=$(basename "$file" .ts | sed 's/-//g')
  if ! grep -r "$component" pages/ layouts/ --include="*.php" > /dev/null; then
    echo "Potentially unused component: $file"
  fi
done
```

### Build Optimization

#### Clean Build Cache

```bash
# Remove Vite cache
rm -rf node_modules/.vite
rm -rf public/assets/.vite

# Rebuild
ppnpm run build
```

#### Bundle Analysis

```bash
# Analyze bundle size
ppnpm run build --analyze
```

### Dependency Management

#### Check for Unused Dependencies

```bash
# Find unused npm packages
pnpm dlx depcheck

# Check for outdated packages
pnpm outdated
```

#### Clean node_modules

```bash
# Remove and reinstall dependencies
rm -rf node_modules pnpm-lock.yaml
pnpm install
```

## File Structure Standards

### Approved Directory Structure

```
src/
├── app.css              # Main CSS entry
├── app.ts               # Main JS entry
├── css.ts              # Vite entry point
├── components/          # Vanilla JS components
├── shared/              # Reusable code
│   └── sections/        # Page sections
├── styles/              # CSS styles
│   ├── components/      # Component CSS
│   ├── shared/          # Shared styles
│   └── use-cases/       # Page-specific CSS
├── utils/               # Utility functions
├── types/               # TypeScript definitions
├── assets/              # Static assets
└── vendors/             # Third-party code
```

### Naming Conventions

#### CSS Classes (BEM)

- Block: `.card`, `.header`, `.nav`
- Element: `.card__title`, `.header__logo`
- Modifier: `.card--featured`, `.nav--active`

#### Files

- CSS: `kebab-case.css`
- TypeScript: `kebab-case.ts`
- JavaScript: `kebab-case.js`

#### Design Tokens

- Colors: `--color-[name]`
- Spacing: `--space-[size]`
- Typography: `--font-[family]`

## Performance Monitoring

### Bundle Size Tracking

```bash
# Monitor bundle size changes
du -sh public/assets/app.js
du -sh public/assets/app.css
```

### Build Time Monitoring

```bash
# Time the build process
time ppnpm run build
```

### Lighthouse Performance

```bash
# Run Lighthouse audit
pnpm dlx lighthouse http://localhost:8008 --output html --output-path ./lighthouse-report.html
```

## Common Issues and Solutions

### Build Errors

#### Import Resolution Issues

```bash
# Check for missing imports
grep -r "import.*from" src/ --include="*.ts" --include="*.js" | grep "\./"
```

#### CSS Import Issues

```bash
# Verify CSS imports in css.ts
grep "import.*\.css" src/css.ts
```

### CSS Issues

#### Unused CSS

```bash
# Find potentially unused CSS
find src/styles -name "*.css" -exec grep -l "^\." {} \;
```

#### Duplicate Selectors

```bash
# Find duplicate CSS selectors
find src/styles -name "*.css" -exec grep -H "^\." {} \; | sort | uniq -d
```

### JavaScript Issues

#### Unused Functions

```bash
# Find exported functions that might be unused
find src/utils -name "*.ts" -exec grep -H "export.*function" {} \;
```

#### Console Logs

```bash
# Find remaining console logs
find src -name "*.ts" -o -name "*.js" -exec grep -H "console\." {} \;
```

## Automated Maintenance

### Pre-commit Hooks

```bash
# Example pre-commit checks
#!/bin/sh
# Check for console logs
if git diff --cached --name-only | xargs grep -l "console\." 2>/dev/null; then
  echo "Remove console logs before committing"
  exit 1
fi

# Check for unused imports
if git diff --cached --name-only | xargs grep -l "import.*luxury" 2>/dev/null; then
  echo "Remove luxury references before committing"
  exit 1
fi
```

### CI/CD Pipeline Checks

```yaml
# Example GitHub Actions
- name: Check for unused files
  run: |
      find src -name "*.ts" -exec bash -c 'if ! grep -r "$(basename "$1" .ts)" src/ --include="*.ts" --include="*.js" > /dev/null; then echo "Unused: $1"; fi' _ {} \;

- name: Build check
  run: ppnpm run build
```

## Documentation Maintenance

### Keep Documentation Updated

- Update AGENTS.md when structure changes
- Document new components in appropriate files
- Update migration guide for major changes
- Keep README files current

### Code Comments

- Document complex CSS logic
- Add TypeScript JSDoc comments
- Explain non-obvious implementation details

## Security Maintenance

### Regular Security Updates

```bash
# Check for security vulnerabilities
pnpm audit

# Fix vulnerabilities
pnpm audit fix
```

### Dependency Updates

```bash
# Update packages
pnpm update

# Check for breaking changes
pnpm outdated
```

## Troubleshooting

### Build Fails

1. Check for syntax errors in TypeScript files
2. Verify all imports resolve correctly
3. Check for circular dependencies
4. Clear build cache and retry

### CSS Not Loading

1. Verify CSS imports in css.ts
2. Check Vite configuration
3. Ensure build output paths are correct
4. Check file permissions

### JavaScript Errors

1. Check browser console for errors
2. Verify all dependencies are installed
3. Check for missing type definitions
4. Ensure proper TypeScript compilation

This maintenance guide should be reviewed and updated quarterly to ensure it remains current with the project's evolving needs.
