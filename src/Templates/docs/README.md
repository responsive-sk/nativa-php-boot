# Frontend Documentation

Complete documentation for the yii-boot frontend architecture.

## 📚 Documentation Index

### Getting Started
- [README.md](../README.md) - Project overview
- [MIGRATION.md](MIGRATION.md) - Migration guide from old architecture
- [MAINTENANCE.md](MAINTENANCE.md) - Maintenance procedures

### Architecture
- [ARCHITECTURE_SEPARATION.md](ARCHITECTURE_SEPARATION.md) - Old vs new code separation
- [BUILD_OPTIMIZATION.md](BUILD_OPTIMIZATION.md) - Build system & performance
- [DESIGN_TOKENS.md](DESIGN_TOKENS.md) - Design tokens & CSS variables

### Development
- [TESTING.md](TESTING.md) - Unit testing with Vitest
- [VISUAL_REGRESSION_TESTING.md](VISUAL_REGRESSION_TESTING.md) - Visual testing with Playwright
- [COMPONENT_LIBRARY.md](COMPONENT_LIBRARY.md) - UI component documentation

### Performance
- [PERFORMANCE.md](PERFORMANCE.md) - Performance optimization guide
- [BUILD_OPTIMIZATION.md](BUILD_OPTIMIZATION.md) - Build optimization

### Components
- [components/](components/) - Component-specific documentation

---

## 🏗️ Architecture Overview

```
src/
├── Entry Points
│   ├── app.ts              # Main JavaScript entry
│   ├── css.ts              # Main CSS entry
│   └── *.ts                # Page-specific entries
│
├── Core Modules
│   ├── core/               # Core utilities (CSRF, etc.)
│   ├── storage/            # localStorage & cookies
│   ├── ui/                 # UI components (alerts, toast, notifications)
│   ├── effects/            # Visual effects (animations, scroll)
│   ├── navigation/         # Navigation (smooth scroll, mobile menu)
│   └── forms/              # Form enhancements
│
├── Styles
│   ├── tokens.css          # Design tokens
│   ├── utilities.css       # Utility classes
│   └── components/         # Component styles (BEM)
│
└── Page-Specific
    ├── use-cases/*/        # Page-specific CSS
    └── *.css               # Page styles
```

---

## 🚀 Quick Start

```bash
# Install dependencies
cd views/templates && pnpm install

# Development server
pnpm run dev

# Build for production
pnpm run build

# Run tests
pnpm test
pnpm test:visual
```

---

## 📋 Key Commands

| Command | Description |
|---------|-------------|
| `pnpm run dev` | Start development server |
| `pnpm run build` | Production build |
| `pnpm run build:dev` | Development build |
| `pnpm test` | Run unit tests |
| `pnpm test:visual` | Run visual regression tests |
| `pnpm run type-check` | TypeScript type checking |
| `pnpm run lint` | ESLint check |
| `pnpm run format` | Prettier check |
| `pnpm run format:fix` | Prettier fix |
| `pnpm run analyze` | Bundle analysis |

---

## 🧪 Testing

### Unit Tests
```bash
pnpm test
pnpm test -- --watch        # Watch mode
pnpm test -- --coverage     # With coverage
```

### Visual Tests
```bash
pnpm test:visual
pnpm test:visual:update     # Update baselines
pnpm test:visual:ui         # Interactive UI
```

---

## 📦 Build Output

```
public/assets/
├── css.[hash].css          # Shared CSS (~25KB, 5KB gzipped)
├── app.[hash].js           # Shared JS (~15KB, 4.5KB gzipped)
├── init.[hash].js          # Theme init (~0.2KB)
└── use-cases/
    ├── home.[hash].css     # Page-specific CSS
    ├── blog.[hash].css
    └── ...
```

---

## 🎨 Design Tokens

All design tokens are defined in `styles/tokens.css`:

```css
:root {
  /* Colors */
  --brand-gold: #d4af37;
  --brand-emerald: #10b981;
  --color-bg: #0a0a0a;
  --color-text: #ffffff;
  
  /* Typography */
  --font-sans: "Inter", system-ui, sans-serif;
  --text-base: 1rem;
  
  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 16px;
  
  /* Effects */
  --radius-md: 10px;
  --shadow-gold: 0 4px 14px rgba(212, 175, 55, 0.3);
}
```

---

## 🧩 Component Patterns

### BEM Naming

```css
/* Block */
.card { }

/* Element */
.card__header { }
.card__title { }

/* Modifier */
.card--featured { }
.card--hover:hover { }
```

### Usage

```html
<article class="card card--featured">
  <header class="card__header">
    <h3 class="card__title">Title</h3>
  </header>
  <div class="card__body">
    Content
  </div>
</article>
```

---

## 📖 Additional Resources

- [Vite Documentation](https://vitejs.dev/)
- [Vitest Documentation](https://vitest.dev/)
- [Playwright Documentation](https://playwright.dev/)
- [BEM Methodology](https://en.bem.info/)

---

**Last Updated:** 2026-02-19  
**Version:** 2.0 (New Architecture)
