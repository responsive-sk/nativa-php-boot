# Button Component Documentation

The button component provides consistent styling and behavior for all interactive buttons in the application.

## Overview

Buttons are built using CSS classes with BEM methodology and design tokens for consistent styling across themes.

## Basic Usage

```html
<button class="btn">Default Button</button>
<a href="#" class="btn" role="button">Link Button</a>
<input type="submit" class="btn" value="Submit Button" />
```

## Variants

### Primary Button (Default)

The default button style with gold gradient background.

```html
<button class="btn">Primary Button</button>
```

**CSS:**

```css
.btn {
    background: linear-gradient(
        135deg,
        var(--brand-gold),
        var(--brand-gold-dark)
    );
    color: var(--color-text-inverse);
    border: none;
    padding: var(--space-2) var(--space-4);
    border-radius: var(--radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-gold);
}
```

### Outline Button

Button with transparent background and outlined border.

```html
<button class="btn btn-outline">Outline Button</button>
```

**CSS:**

```css
.btn-outline {
    background: transparent;
    border: 1px solid var(--brand-gold);
    color: var(--brand-gold);
    box-shadow: none;
}

.btn-outline:hover {
    background: var(--brand-gold);
    color: var(--color-text-inverse);
}
```

## Sizes

### Small Button

Compact button for secondary actions or space-constrained layouts.

```html
<button class="btn btn-sm">Small Button</button>
<button class="btn btn-outline btn-sm">Small Outline</button>
```

### Default Size

Standard button size for most use cases.

```html
<button class="btn">Default Button</button>
```

### Large Button

Prominent button for primary calls-to-action.

```html
<button class="btn btn-lg">Large Button</button>
<button class="btn btn-outline btn-lg">Large Outline</button>
```

## Layout Modifiers

### Block Button

Full-width button that spans the entire container.

```html
<button class="btn btn-block">Full Width Button</button>
```

## States

### Default State

Normal button appearance.

```html
<button class="btn">Normal State</button>
```

### Hover State

Subtle transform and enhanced shadow on hover.

```css
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
}
```

### Active State

Pressed appearance when clicked.

```css
.btn:active {
    transform: translateY(0);
}
```

### Focus State

Visible focus indicator for keyboard navigation.

```css
.btn:focus-visible {
    outline: none;
    box-shadow: var(--focus-ring);
}
```

### Disabled State

Non-interactive appearance for disabled buttons.

```html
<button class="btn" disabled>Disabled Button</button>
```

```css
.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}
```

## Button Groups

### Horizontal Group

Multiple buttons aligned horizontally.

```html
<div class="btn-group">
    <button class="btn">Primary</button>
    <button class="btn btn-outline">Secondary</button>
    <button class="btn btn-outline">Tertiary</button>
</div>
```

### Vertical Group

Buttons stacked vertically.

```html
<div class="btn-group btn-group--vertical">
    <button class="btn">Option 1</button>
    <button class="btn btn-outline">Option 2</button>
    <button class="btn btn-outline">Option 3</button>
</div>
```

## Combinations

You can combine multiple classes for different button variations:

```html
<!-- Small outline button -->
<button class="btn btn-outline btn-sm">Small Outline</button>

<!-- Large block button -->
<button class="btn btn-lg btn-block">Large Full Width</button>

<!-- Small block outline button -->
<button class="btn btn-outline btn-sm btn-block">
    Small Full Width Outline
</button>
```

## Usage Guidelines

### When to Use

- **Primary buttons**: Main actions on a page (submit forms, start processes)
- **Secondary buttons**: Supporting actions (cancel, back, additional options)
- **Call-to-action**: Prominent buttons to drive user engagement

### Best Practices

#### Do's

- Use primary buttons sparingly (typically one per section)
- Use descriptive text that clearly indicates the action
- Ensure adequate spacing between buttons
- Test button contrast in both light and dark themes
- Make buttons large enough for touch interfaces (minimum 44px height)

#### Don'ts

- Don't use more than one primary button in the same context
- Don't use generic text like "Click here" or "Button"
- Don't make buttons too small for easy interaction
- Don't forget to handle disabled and loading states
- Don't use buttons for navigation (use links instead)

## Accessibility

### Keyboard Navigation

- Buttons are focusable with Tab key
- Activated with Space or Enter key
- Clear focus indicators visible

### Screen Readers

```html
<!-- Button with accessible label -->
<button class="btn" aria-label="Save document">Save</button>

<!-- Button with description -->
<button class="btn" aria-describedby="save-help">Save</button>
<div id="save-help">Saves your current work</div>
```

### Color Contrast

- Primary buttons meet WCAG AA contrast requirements
- Outline buttons maintain sufficient contrast with borders
- Focus states have adequate contrast ratios

## Technical Implementation

### CSS Classes Reference

| Class          | Purpose            | Example                            |
| -------------- | ------------------ | ---------------------------------- |
| `.btn`         | Base button styles | `<button class="btn">`             |
| `.btn-outline` | Outline variant    | `<button class="btn btn-outline">` |
| `.btn-sm`      | Small size         | `<button class="btn btn-sm">`      |
| `.btn-lg`      | Large size         | `<button class="btn btn-lg">`      |
| `.btn-block`   | Full width         | `<button class="btn btn-block">`   |

### Design Tokens Used

| Token               | Value       | Usage                      |
| ------------------- | ----------- | -------------------------- |
| `--brand-gold`      | #d4af37     | Primary button background  |
| `--brand-gold-dark` | #b8941f     | Gradient end color         |
| `--space-2`         | 8px         | Small button padding       |
| `--space-3`         | 16px        | Default vertical padding   |
| `--space-4`         | 24px        | Default horizontal padding |
| `--space-5`         | 32px        | Large button padding       |
| `--radius-md`       | 10px        | Button border radius       |
| `--shadow-gold`     | Gold shadow | Button elevation           |

### HTML Semantic Guidelines

```html
<!-- Form submission -->
<button type="submit" class="btn">Submit Form</button>

<!-- General action -->
<button type="button" class="btn">Perform Action</button>

<!-- Navigation (use link instead) -->
<a href="/page" class="btn" role="button">Go to Page</a>
```

## Examples

### Login Form Buttons

```html
<div class="form-actions">
    <button type="submit" class="btn btn-lg btn-block">Sign In</button>
    <button type="button" class="btn btn-outline btn-block">
        Create Account
    </button>
</div>
```

### Card Action Buttons

```html
<div class="card">
    <h3>Article Title</h3>
    <p>Article excerpt...</p>
    <div class="card-actions">
        <a href="/article/slug" class="btn">Read More</a>
        <button class="btn btn-outline btn-sm">Share</button>
    </div>
</div>
```

### Modal Dialog Buttons

```html
<div class="modal-footer">
    <button type="button" class="btn btn-outline" data-dismiss="modal">
        Cancel
    </button>
    <button type="button" class="btn">Confirm</button>
</div>
```

## Browser Support

- All modern browsers support the button styling
- CSS transforms and transitions work in IE 10+
- Focus-visible pseudo-class has progressive enhancement
- Fallbacks provided for older browsers

## Performance Notes

- Button styles are included in the main CSS bundle
- Hover effects use CSS transforms for smooth performance
- Box-shadow animations are hardware-accelerated
- No JavaScript required for basic functionality

---

**Related Components:**

- [Form Controls](./form.md)
- [Navigation](./navigation.md)
- [Cards](./card.md)

**Last Updated**: 2024-01-17
