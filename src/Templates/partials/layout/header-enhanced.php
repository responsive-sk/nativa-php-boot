<?php
declare(strict_types = 1);

/**
 * Enhanced Header with Svelte Hydration
 * 
 * This is an OPTIONAL enhancement - original header.php still works!
 * Use this when you want Svelte interactivity on top of PHP-rendered HTML.
 * 
 * @var string $page Current page identifier (home, blog, portfolio, contact, docs)
 * @var bool   $isGuest User authentication state
 */
$page ??= 'home';
$isGuest ??= true;

// Menu items (same as original header.php)
$menu = [
    ['name' => 'Home', 'href' => '/', 'page' => 'home', 'number' => '01'],
    ['name' => 'Blog', 'href' => '/blog', 'page' => 'blog', 'number' => '02'],
    ['name' => 'Portfolio', 'href' => '/portfolio', 'page' => 'portfolio', 'number' => '03'],
    ['name' => 'Contact', 'href' => '/contact', 'page' => 'contact', 'number' => '04'],
    ['name' => 'Docs', 'href' => '/docs', 'page' => 'docs', 'number' => '05'],
];

/**
 * Helper function to check if page is active.
 */
function isActivePage(string $currentPage, string $targetPage): string
{
    return $currentPage === $targetPage ? ' nav-primary__item--active' : '';
}

?>
<!-- Navigation with Svelte Enhancement -->
<nav 
    class="nav-primary" 
    data-svelte-hydrate="navigation" 
    data-page="<?= $page ?>" 
    data-is-guest="<?= $isGuest ? 'true' : 'false' ?>"
>
    <div class="nav-primary__inner container">
        <a href="/" class="nav-primary__logo">
            <span>Nativa</span>
            <span class="nav-primary__logo-dot">•</span>
            <span>CMS</span>
        </a>
        
        <!-- Menu items (PHP rendered, Svelte enhances) -->
        <ul class="nav-primary__list">
            <?php foreach ($menu as $item): ?>
                <li class="nav-primary__item<?= isActivePage($page, $item['page']) ?>">
                    <a href="<?= $item['href'] ?>" class="nav-primary__link" data-page="<?= $item['page'] ?>">
                        <span class="nav-primary__number"><?= $item['number'] ?></span>
                        <span class="nav-primary__text"><?= $item['name'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-primary__actions">
            <!-- Theme Toggle (Svelte will enhance) -->
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

<!-- Mobile Menu (PHP rendered, Svelte enhances behavior) -->
<nav 
    class="mobile-menu" 
    aria-label="Mobile navigation" 
    data-svelte-hydrate="mobile-menu"
    <?= $isGuest ? '' : 'hidden' ?>
>
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
        <?php foreach ($menu as $item): ?>
            <div class="mobile-menu__item">
                <a href="<?= $item['href'] ?>" class="mobile-menu__link" data-page="<?= $item['page'] ?>">
                    <?= $item['name'] ?>
                </a>
            </div>
        <?php endforeach; ?>
        
        <?php if (!$isGuest): ?>
            <div class="mobile-menu__item">
                <a href="/admin" class="mobile-menu__link">Admin</a>
            </div>
            <div class="mobile-menu__item">
                <a href="/logout" class="mobile-menu__link">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Svelte Enhancement (only loads if JS is available) -->
<script type="module">
    // Only enhance if Svelte components are available
    // Silently fails if JS doesn't load - PHP navigation still works!
    import('/assets/navigation-enhance.js').then(({ enhanceNavigation }) => {
        enhanceNavigation();
    }).catch(err => {
        // Silently fail - PHP navigation still works perfectly!
        console.log('ℹ️ Navigation enhancement not loaded, using PHP fallback');
    });
</script>
