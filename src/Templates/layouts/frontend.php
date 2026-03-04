<?php
declare(strict_types = 1);

/**
 * CMS Layout - For PHP CMS pages with dynamic content.
 *
 * @var string      $content
 * @var string      $page Page identifier (home, blog, contact, etc.)
 * @var string      $pageTitle Page title (dynamic from CMS)
 * @var string|null $metaDescription Optional meta description
 * @var bool        $isGuest User authentication state
 * @var string      $csrfToken CSRF token for forms
 */

use Infrastructure\View\AssetHelper;

$page ??= 'home';
$pageTitle ??= 'Nativa CMS';
$isGuest ??= true;
$csrfToken ??= '';
$metaDescription ??= 'Modern PHP CMS and Blog Platform';

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
  <meta name="description" content="<?php echo $this->e($metaDescription); ?>">
  <meta name="referrer" content="strict-origin-when-cross-origin">

  <!-- Dynamic page title from CMS -->
  <title><?php echo $this->e($pageTitle); ?></title>

  <!-- Prevent theme flash - load theme script before CSS -->
  <script src="<?php echo $themeInitJs; ?>" defer crossorigin="anonymous"></script>

  <!-- CRITICAL CSS (inlined for faster FCP) -->
  <?php
  $criticalCssFile = __DIR__ . '/storage/critical-css/critical.css';
  if (file_exists($criticalCssFile)) {
      echo '<style id="critical-css">' . file_get_contents($criticalCssFile) . '</style>';
  }
  ?>

  <!-- Shared base CSS (async loaded) -->
  <link rel="preload" href="<?php echo $cssBundle; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="<?php echo $cssBundle; ?>"></noscript>

  <!-- Page-specific CSS (async loaded, only if exists) -->
  <?php if ($pageSpecificCssUrl) { ?>
  <link rel="preload" href="<?php echo $pageSpecificCssUrl; ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="<?php echo $pageSpecificCssUrl; ?>"></noscript>
  <?php } ?>

  <!-- Preload critical fonts for hero -->
  <link rel="preload" href="/assets/fonts/sans-serif/font-sans-web.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/assets/fonts/sans-serif/plein-variable.woff2" as="font" type="font/woff2" crossorigin>

</head>
<body>
  <!-- Navigation -->
  <nav class="nav-primary">
    <div class="nav-primary__inner container">
      <a href="/" class="nav-primary__logo">
        <span>Nativa</span>
        <span class="nav-primary__logo-dot">•</span>
        <span>CMS</span>
      </a>
      <ul class="nav-primary__list">
        <li class="nav-primary__item nav-primary__item--active">
          <a href="/" class="nav-primary__link">
            <span class="nav-primary__number">01</span>
            <span class="nav-primary__text">Home</span>
          </a>
        </li>
        <li class="nav-primary__item">
          <a href="/blog" class="nav-primary__link">
            <span class="nav-primary__number">02</span>
            <span class="nav-primary__text">Blog</span>
          </a>
        </li>
        <li class="nav-primary__item">
          <a href="/portfolio" class="nav-primary__link">
            <span class="nav-primary__number">03</span>
            <span class="nav-primary__text">Portfolio</span>
          </a>
        </li>
        <li class="nav-primary__item">
          <a href="/contact" class="nav-primary__link">
            <span class="nav-primary__number">04</span>
            <span class="nav-primary__text">Contact</span>
          </a>
        </li>
        <li class="nav-primary__item">
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

        <?php if ($isGuest) { ?>
        <a href="/login" class="btn btn--outline btn--sm">Sign In</a>
        <?php } else { ?>
        <a href="/profile" class="btn btn--outline btn--sm">Profile</a>
        <?php } ?>

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

  <!-- Hero Section (only for homepage) -->
  <?php if (($page ?? '') === 'home') { ?>
    <?php include $this->getTemplatesPath() . '/partials/hero-home.php'; ?>
  <?php } ?>

  <!-- Main Content -->
  <main class="main">
    <?php echo $content; ?>
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
  <script type="module" src="<?php echo $appJs; ?>"></script>

  <!-- Page-specific JavaScript (if exists) -->
  <?php
  $pageSpecificJs = [
      'home' => AssetHelper::js('home'),
      'blog' => AssetHelper::js('blog'),
      'portfolio' => AssetHelper::js('portfolio'),
      'contact' => AssetHelper::js('contact'),
      'docs' => AssetHelper::js('docs'),
      'about' => AssetHelper::js('about'),
      'services' => AssetHelper::js('services'),
      'pricing' => AssetHelper::js('pricing'),
  ];
  ?>
  <?php foreach ($pageSpecificJs as $pageName => $jsFile): ?>
  <?php if (($page ?? '') === $pageName && $jsFile): ?>
  <script type="module" src="<?php echo $jsFile; ?>" defer crossorigin="anonymous"></script>
  <?php endif; ?>
  <?php endforeach; ?>

</body>
</html>
