<script>
    import { fade, slide } from 'svelte/transition';
    
    let isOpen = false;
    let currentPage = 'home';
    
    const navItems = [
        { name: 'Home', href: '/', page: 'home' },
        { name: 'Blog', href: '/blog', page: 'blog' },
        { name: 'Portfolio', href: '/portfolio', page: 'portfolio' },
        { name: 'Contact', href: '/contact', page: 'contact' },
        { name: 'Docs', href: '/docs', page: 'docs' },
    ];
    
    function toggleMenu() {
        isOpen = !isOpen;
    }
    
    function closeMenu() {
        isOpen = false;
    }
</script>

<nav class="navigation-svelte">
    <div class="nav-container">
        <a href="/" class="nav-logo">
            <span class="logo-text">Nativa</span>
            <span class="logo-dot">•</span>
            <span class="logo-text">CMS</span>
        </a>
        
        <!-- Desktop Navigation -->
        <ul class="nav-desktop">
            {#each navItems as item (item.name)}
                <li class="nav-item">
                    <a 
                        href={item.href} 
                        class="nav-link" 
                        class:active={currentPage === item.page}
                    >
                        {item.name}
                    </a>
                </li>
            {/each}
        </ul>
        
        <!-- Mobile Menu Button -->
        <button 
            class="nav-mobile-toggle" 
            on:click={toggleMenu}
            aria-label="Toggle menu"
            aria-expanded={isOpen}
        >
            <span class="hamburger"></span>
        </button>
        
        <!-- Mobile Navigation -->
        {#if isOpen}
            <ul class="nav-mobile" in:slide={{ duration: 300 }}>
                {#each navItems as item (item.name)}
                    <li class="nav-item">
                        <a 
                            href={item.href} 
                            class="nav-link"
                            class:active={currentPage === item.page}
                            on:click={closeMenu}
                        >
                            {item.name}
                        </a>
                    </li>
                {/each}
            </ul>
        {/if}
    </div>
</nav>

<style>
    .navigation-svelte {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: var(--nav-bg, rgba(255, 255, 255, 0.95));
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--border, #e0e0e0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .nav-logo {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary, #1a1a1a);
    }
    
    .logo-dot {
        color: var(--accent, #007bff);
    }
    
    .nav-desktop {
        display: flex;
        list-style: none;
        gap: 2rem;
        margin: 0;
        padding: 0;
    }
    
    .nav-link {
        text-decoration: none;
        color: var(--text-secondary, #666);
        font-weight: 500;
        transition: color 0.3s ease;
        padding: 0.5rem 0;
        position: relative;
    }
    
    .nav-link:hover {
        color: var(--accent, #007bff);
    }
    
    .nav-link.active {
        color: var(--accent, #007bff);
    }
    
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--accent, #007bff);
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }
    
    .nav-mobile-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }
    
    .hamburger {
        display: block;
        width: 24px;
        height: 2px;
        background: var(--text-primary, #1a1a1a);
        position: relative;
        transition: background 0.3s ease;
    }
    
    .hamburger::before,
    .hamburger::after {
        content: '';
        position: absolute;
        width: 24px;
        height: 2px;
        background: var(--text-primary, #1a1a1a);
        transition: transform 0.3s ease;
    }
    
    .hamburger::before {
        top: -8px;
    }
    
    .hamburger::after {
        top: 8px;
    }
    
    .nav-mobile {
        display: none;
        list-style: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--nav-bg, #fff);
        padding: 1rem 2rem;
        margin: 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .nav-mobile .nav-item {
        margin-bottom: 1rem;
    }
    
    .nav-mobile .nav-link {
        display: block;
        padding: 0.75rem 0;
    }
    
    @media (max-width: 768px) {
        .nav-desktop {
            display: none;
        }
        
        .nav-mobile-toggle {
            display: block;
        }
        
        .nav-mobile {
            display: block;
        }
    }
</style>
