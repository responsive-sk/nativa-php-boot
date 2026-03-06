<?php
declare(strict_types = 1);

/**
 * Header Partial - Primary Navigation
 *
 * @var string $page Current page identifier (home, blog, portfolio, contact, docs)
 * @var bool   $isGuest User authentication state
 */
$page ??= 'home';
$isGuest ??= true;

/**
 * Helper function to check if page is active
 */
function isActivePage(string $currentPage, string $targetPage): string
{
    return $currentPage === $targetPage ? ' nav-primary__item--active' : '';
}

?>
<!-- Navigation -->
<nav class="nav-primary">
  <div class="nav-primary__inner container">
    <a href="/" class="nav-primary__logo">
      <span>Nativa</span>
      <span class="nav-primary__logo-dot">•</span>
      <span>CMS</span>
    </a>
    <ul class="nav-primary__list">
      <li class="nav-primary__item<?= isActivePage($page, 'home') ?>">
        <a href="/" class="nav-primary__link">
          <span class="nav-primary__number">01</span>
          <span class="nav-primary__text">Home</span>
        </a>
      </li>
      <li class="nav-primary__item<?= isActivePage($page, 'blog') ?>">
        <a href="/blog" class="nav-primary__link">
          <span class="nav-primary__number">02</span>
          <span class="nav-primary__text">Blog</span>
        </a>
      </li>
      <li class="nav-primary__item<?= isActivePage($page, 'portfolio') ?>">
        <a href="/portfolio" class="nav-primary__link">
          <span class="nav-primary__number">03</span>
          <span class="nav-primary__text">Portfolio</span>
        </a>
      </li>
      <li class="nav-primary__item<?= isActivePage($page, 'contact') ?>">
        <a href="/contact" class="nav-primary__link">
          <span class="nav-primary__number">04</span>
          <span class="nav-primary__text">Contact</span>
        </a>
      </li>
      <li class="nav-primary__item<?= isActivePage($page, 'docs') ?>">
        <a href="/docs" class="nav-primary__link">
          <span class="nav-primary__number">05</span>
          <span class="nav-primary__text">Docs</span>
        </a>
      </li>
    </ul>

    <div class="nav-primary__actions">
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" type="button">
        <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"></circle>
          <line x1="12" y1="1" x2="12" y2="3"></line>
          <line x1="12" y1="21" x2="12" y2="23"></line>
        </svg>
        <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
      </button>

      <!-- Mobile Menu Toggle -->
      <button class="nav-primary__mobile-toggle mobile-menu-btn" type="button" aria-label="Toggle menu" aria-expanded="false">
        <span class="mobile-toggle__icon"></span>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Menu -->
<nav class="mobile-menu" aria-label="Mobile navigation" hidden>
  <div class="mobile-menu__header">
    <div class="mobile-menu__theme">
      <span class="mobile-menu__theme-label">Theme</span>
      <button class="theme-toggle" aria-label="Toggle theme">
        <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="5"/>
          <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
        <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu__nav">
    <div class="mobile-menu__item"><a href="/" class="mobile-menu__link" data-page="home">Home</a></div>
    <div class="mobile-menu__item"><a href="/blog" class="mobile-menu__link" data-page="blog">Blog</a></div>
    <div class="mobile-menu__item"><a href="/portfolio" class="mobile-menu__link" data-page="portfolio">Portfolio</a></div>
    <div class="mobile-menu__item"><a href="/contact" class="mobile-menu__link" data-page="contact">Contact</a></div>
    <div class="mobile-menu__item"><a href="/docs" class="mobile-menu__link" data-page="docs">Docs</a></div>
    <?php if (!$isGuest): ?>
    <div class="mobile-menu__item"><a href="/admin" class="mobile-menu__link">Admin</a></div>
    <div class="mobile-menu__item"><a href="/logout" class="mobile-menu__link">Logout</a></div>
    <?php endif; ?>
  </div>
</nav>
