# Changelog

All notable changes to the Yii Boot frontend are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [Unreleased]

### Added
- Path aliases for cleaner imports
- Type annotations for event handlers

### Fixed
- TypeScript `this` context errors
- Import path resolution

---

## [2.0.0] - 2026-02-19

### Architecture Changes

#### Added
- **Modular Structure**
  - `core/` - Core utilities (CSRF, etc.)
  - `ui/` - UI components (alerts, toast, notifications)
  - `storage/` - Storage utilities (localStorage, cookies)
  - `effects/` - Visual effects (animations, scroll)
  - `navigation/` - Navigation utilities
  - `forms/` - Form enhancements

- **Testing**
  - Vitest setup with JSDOM
  - 69 passing unit tests
  - Visual regression testing (Playwright)
  - Mock setup for localStorage, IntersectionObserver, etc.

- **Documentation**
  - Component library guide
  - Build optimization guide
  - Testing guide
  - Architecture documentation

#### Changed
- **CSS**: BEM naming convention implemented
- **JS**: Path aliases (`@core`, `@ui`, `@storage`, etc.)
- **Build**: ES2020 target, esbuild minification
- **Bundle**: CSS -28%, JS -43%

#### Removed
- Legacy components (`components/ag/`, `components/sections/`, `components/ui/`)
- Old section components (`shared/sections/`)
- Duplicate CSS files
- Flat `utils/` folder structure

---

## [1.1.0] - 2024-01-17

### Added
- Cookie consent component
- Theme toggle functionality
- HTMX notifications
- Alpine.js integration

### Changed
- Updated design tokens
- Improved dark/light theme switching

---

## [1.0.0] - 2024-01-01

### Added
- Initial frontend architecture
- Vite + TypeScript setup
- BEM CSS architecture
- Design tokens
- Component library

---

## Bundle Size History

| Version | CSS | JS | Gzip CSS | Gzip JS |
|---------|-----|----|----------|---------|
| 2.0.0 | 25.06 KB | 15.14 KB | 5.10 KB | 4.57 KB |
| 1.1.0 | 34.82 KB | 26.64 KB | 6.37 KB | 6.69 KB |

---

**Last Updated:** 2026-02-19  
**Version:** 2.0.0
