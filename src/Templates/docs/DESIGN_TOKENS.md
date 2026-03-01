# Design Tokens

Tento dokument popisuje všetky CSS custom properties používané v Luxury theme.

## Obsah

1. [Brand Colors](#brand-colors)
2. [Theme Colors](#theme-colors)
3. [Typography](#typography)
4. [Spacing](#spacing)
5. [Layout](#layout)
6. [Border Radius](#border-radius)
7. [Shadows](#shadows)
8. [Transitions](#transitions)
9. [Z-Index](#z-index)
10. [Border Width](#border-width)
11. [Line Height](#line-height)
12. [Letter Spacing](#letter-spacing)
13. [Focus Ring](#focus-ring)
14. [Overlay](#overlay)
15. [Icon Size](#icon-size)
16. [Grid Gap](#grid-gap)
17. [Aspect Ratio](#aspect-ratio)

---

## Brand Colors

**Fixné hodnoty** - nikdy sa nemenia (dark/light theme).

```css
:root {
    --brand-gold: #d4af37;
    --brand-gold-light: #e5c559;
    --brand-gold-dark: #b8941f;
    --brand-emerald: #10b981;
    --brand-ruby: #ef4444;
    --brand-sapphire: #3b82f6;
}
```

### Použitie

```css
.btn--primary {
    background: var(--brand-gold);
}

.text-success {
    color: var(--brand-emerald);
}

.text-error {
    color: var(--brand-ruby);
}
```

---

## Theme Colors

**Flipujúce hodnoty** - menia sa podľa `data-theme`.

```css
:root {
    --color-bg: #0a0a0a;
    --color-bg-alt: #1a1a1a;
    --color-bg-hover: #252525;
    --color-bg-tooltip: #1c212f;
    --color-border: #2d2d2d;
    --color-text: #ffffff;
    --color-text-muted: #a1a1aa;
    --color-text-inverse: #0a0a0a;
}

[data-theme="light"] {
    --color-bg: #ffffff;
    --color-bg-alt: #f8fafc;
    --color-bg-hover: #f1f5f9;
    --color-bg-tooltip: #1e293b;
    --color-border: #e2e8f0;
    --color-text: #1e293b;
    --color-text-muted: #64748b;
    --color-text-inverse: #ffffff;
}
```

### Použitie

```css
.card {
    background: var(--color-bg-alt);
    color: var(--color-text);
    border: 1px solid var(--color-border);
}
```

---

## Typography

```css
:root {
    --font-sans: "Inter", system-ui, -apple-system, sans-serif;
    --font-serif: "Playfair Display", Georgia, serif;
    --font-mono: "JetBrains Mono", monospace;

    --text-xs: 0.75rem;
    --text-sm: 0.875rem;
    --text-base: 1rem;
    --text-lg: 1.125rem;
    --text-xl: 1.25rem;
    --text-2xl: 1.5rem;
    --text-3xl: 2rem;
    --text-4xl: 2.5rem;
    --text-5xl: 3rem;

    --leading-tight: 1.25;
    --leading-normal: 1.5;
    --leading-relaxed: 1.75;

    --tracking-tight: -0.025em;
    --tracking-normal: 0;
    --tracking-wide: 0.025em;
}
```

---

## Spacing

```css
:root {
    --space-1: 4px;
    --space-2: 8px;
    --space-3: 16px;
    --space-4: 24px;
    --space-5: 32px;
    --space-6: 48px;
    --space-8: 64px;
    --space-10: 80px;
    --space-12: 96px;
}
```

### Použitie

```css
.card {
    padding: var(--space-4);
    gap: var(--space-3);
}
```

---

## Layout

```css
:root {
    --container-max: 1400px;
    --header-height: 70px;
}
```

---

## Border Radius

```css
:root {
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;
}
```

---

## Shadows

```css
:root {
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
    --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.3);
    --shadow-inner: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    --shadow-gold: 0 4px 14px rgba(212, 175, 55, 0.3);
}
```

---

## Transitions

```css
:root {
    --transition-fast: 150ms ease;
    --transition-normal: 250ms ease;
    --transition-slow: 350ms ease;
}
```

### Použitie

```css
.btn {
    transition: all var(--transition-normal);
}
```

---

## Z-Index

```css
:root {
    --z-base: 0;
    --z-dropdown: 100;
    --z-sticky: 200;
    --z-fixed: 300;
    --z-modal-backdrop: 400;
    --z-modal: 500;
    --z-popover: 600;
    --z-tooltip: 700;
}
```

---

## Border Width

```css
:root {
    --border-thin: 1px;
    --border-medium: 2px;
    --border-thick: 4px;
}
```

### Použitie

```css
.input {
    border: var(--border-thin) solid var(--color-border);
}

.divider {
    border-bottom: var(--border-medium) solid var(--color-border);
}
```

---

## Focus Ring

```css
:root {
    --focus-ring: 0 0 0 2px var(--color-bg), 0 0 0 4px var(--brand-gold);
    --focus-ring-inset: inset 0 0 0 2px var(--brand-gold);
}
```

### Použitie

```css
.btn:focus-visible {
    outline: none;
    box-shadow: var(--focus-ring);
}
```

---

## Overlay

```css
:root {
    --overlay-bg: rgba(0, 0, 0, 0.5);
    --overlay-bg-dark: rgba(0, 0, 0, 0.7);
    --overlay-bg-light: rgba(255, 255, 255, 0.9);
}
```

### Použitie

```css
.modal-overlay {
    background: var(--overlay-bg);
}

.lightbox-overlay {
    background: var(--overlay-bg-dark);
}
```

---

## Icon Size

```css
:root {
    --icon-xs: 12px;
    --icon-sm: 16px;
    --icon-md: 20px;
    --icon-lg: 24px;
    --icon-xl: 32px;
}
```

### Použitie

```css
.icon {
    width: var(--icon-md);
    height: var(--icon-md);
}
```

---

## Grid Gap

```css
:root {
    --gap-xs: 4px;
    --gap-sm: 8px;
    --gap-md: 16px;
    --gap-lg: 24px;
    --gap-xl: 32px;
    --gap-2xl: 48px;
}
```

### Použitie

```css
.grid {
    display: grid;
    gap: var(--gap-lg);
}
```

---

## Aspect Ratio

```css
:root {
    --aspect-square: 1 / 1;
    --aspect-video: 16 / 9;
    --aspect-portrait: 3 / 4;
    --aspect-wide: 21 / 9;
}
```

### Použitie

```css
.card-image {
    aspect-ratio: var(--aspect-video);
    object-fit: cover;
}

.avatar {
    aspect-ratio: var(--aspect-square);
    border-radius: var(--radius-full);
}
```

---

## Best Practices

### Oddelenie brand vs theme

Vždy oddeľujte:

- **Brand** (`--brand-*`) - fixné, nikdy sa nemenia
- **Theme** (`--color-*`) - flipujú pri zmene theme

### Konzistentnosť

Nikdy nepoužívajte raw hodnoty:

```css
/* Zle */
padding: 16px;

/* Dobre */
padding: var(--space-3);
```

### Komponenty

Definovanie komponentových premenných:

```css
.card {
    --card-padding: var(--space-4);
    --card-radius: var(--radius-lg);
    --card-shadow: var(--shadow-md);

    padding: var(--card-padding);
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
}
```
