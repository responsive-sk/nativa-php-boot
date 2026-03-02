<?php
/**
 * Portfolio Page Template
 * Based on vzor design - dark theme with glassmorphism
 *
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var string $pageTitle
 */

use Infrastructure\View\AssetHelper;

$portfolioCss = AssetHelper::css('portfolio');
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
    <meta name="description" content="Our latest projects and creative work">
    <link rel="stylesheet" href="<?= $coreCss ?>">
    <link rel="stylesheet" href="<?= $portfolioCss ?>">
</head>
<body>
    <!-- Topbar -->
    <header class="topbar">
        <div>
            <div class="title">Nativa CMS</div>
            <div class="subtitle">Portfolio</div>
        </div>
        
        <div class="topbar-actions">
            <nav class="nav">
                <a href="/" class="nav__link">Home</a>
                <a href="/articles" class="nav__link">Articles</a>
                <a href="/portfolio" class="nav__link nav__link--active">Portfolio</a>
                <a href="/contact" class="nav__link">Contact</a>
            </nav>
            
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" type="button">
                <svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
            
            <a href="/login" class="btn btn--primary">Login</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="layout">
        <!-- Page Header -->
        <section class="card card--header">
            <h1 class="page-title">Our Portfolio</h1>
            <p class="page-subtitle">Explore our latest projects and creative work</p>
        </section>

        <!-- Projects Grid -->
        <section class="card">
            <h2 class="card__title">Projects</h2>
            <div class="projects-grid">
                <article class="project-card">
                    <div class="project-card__image">
                        <div class="project-card__placeholder">Project 1</div>
                    </div>
                    <h3 class="project-card__title">Web Design Project</h3>
                    <p class="project-card__desc">Modern responsive website design</p>
                </article>
                
                <article class="project-card">
                    <div class="project-card__image">
                        <div class="project-card__placeholder">Project 2</div>
                    </div>
                    <h3 class="project-card__title">Mobile App</h3>
                    <p class="project-card__desc">Cross-platform mobile application</p>
                </article>
                
                <article class="project-card">
                    <div class="project-card__image">
                        <div class="project-card__placeholder">Project 3</div>
                    </div>
                    <h3 class="project-card__title">Brand Identity</h3>
                    <p class="project-card__desc">Complete brand redesign</p>
                </article>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer__content">
            <p>Nativa CMS v1.0 - Built with PHP 8.4+</p>
            <div class="footer__links">
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
                <a href="/admin">Admin</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= $coreJs ?>" defer></script>
    <script src="<?= $appJs ?>" defer></script>
</body>
</html>
