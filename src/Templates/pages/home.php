<?php
/**
 * Homepage Template - Luxury Winery Style
 * Inspired by BRAND Napa Valley design
 *
 * @var TemplateRenderer $this
 * @var array $articles
 * @var string $pageTitle
 */

use Infrastructure\View\AssetHelper;

$homeCss = AssetHelper::css('home');
$homeJs = AssetHelper::js('home');
$coreCss = AssetHelper::css('core-css');
$coreJs = AssetHelper::js('core-init');
$appJs = AssetHelper::js('core-app');
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($pageTitle) ?></title>
    <meta name="description" content="Modern PHP 8.4+ CMS and Blog Platform">
    <link rel="stylesheet" href="<?= $coreCss ?>">
    <link rel="stylesheet" href="<?= $homeCss ?>">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav-primary">
        <div class="nav-primary__inner container">
            <ul class="nav-primary__list">
                <li class="nav-primary__item nav-primary__item--active">
                    <a href="/" class="nav-primary__link">
                        <span class="nav-primary__number">01</span>
                        <span class="nav-primary__text">Home</span>
                    </a>
                </li>
                <li class="nav-primary__item">
                    <a href="/articles" class="nav-primary__link">
                        <span class="nav-primary__number">02</span>
                        <span class="nav-primary__text">Articles</span>
                    </a>
                </li>
                <li class="nav-primary__item">
                    <a href="/portfolio" class="nav-primary__link">
                        <span class="nav-primary__number">03</span>
                        <span class="nav-primary__text">Portfolio</span>
                    </a>
                </li>
                <li class="nav-primary__item">
                    <a href="/about" class="nav-primary__link">
                        <span class="nav-primary__number">04</span>
                        <span class="nav-primary__text">About</span>
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

                <a href="/login" class="btn btn--outline btn--sm">Join Us</a>
                
                <!-- Mobile Menu Toggle -->
                <button class="nav-primary__mobile-toggle mobile-menu-btn" type="button" aria-label="Toggle menu" aria-expanded="false">
                    <span class="mobile-toggle__icon"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <nav class="mobile-menu" aria-label="Mobile navigation" hidden>
        <div class="mobile-menu__inner">
            <button class="mobile-menu__close" type="button" aria-label="Close menu">
                <span class="mobile-close__icon"></span>
            </button>
            
            <ul class="mobile-menu__list">
                <li class="mobile-menu__item mobile-menu__item--active">
                    <a href="/" class="mobile-menu__link">
                        <span class="mobile-menu__number">01</span>
                        <span class="mobile-menu__text">Home</span>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="/articles" class="mobile-menu__link">
                        <span class="mobile-menu__number">02</span>
                        <span class="mobile-menu__text">Articles</span>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="/portfolio" class="mobile-menu__link">
                        <span class="mobile-menu__number">03</span>
                        <span class="mobile-menu__text">Portfolio</span>
                    </a>
                </li>
                <li class="mobile-menu__item">
                    <a href="/about" class="mobile-menu__link">
                        <span class="mobile-menu__number">04</span>
                        <span class="mobile-menu__text">About</span>
                    </a>
                </li>
            </ul>
            
            <div class="mobile-menu__footer">
                <a href="/login" class="btn btn--primary btn--full">Join Us</a>
            </div>
        </div>
    </nav>

    <!-- Scroll Indicator - Fixed at bottom of viewport -->
    <div class="hero-manifesto__scroll">
        <span class="hero-manifesto__scroll-text">Scroll</span>
        <div class="hero-manifesto__scroll-line"></div>
        <span class="hero-manifesto__scroll-number">04</span>
    </div>

    <!-- Hero / Manifesto -->
    <section class="hero-manifesto">
        <div class="hero-manifesto__bg">
            <picture class="hero-manifesto__picture">
                <!-- Desktop Large: 1366w (for 1920px screens, displayed at ~1350px) -->
                <source 
                    media="(min-width: 1440px)" 
                    srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1366,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                >
                <!-- Desktop: 1024w -->
                <source 
                    media="(min-width: 1024px)" 
                    srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1024,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                >
                <!-- Tablet: 768w -->
                <source 
                    media="(min-width: 769px)" 
                    srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                >
                <!-- Mobile: 480w -->
                <source 
                    media="(min-width: 480px)" 
                    srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_480,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                >
                <!-- Mobile Small: 320w -->
                <source 
                    media="(max-width: 479px)" 
                    srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_320,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                >
                <!-- Fallback -->
                <img 
                    src="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-5.jpg" 
                    alt="Modern office background"
                    class="hero-manifesto__bg-image"
                    fetchpriority="high"
                    loading="eager"
                    decoding="async"
                    width="1366"
                    height="768"
                    crossorigin="anonymous"
                >
            </picture>
            <div class="hero-manifesto__bg-overlay"></div>
            <div class="hero-manifesto__bg-shapes">
                <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--1"></div>
                <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--2"></div>
                <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--3"></div>
            </div>
        </div>
        
        <div class="hero-manifesto__content container">
            <p class="hero-manifesto__text">
                Nativa CMS begins with a premise—platforms that root deepest in clean architecture 
                are the ones that reach highest in performance and maintainability.
            </p>
            <a href="#featured" class="hero-manifesto__link">Our Philosophy</a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="site-body">
        <!-- Featured Section -->
        <section class="section section--featured" id="featured">
            <div class="container">
                <header class="section__header">
                    <span class="section__number">01</span>
                    <h2 class="section__title">Built on Modern PHP</h2>
                    <p class="section__subtitle">Rooted in PHP 8.4+, reaching for excellence</p>
                </header>

                <div class="featured-grid">
                    <article class="featured-item">
                        <div class="featured-item__content">
                            <h3 class="featured-item__title">Domain-Driven Design</h3>
                            <p class="featured-item__desc">Clean separation of concerns with DDD architecture</p>
                        </div>
                        <span class="featured-item__number">01</span>
                    </article>

                    <article class="featured-item">
                        <div class="featured-item__content">
                            <h3 class="featured-item__title">Full-Text Search</h3>
                            <p class="featured-item__desc">Lightning-fast SQLite FTS5 search engine</p>
                        </div>
                        <span class="featured-item__number">02</span>
                    </article>

                    <article class="featured-item">
                        <div class="featured-item__content">
                            <h3 class="featured-item__title">Modern Stack</h3>
                            <p class="featured-item__desc">Vite, TypeScript, BEM, and design tokens</p>
                        </div>
                        <span class="featured-item__number">03</span>
                    </article>
                </div>
            </div>
        </section>

        <!-- Articles / Wines Showcase -->
        <section class="section section--articles">
            <div class="container">
                <header class="section__header">
                    <span class="section__number">02</span>
                    <h2 class="section__title">Latest Articles</h2>
                    <p class="section__subtitle">Fresh content from our platform</p>
                </header>

                <?php if ($this->isEmpty($articles)): ?>
                    <div class="empty-state">
                        <p>No articles yet.</p>
                        <a href="/admin/articles/create" class="btn btn--primary">Create First Article</a>
                    </div>
                <?php else: ?>
                    <div class="articles-showcase">
                        <?php foreach ($articles as $article): ?>
                        <article class="article-showcase">
                            <div class="article-showcase__content">
                                <h3 class="article-showcase__title">
                                    <a href="/articles/<?= $this->e($article->slug()) ?>" class="article-showcase__link">
                                        <?= $this->e($article->title()) ?>
                                    </a>
                                </h3>
                                <p class="article-showcase__excerpt"><?= $this->e($article->excerpt()) ?></p>
                                <footer class="article-showcase__footer">
                                    <span class="article-showcase__date"><?= $this->date($article->publishedAt()) ?></span>
                                    <a href="/articles/<?= $this->e($article->slug()) ?>" class="article-showcase__read">Read More</a>
                                </footer>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="section__cta">
                        <a href="/articles" class="btn btn--outline">View All Articles</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Visit / Contact -->
        <section class="section section--visit">
            <div class="container">
                <header class="section__header">
                    <span class="section__number">03</span>
                    <h2 class="section__title">Get In Touch</h2>
                    <p class="section__subtitle">Ready to start your journey?</p>
                </header>

                <div class="visit-grid">
                    <div class="visit-item">
                        <h3 class="visit-item__title">Contact Us</h3>
                        <p class="visit-item__desc">Have questions? We're here to help.</p>
                        <a href="/contact" class="btn btn--outline">Contact</a>
                    </div>

                    <div class="visit-item">
                        <h3 class="visit-item__title">Documentation</h3>
                        <p class="visit-item__desc">Explore our comprehensive guides.</p>
                        <a href="/docs" class="btn btn--outline">View Docs</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Join / CTA -->
        <section class="section section--join">
            <div class="container">
                <div class="join-content">
                    <h2 class="join-content__title">Join Our Community</h2>
                    <p class="join-content__text">Stay updated with the latest features and announcements.</p>
                    <div class="join-content__actions">
                        <a href="/login" class="btn btn--primary btn--lg">Get Started</a>
                        <a href="/about" class="btn btn--outline btn--lg">Learn More</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer__inner container">
            <div class="site-footer__top">
                <div class="site-footer__brand">
                    <span class="site-footer__logo">Nativa<span class="site-footer__dot">.</span></span>
                    <p class="site-footer__tagline">Modern PHP CMS Platform</p>
                </div>

                <nav class="site-footer__nav">
                    <a href="/about" class="site-footer__link">About</a>
                    <a href="/contact" class="site-footer__link">Contact</a>
                    <a href="/admin" class="site-footer__link">Admin</a>
                </nav>
            </div>

            <div class="site-footer__bottom">
                <p class="site-footer__copyright">&copy; <?= date('Y') ?> Nativa CMS. Built with PHP 8.4+</p>
                <div class="site-footer__legal">
                    <a href="#" class="site-footer__legal-link">Privacy</a>
                    <a href="#" class="site-footer__legal-link">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= $coreJs ?>" defer></script>
    <script src="<?= $appJs ?>" defer></script>
    <script src="<?= $homeJs ?>" type="module" defer></script>
</body>
</html>
