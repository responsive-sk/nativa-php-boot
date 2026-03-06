// Navigation Enhancement Script
// Enhances existing PHP navigation with Svelte interactivity
// Does NOT replace - just enhances!

import { mount } from 'svelte';
import NavigationEnhance from '../svelte/components/NavigationEnhance.svelte';

/**
 * Enhance existing PHP navigation with Svelte interactivity
 * Call this function to add enhancements to PHP-rendered navigation
 */
export function enhanceNavigation() {
    // Find PHP-rendered navigation
    const nav = document.querySelector('[data-svelte-hydrate="navigation"]');
    const mobileMenu = document.querySelector('[data-svelte-hydrate="mobile-menu"]');
    
    if (nav) {
        const currentPage = nav.dataset.page || 'home';
        const isGuest = nav.dataset.isGuest === 'true';
        
        // Mount Svelte component ON TOP of PHP HTML
        // Svelte will enhance behavior, not replace DOM
        mount(NavigationEnhance, {
            target: nav,
            props: {
                currentPage,
                isGuest,
                enhanceOnly: true  // Important: don't replace HTML
            }
        });
        
        console.log('✅ Navigation enhanced with Svelte');
    }
    
    if (mobileMenu) {
        // Enhance mobile menu toggle behavior
        const toggle = mobileMenu.querySelector('.nav-primary__mobile-toggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.hidden = isExpanded;
            });
        }
        
        console.log('✅ Mobile menu enhanced');
    }
    
    // Enhance theme toggle
    const themeToggles = document.querySelectorAll('.theme-toggle');
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            // Toggle dark mode
            document.documentElement.classList.toggle('dark');
            
            // Save preference
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            console.log('Theme toggled:', isDark ? 'dark' : 'light');
        });
    });
}

// Auto-enhance on load (expose globally for inline script)
if (typeof window !== 'undefined') {
    window.enhanceNavigation = enhanceNavigation;
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceNavigation);
    } else {
        enhanceNavigation();
    }
}
