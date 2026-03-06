# Component Library

Interactive UI component library for Yii Boot. Built with semantic HTML, BEM CSS architecture, and design tokens.

**Live Demo:** `/docs` route

---

## 🎨 Design Tokens

All components use design tokens defined in [`tokens.css`](./DESIGN_TOKENS.md).

### Colors

#### Brand Colors (Fixed)

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin: 24px 0;">
  <div style="background: #d4af37; padding: 16px; border-radius: 8px;">
    <div style="color: #000; font-weight: 600;">Gold</div>
    <div style="color: #000; font-size: 12px;">#d4af37</div>
  </div>
  <div style="background: #10b981; padding: 16px; border-radius: 8px;">
    <div style="color: #fff; font-weight: 600;">Emerald</div>
    <div style="color: #fff; font-size: 12px;">#10b981</div>
  </div>
  <div style="background: #ef4444; padding: 16px; border-radius: 8px;">
    <div style="color: #fff; font-weight: 600;">Ruby</div>
    <div style="color: #fff; font-size: 12px;">#ef4444</div>
  </div>
  <div style="background: #3b82f6; padding: 16px; border-radius: 8px;">
    <div style="color: #fff; font-weight: 600;">Sapphire</div>
    <div style="color: #fff; font-size: 12px;">#3b82f6</div>
  </div>
</div>

#### Theme Colors

| Token | Dark Theme | Light Theme |
|-------|------------|-------------|
| `--color-bg` | `#0a0a0a` | `#ffffff` |
| `--color-bg-alt` | `#1a1a1a` | `#f8fafc` |
| `--color-text` | `#ffffff` | `#1a1a2e` |
| `--color-text-muted` | `#a1a1aa` | `#4b5563` |
| `--color-border` | `#2d2d2d` | `#e2e8f0` |

### Typography

```css
--font-sans: "Inter", system-ui, -apple-system, sans-serif;
--font-serif: "Playfair Display", Georgia, serif;
--font-mono: "JetBrains Mono", monospace;
```

### Spacing Scale

8px grid system:

| Token | Value | Usage |
|-------|-------|-------|
| `--space-1` | 4px | Tight spacing |
| `--space-2` | 8px | Small gaps |
| `--space-3` | 16px | Base spacing |
| `--space-4` | 24px | Section padding |
| `--space-5` | 32px | Large gaps |
| `--space-6` | 48px | Section margins |
| `--space-8` | 64px | Page sections |

---

## 🧩 Components

### Buttons

Interactive button components with multiple variants.

#### Primary Button

```html
<button class="btn">Primary Action</button>
<a href="#" class="btn">Link Button</a>
```

#### Outline Button

```html
<button class="btn btn--outline">Outline Action</button>
```

#### Secondary Button

```html
<button class="btn btn--secondary">Secondary Action</button>
```

#### Sizes

```html
<button class="btn btn--sm">Small</button>
<button class="btn">Default</button>
<button class="btn btn--lg">Large</button>
```

#### Block Button

```html
<button class="btn btn--block">Full Width Button</button>
```

#### States

```html
<button class="btn">Default</button>
<button class="btn" disabled>Disabled</button>
<button class="btn" aria-busy="true">Loading...</button>
```

---

### Cards

Content containers with consistent styling.

#### Basic Card

```html
<article class="card">
    <header class="card__header">
        <h3 class="card__title">Card Title</h3>
    </header>
    <div class="card__body">
        <p>Card content goes here...</p>
    </div>
</article>
```

#### Interactive Card

```html
<article class="card card--interactive">
    <a href="/link" class="card__link">
        <h3 class="card__title">Clickable Card</h3>
        <p>Entire card is clickable</p>
    </a>
</article>
```

#### Featured Card

```html
<article class="card card--featured">
    <h3 class="card__title">Featured Card</h3>
    <p>Highlighted with gold border</p>
</article>
```

#### Card with Icon

```html
<article class="card">
    <div class="card__icon">
        <svg><!-- icon --></svg>
    </div>
    <h3 class="card__title">Card with Icon</h3>
    <p>Description text</p>
</article>
```

---

### Alerts

Status messages and notifications.

#### Success Alert

```html
<div class="alert alert--success" role="alert">
    <div class="alert__icon">✓</div>
    <div class="alert__message">Operation completed successfully!</div>
    <button class="alert__close" aria-label="Close">×</button>
</div>
```

#### Error Alert

```html
<div class="alert alert--error" role="alert">
    <div class="alert__icon">✗</div>
    <div class="alert__message">Something went wrong</div>
</div>
```

#### Warning Alert

```html
<div class="alert alert--warning" role="alert">
    <div class="alert__icon">⚠</div>
    <div class="alert__message">Please review before continuing</div>
</div>
```

#### Info Alert

```html
<div class="alert alert--info" role="alert">
    <div class="alert__icon">ℹ</div>
    <div class="alert__message">Here's some useful information</div>
</div>
```

---

### Forms

Form elements with consistent styling.

#### Text Input

```html
<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input 
        type="email" 
        id="email" 
        class="form-control" 
        placeholder="you@example.com"
        required
    />
    <div class="form-hint">We'll never share your email</div>
</div>
```

#### Error State

```html
<div class="form-group form-group--error">
    <label for="invalid" class="form-label">Invalid Input</label>
    <input 
        type="text" 
        id="invalid" 
        class="form-control" 
        value="Invalid value"
        aria-invalid="true"
        aria-describedby="error-msg"
    />
    <div class="form-error" id="error-msg">Please enter a valid value</div>
</div>
```

#### Textarea

```html
<div class="form-group">
    <label for="message" class="form-label">Message</label>
    <textarea 
        id="message" 
        class="form-control" 
        rows="4"
        placeholder="Type your message..."
    ></textarea>
</div>
```

#### Select

```html
<div class="form-group">
    <label for="country" class="form-label">Country</label>
    <select id="country" class="form-control">
        <option value="">Select country</option>
        <option value="us">United States</option>
        <option value="uk">United Kingdom</option>
    </select>
</div>
```

#### Checkbox & Radio

```html
<div class="form-group form-group--inline">
    <input type="checkbox" id="remember" class="form-checkbox" />
    <label for="remember" class="form-label">Remember me</label>
</div>

<div class="form-group form-group--inline">
    <input type="radio" id="option1" name="options" class="form-radio" />
    <label for="option1" class="form-label">Option 1</label>
</div>
```

---

### Navigation

#### Breadcrumbs

```html
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/" class="breadcrumbs__link">Home</a>
    <span class="breadcrumbs__separator" aria-hidden="true">/</span>
    <a href="/docs" class="breadcrumbs__link">Docs</a>
    <span class="breadcrumbs__separator" aria-hidden="true">/</span>
    <span class="breadcrumbs__current" aria-current="page">Components</span>
</nav>
```

#### Sidebar Navigation

```html
<nav class="sidebar">
    <div class="sidebar__section">
        <h3 class="sidebar__title">Getting Started</h3>
        <ul class="sidebar__menu">
            <li class="sidebar__item">
                <a href="/docs/intro" class="sidebar__link">Introduction</a>
            </li>
            <li class="sidebar__item">
                <a href="/docs/install" class="sidebar__link sidebar__link--active">Installation</a>
            </li>
        </ul>
    </div>
</nav>
```

---

### Layout

#### Container

```html
<div class="container">
    <!-- Content centered with max-width -->
</div>
```

#### Grid

```html
<div class="grid grid--cols-3">
    <div class="grid__item">Item 1</div>
    <div class="grid__item">Item 2</div>
    <div class="grid__item">Item 3</div>
</div>
```

#### Hero Section

```html
<section class="hero">
    <div class="hero__content">
        <h1 class="hero__title">Page Title</h1>
        <p class="hero__subtitle">Description text goes here</p>
        <div class="hero__actions">
            <button class="btn btn--lg">Primary Action</button>
            <button class="btn btn--outline btn--lg">Secondary</button>
        </div>
    </div>
</section>
```

---

## ♿ Accessibility

### Focus States

All interactive elements have visible focus indicators:

```css
.btn:focus-visible {
    outline: 2px solid var(--brand-gold);
    outline-offset: 2px;
}
```

### ARIA Labels

```html
<button aria-label="Close modal">×</button>
<nav aria-label="Main navigation">...</nav>
<section aria-labelledby="section-title">...</section>
```

### Keyboard Navigation

- All interactive elements are keyboard accessible
- Logical tab order
- Focus trapping in modals
- Escape key closes modals

---

## 📱 Responsive Design

### Breakpoints

```css
/* Mobile first */
@media (min-width: 640px) { /* sm */ }
@media (min-width: 768px) { /* md */ }
@media (min-width: 1024px) { /* lg */ }
@media (min-width: 1280px) { /* xl */ }
```

### Responsive Grid

```html
<div class="grid grid--cols-1 sm:grid--cols-2 lg:grid--cols-3">
    <!-- Responsive columns -->
</div>
```

---

## 🎯 Usage Guidelines

### Do's ✅

- Use design tokens for all values
- Follow BEM naming convention
- Test in both light/dark themes
- Ensure keyboard navigation works
- Add proper ARIA labels
- Use semantic HTML

### Don'ts ❌

- Don't use hardcoded colors/spacing
- Don't skip focus states
- Don't ignore responsive design
- Don't use emojis
- Don't create one-off CSS classes

---

## 🔧 Development

### Adding New Components

1. Create CSS in `src/styles/components/`
2. Follow BEM naming convention
3. Use design tokens
4. Test all themes and states
5. Add documentation here

### Testing

```bash
# Build test
ppnpm run build

# Type checking
ppnpm run type-check

# Visual regression
pnpm test:visual
```

---

## 📖 Resources

- [Design Tokens](./DESIGN_TOKENS.md)
- [Architecture](./ARCHITECTURE_SEPARATION.md)
- [Build Optimization](./BUILD_OPTIMIZATION.md)
- [Testing Guide](./TESTING.md)

---

**Last Updated:** 2026-02-19  
**Version:** 2.0
