<?php

declare(strict_types = 1);

/**
 * @var string      $content
 * @var string      $page Page identifier (home, services, pricing, contact, not-found)
 * @var string|null $metaDescription Optional meta description
 * @var User|null   $user
 * @var bool        $isGuest
 */

use App\Domain\User\User;
use App\Infrastructure\View\AssetHelper;

$assetBase = '/assets';
$page ??= 'home';
$isGuest ??= true;
$csrfToken ??= '';

// Use AssetHelper for production builds with hashed filenames
$themeInitJs = AssetHelper::js('init.js');
$cssBundle = AssetHelper::css('css.css');
$appJs = AssetHelper::js('app.js');

// Page-specific CSS only (JS is shared in app.js)
$pageSpecificCss = match ($page) {
    'home'      => 'home.css',
    'services'  => 'services.css',
    'pricing'   => 'pricing.css',
    'contact'   => 'contact.css',
    'portfolio' => 'portfolio.css',
    'blog'      => 'blog.css',
    'docs'      => 'docs.css',
    'not-found' => 'use-cases/not-found.css',
    default     => null,
};

$pageSpecificCssUrl = $pageSpecificCss ? AssetHelper::css($pageSpecificCss) : null;

$pageDescriptions = [
    'home'      => 'App - Premium digital experiences and web development services.',
    'services'  => 'Our professional web development services include UI/UX design, custom websites, and mobile applications.',
    'pricing'   => 'Transparent pricing plans for every need. Start with a 14-day free trial.',
    'contact'   => 'Get in touch with us. We\'d love to hear from you about your next project.',
    'blog'      => 'Read our latest insights, tutorials, and updates on web development, design, and technology.',
    'portfolio' => 'Explore our portfolio of web design and development projects showcasing our expertise and creativity.',
    'docs'      => 'Component library documentation with design tokens, UI patterns, and interactive examples.',
    'not-found' => 'The page you\'re looking for doesn\'t exist.',
];

$metaDescription ??= $pageDescriptions[$page] ?? '';
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
  <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">
  <meta name="referrer" content="no-referrer-when-downgrade">
  <title>App - Digital Experiences</title>
  <!-- Prevent theme flash - load theme script before CSS -->
  <script src="<?php echo $themeInitJs; ?>" defer crossorigin="anonymous"></script>

  <!-- Shared base CSS (loaded on every page) -->
  <link rel="stylesheet" href="<?php echo $cssBundle; ?>">

  <!-- Page-specific CSS (only if exists) -->
  <?php if ($pageSpecificCssUrl) { ?>
  <link rel="stylesheet" href="<?php echo $pageSpecificCssUrl; ?>">
  <?php } ?>


</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header__inner container">
      <a href="/" class="header__logo">App<span class="header__logo-dot">.</span></a>

      <nav class="nav">
        <a href="/" class="nav__link" data-page="home">Home</a>
        <a href="/services" class="nav__link" data-page="services">Services</a>
        <a href="/pricing" class="nav__link" data-page="pricing">Pricing</a>
        <a href="/portfolio" class="nav__link" data-page="portfolio">Portfolio</a>
        <a href="/blog" class="nav__link" data-page="blog">Blog</a>
        <a href="/docs" class="nav__link" data-page="docs">Docs</a>
        <a href="/contact" class="nav__link" data-page="contact">Contact</a>
      </nav>

      <div class="header__actions">
        <button class="theme-toggle" aria-label="Toggle theme">
            <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
            <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>
        <?php if ($isGuest) { ?>
        <a href="/login" class="btn btn--outline">Sign In</a>
        <?php } else { ?>
        <a href="/profile" class="header__user-btn">
            <span class="header__user-avatar"><?php echo strtoupper(substr($user->name ?? 'U', 0, 2)); ?></span>
        </a>
        <form method="post" action="/logout" style="display:inline;">
            <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">
            <button type="submit" class="btn btn--outline btn--sm">Sign Out</button>
        </form>
        <?php } ?>
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
      <div class="mobile-menu__item"><a href="/services" class="mobile-menu__link" data-page="services">Services</a></div>
      <div class="mobile-menu__item"><a href="/pricing" class="mobile-menu__link" data-page="pricing">Pricing</a></div>
      <div class="mobile-menu__item"><a href="/portfolio" class="mobile-menu__link" data-page="portfolio">Portfolio</a></div>
      <div class="mobile-menu__item"><a href="/blog" class="mobile-menu__link" data-page="blog">Blog</a></div>
      <div class="mobile-menu__item"><a href="/docs" class="mobile-menu__link" data-page="docs">Docs</a></div>
      <div class="mobile-menu__item"><a href="/contact" class="mobile-menu__link" data-page="contact">Contact</a></div>
    </div>
  </nav>

  <!-- Mobile Menu (created by JavaScript in app.ts) -->

  <!-- Main Content -->
  <main class="main">
    <?php echo $content; ?>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer__inner container">
      <div class="footer__content">
        <div class="footer__section">
          <h3 class="footer__title">Company</h3>
          <ul class="footer__links">
            <li><a href="/about">About Us</a></li>
            <li><a href="/careers">Careers</a></li>
            <li><a href="/blog">Blog</a></li>
          </ul>
        </div>
        <div class="footer__section">
          <h3 class="footer__title">Services</h3>
          <ul class="footer__links">
            <li><a href="/services">Web Development</a></li>
            <li><a href="/services">UI/UX Design</a></li>
            <li><a href="/services">Mobile Apps</a></li>
          </ul>
        </div>
        <div class="footer__section">
          <h3 class="footer__title">Support</h3>
          <ul class="footer__links">
            <li><a href="/contact">Contact</a></li>
            <li><a href="/faq">FAQ</a></li>
            <li><a href="/privacy">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer__section">
          <h3 class="footer__title">Connect</h3>
          <ul class="footer__links">
            <li><a href="https://twitter.com">Twitter</a></li>
            <li><a href="https://linkedin.com">LinkedIn</a></li>
            <li><a href="https://github.com/responsive-sk">GitHub</a></li>
          </ul>
        </div>
      </div>
      <div class="footer__bottom">
        <p> 2026 App. by responsive.sk ___ all rights reserved ___</p>
      </div>
    </div>
  </footer>

  <!-- Shared JavaScript -->
  <script type="module" src="<?php echo $appJs; ?>"></script>

  <!-- HTMX for dynamic interactions -->
  <!--<script src="https://unpkg.com/htmx.org@1.9.12"></script>-->
</body>
</html>
