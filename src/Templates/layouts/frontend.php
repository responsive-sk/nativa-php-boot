<?php
declare(strict_types=1);

/**
 * CMS Layout - For PHP CMS pages with dynamic content
 *
 * @var string $content
 * @var string $page Page identifier (home, blog, contact, etc.)
 * @var string $pageTitle Page title (dynamic from CMS)
 * @var string|null $metaDescription Optional meta description
 * @var bool $isGuest User authentication state
 * @var string $csrfToken CSRF token for forms
 */

use Infrastructure\View\AssetHelper;

$page = $page ?? 'home';
$pageTitle = $pageTitle ?? 'Nativa CMS';
$isGuest = $isGuest ?? true;
$csrfToken = $csrfToken ?? '';
$metaDescription = $metaDescription ?? 'Modern PHP CMS and Blog Platform';

// Use AssetHelper for production builds with hashed filenames
$themeInitJs = AssetHelper::js('init.js');
$cssBundle = AssetHelper::css('css.css');
$appJs = AssetHelper::js('app.js');

// Page-specific CSS - dynamically loaded from manifest.json
$pageSpecificCssUrl = AssetHelper::pageCss($page);

?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
  <meta name="description" content="<?= $this->e($metaDescription) ?>">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  
  <!-- Dynamic page title from CMS -->
  <title><?= $this->e($pageTitle) ?></title>
  
  <!-- Prevent theme flash - load theme script before CSS -->
  <script src="<?= $themeInitJs ?>" defer crossorigin="anonymous"></script>

  <!-- Shared base CSS (loaded on every page) -->
  <link rel="stylesheet" href="<?= $cssBundle ?>">

  <!-- Page-specific CSS (only if exists) -->
  <?php if ($pageSpecificCssUrl): ?>
  <link rel="stylesheet" href="<?= $pageSpecificCssUrl ?>">
  <?php endif; ?>

  <!-- Preload critical CSS -->
  <link rel="preload" href="<?= $cssBundle ?>" as="style">

</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header__inner container">
      <a href="/" class="header__logo">Nativa<span class="header__logo-dot">.</span></a>

      <nav class="nav">
        <a href="/" class="nav__link" data-page="home">Home</a>
        <a href="/blog" class="nav__link" data-page="blog">Blog</a>
        <a href="/portfolio" class="nav__link" data-page="portfolio">Portfolio</a>
        <a href="/contact" class="nav__link" data-page="contact">Contact</a>
        <a href="/docs" class="nav__link" data-page="docs">Docs</a>
      </nav>

      <div class="header__actions">
        <!-- Theme toggle -->
        <button class="theme-toggle" aria-label="Toggle theme">
            <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
            <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>
        
        <?php if ($isGuest): ?>
        <a href="/login" class="btn btn--outline">Sign In</a>
        <?php else: ?>
        <a href="/profile" class="header__user-btn">
            <span class="header__user-avatar">U</span>
        </a>
        <form method="post" action="/logout" style="display:inline;">
            <input type="hidden" name="_csrf" value="<?= $this->e($csrfToken) ?>">
            <button type="submit" class="btn btn--outline btn--sm">Sign Out</button>
        </form>
        <?php endif; ?>
      </div>

      <button class="mobile-menu-btn" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
        <svg class="mobile-menu-btn__icon-burger" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
        <svg class="mobile-menu-btn__icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
  </header>

  <!-- Mobile Navigation Menu -->
  <nav id="mobile-menu" class="mobile-menu" aria-label="Mobile navigation" role="dialog" aria-modal="true">
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
      <div class="mobile-menu__auth">
        <a href="/login" class="btn btn--outline btn--sm">Sign In</a>
      </div>
    </div>
    <div class="mobile-menu__nav">
      <div class="mobile-menu__item"><a href="/" class="mobile-menu__link" data-page="home">Home</a></div>
      <div class="mobile-menu__item"><a href="/blog" class="mobile-menu__link" data-page="blog">Blog</a></div>
      <div class="mobile-menu__item"><a href="/portfolio" class="mobile-menu__link" data-page="portfolio">Portfolio</a></div>
      <div class="mobile-menu__item"><a href="/contact" class="mobile-menu__link" data-page="contact">Contact</a></div>
      <div class="mobile-menu__item"><a href="/docs" class="mobile-menu__link" data-page="docs">Docs</a></div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="main">
    <?= $content ?>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer__inner container">
      <div class="footer__content">
        <div class="footer__section">
          <h3 class="footer__title">Nativa CMS</h3>
          <ul class="footer__links">
            <li><a href="/">Home</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </div>
        <div class="footer__section">
          <h3 class="footer__title">Features</h3>
          <ul class="footer__links">
            <li><a href="/blog">Articles</a></li>
            <li><a href="/blog">Pages</a></li>
            <li><a href="/contact">Forms</a></li>
          </ul>
        </div>
        <div class="footer__section">
          <h3 class="footer__title">Support</h3>
          <ul class="footer__links">
            <li><a href="/contact">Contact</a></li>
            <li><a href="/docs">Documentation</a></li>
          </ul>
        </div>
      </div>
      <div class="footer__bottom">
        <p>© 2026 Nativa CMS. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Shared JavaScript -->
 <script type="module" src="<?= $appJs ?>"></script>

</body>
</html>
