<script>
    let { currentPage = 'home', isGuest = true, enhanceOnly = false } = $props();
    let activePage = currentPage;
    
    // Only enhance existing DOM, don't replace it
    $effect(() => {
        if (enhanceOnly) {
            // Find existing links and enhance active state
            const links = document.querySelectorAll('.nav-primary__link[data-page]');
            
            links.forEach(link => {
                const listItem = link.parentElement;
                
                // Update active state based on current page
                if (link.dataset.page === activePage) {
                    listItem.classList.add('nav-primary__item--active');
                } else {
                    listItem.classList.remove('nav-primary__item--active');
                }
                
                // Add smooth click behavior
                link.addEventListener('click', (e) => {
                    // Let default navigation happen, but update state
                    activePage = link.dataset.page;
                });
            });
            
            // Enhance theme toggle if present
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    // Theme toggle logic will be handled by ThemeToggle component
                    console.log('Theme toggle clicked');
                });
            }
        }
    });
</script>

{#if !enhanceOnly}
    <!-- Full Svelte navigation (replaces PHP) -->
    <nav class="nav-primary">
        <div class="nav-primary__inner container">
            <a href="/" class="nav-primary__logo">
                <span>Nativa</span>
                <span class="nav-primary__logo-dot">•</span>
                <span>CMS</span>
            </a>
            
            <ul class="nav-primary__list">
                <li class="nav-primary__item" class:nav-primary__item--active={activePage === 'home'}>
                    <a href="/" class="nav-primary__link" data-page="home">
                        <span class="nav-primary__number">01</span>
                        <span class="nav-primary__text">Home</span>
                    </a>
                </li>
                <li class="nav-primary__item" class:nav-primary__item--active={activePage === 'blog'}>
                    <a href="/blog" class="nav-primary__link" data-page="blog">
                        <span class="nav-primary__number">02</span>
                        <span class="nav-primary__text">Blog</span>
                    </a>
                </li>
                <li class="nav-primary__item" class:nav-primary__item--active={activePage === 'portfolio'}>
                    <a href="/portfolio" class="nav-primary__link" data-page="portfolio">
                        <span class="nav-primary__number">03</span>
                        <span class="nav-primary__text">Portfolio</span>
                    </a>
                </li>
                <li class="nav-primary__item" class:nav-primary__item--active={activePage === 'contact'}>
                    <a href="/contact" class="nav-primary__link" data-page="contact">
                        <span class="nav-primary__number">04</span>
                        <span class="nav-primary__text">Contact</span>
                    </a>
                </li>
                <li class="nav-primary__item" class:nav-primary__item--active={activePage === 'docs'}>
                    <a href="/docs" class="nav-primary__link" data-page="docs">
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

                <button class="nav-primary__mobile-toggle mobile-menu-btn" type="button" aria-label="Toggle menu" aria-expanded="false">
                    <span class="mobile-toggle__icon"></span>
                </button>
            </div>
        </div>
    </nav>
{:else}
    <!-- Enhancement mode: just behavior, no HTML rendered -->
    <!-- Existing PHP HTML is enhanced, not replaced -->
{/if}

<style>
    /* Styles only apply in full mode (!enhanceOnly) */
    /* In enhance mode, existing PHP CSS is used */
</style>
