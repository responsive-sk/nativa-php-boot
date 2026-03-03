// src/vendors/htmx.ts
// Vanilla JS htmx-like functionality
// No 3rd party dependencies for maximum performance

/**
 * Minimal htmx-like functionality
 * Only what we actually need
 */

export function loadHtml(url: string, target: string): void {
  const element = document.querySelector(target);
  if (!element) return;

  fetch(url)
    .then(res => res.text())
    .then(html => {
      element.innerHTML = html;
    })
    .catch(err => console.error('Load failed:', err));
}

// Auto-init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  console.log('%c🔧 HTMX-LITE READY', 'color: #3b82f6; font-weight: bold');
});
