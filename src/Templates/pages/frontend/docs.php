<?php

declare(strict_types=1);
?>

<section class="docs">
  <div class="docs__hero">
    <h1>Component Library Documentation</h1>
    <p>Complete guide to UI components, design tokens, and frontend patterns</p>

    <form class="docs-search-form" method="GET" action="/docs/search">
      <div class="search-input-group">
        <div class="search-input-container">
          <svg class="search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input
            type="search"
            name="q"
            value=""
            placeholder="Search documentation..."
            class="search-input"
          >
        </div>

        <button type="submit" class="search-submit-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          Search
        </button>
      </div>
    </form>
  </div>

  <div class="docs__nav">
    <nav class="docs-nav">
      <ul class="docs-nav__list">
        <li class="docs-nav__item">
          <a href="#components" class="docs-nav__link">Components</a>
        </li>
        <li class="docs-nav__item">
          <a href="#design-tokens" class="docs-nav__link">Design Tokens</a>
        </li>
        <li class="docs-nav__item">
          <a href="#patterns" class="docs-nav__link">Patterns</a>
        </li>
        <li class="docs-nav__item">
          <a href="#playground" class="docs-nav__link">Playground</a>
        </li>
      </ul>
    </nav>
  </div>

  <div class="docs__content">
    <section id="components" class="docs-section">
      <h2>UI Components</h2>
      <p>Reusable components built with BEM methodology and design tokens.</p>

      <div class="component-grid">
        <article class="component-card">
          <h3 class="component-card__title">Buttons</h3>
          <div class="component-card__preview">
            <button class="btn">Primary Button</button>
            <button class="btn btn-outline">Outline Button</button>
          </div>
          <div class="component-card__code">
            <pre><code>&lt;button class="btn"&gt;Primary&lt;/button&gt;
&lt;button class="btn btn-outline"&gt;Outline&lt;/button&gt;</code></pre>
          </div>
        </article>

        <article class="component-card">
          <h3 class="component-card__title">Cards</h3>
          <div class="component-card__preview">
            <div class="card">
              <h4>Card Title</h4>
              <p>Card content goes here</p>
            </div>
          </div>
          <div class="component-card__code">
            <pre><code>&lt;div class="card"&gt;
  &lt;h4&gt;Card Title&lt;/h4&gt;
  &lt;p&gt;Card content&lt;/p&gt;
&lt;/div&gt;</code></pre>
          </div>
        </article>

        <article class="component-card">
          <h3 class="component-card__title">Alerts</h3>
          <div class="component-card__preview">
            <div class="alert alert--success">
              <p>Success message</p>
            </div>
          </div>
          <div class="component-card__code">
            <pre><code>&lt;div class="alert alert--success"&gt;
  &lt;p&gt;Success message&lt;/p&gt;
&lt;/div&gt;</code></pre>
          </div>
        </article>
      </div>
    </section>

    <section id="design-tokens" class="docs-section">
      <h2>Design Tokens</h2>
      <p>CSS custom properties that define the visual foundation of the system.</p>

      <div class="tokens-grid">
        <div class="token-group">
          <h3>Colors</h3>
          <div class="color-tokens">
            <div class="color-token">
              <div class="color-token__swatch" style="background: var(--brand-gold);"></div>
              <div class="color-token__info">
                <code>--brand-gold</code>
                <span>#d4af37</span>
              </div>
            </div>
            <div class="color-token">
              <div class="color-token__swatch" style="background: var(--color-bg);"></div>
              <div class="color-token__info">
                <code>--color-bg</code>
                <span>Theme dependent</span>
              </div>
            </div>
          </div>
        </div>

        <div class="token-group">
          <h3>Spacing</h3>
          <div class="spacing-tokens">
            <div class="spacing-token">
              <div class="spacing-token__visual" style="width: var(--space-1); height: var(--space-1);"></div>
              <code>--space-1: 4px</code>
            </div>
            <div class="spacing-token">
              <div class="spacing-token__visual" style="width: var(--space-3); height: var(--space-3);"></div>
              <code>--space-3: 16px</code>
            </div>
            <div class="spacing-token">
              <div class="spacing-token__visual" style="width: var(--space-5); height: var(--space-5);"></div>
              <code>--space-5: 32px</code>
            </div>
          </div>
        </div>

        <div class="token-group">
          <h3>Typography</h3>
          <div class="typography-tokens">
            <div class="typography-token" style="font-size: var(--text-sm);">
              <code>--text-sm</code> - Small text
            </div>
            <div class="typography-token" style="font-size: var(--text-base);">
              <code>--text-base</code> - Base text
            </div>
            <div class="typography-token" style="font-size: var(--text-xl);">
              <code>--text-xl</code> - Large text
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="patterns" class="docs-section">
      <h2>Common Patterns</h2>
      <p>Combinations of components that solve common design problems.</p>

      <div class="pattern-examples">
        <article class="pattern-example">
          <h3>Hero Section</h3>
          <div class="pattern-example__preview">
            <div class="hero hero--compact">
              <h2>Pattern Example</h2>
              <p>This is how components combine</p>
              <button class="btn">Call to Action</button>
            </div>
          </div>
        </article>

        <article class="pattern-example">
          <h3>Card Grid</h3>
          <div class="pattern-example__preview">
            <div class="card-grid">
              <div class="card">
                <h4>Feature One</h4>
                <p>Description</p>
              </div>
              <div class="card">
                <h4>Feature Two</h4>
                <p>Description</p>
              </div>
            </div>
          </div>
        </article>
      </div>
    </section>

    <section id="playground" class="docs-section">
      <h2>Interactive Playground</h2>
      <p>Test and experiment with components in real-time.</p>

      <div class="playground">
        <div class="playground__controls">
          <label>
            <input type="checkbox" id="toggle-theme"> Dark Theme
          </label>
          <label>
            Button Size:
            <select id="button-size">
              <option value="">Default</option>
              <option value="btn-sm">Small</option>
              <option value="btn-lg">Large</option>
            </select>
          </label>
        </div>

        <div class="playground__preview" id="playground-preview">
          <button class="btn" id="playground-button">Interactive Button</button>
          <div class="card">
            <h4>Playground Card</h4>
            <p>Changes based on controls above</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</section>

<script>
// Simple playground interactions
document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('toggle-theme');
  const buttonSize = document.getElementById('button-size');
  const playgroundButton = document.getElementById('playground-button');

  if (themeToggle) {
    themeToggle.addEventListener('change', function() {
      document.documentElement.setAttribute('data-theme', this.checked ? 'dark' : 'light');
    });
  }

  if (buttonSize && playgroundButton) {
    buttonSize.addEventListener('change', function() {
      playgroundButton.className = this.value ? `btn ${this.value}` : 'btn';
    });
  }
});
</script>
