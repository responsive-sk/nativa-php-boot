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
        const toggle = document.querySelector('.nav-primary__mobile-toggle, .mobile-menu-btn');
        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                const shouldBeExpanded = !isExpanded;
                
                toggle.setAttribute('aria-expanded', shouldBeExpanded);
                mobileMenu.hidden = !shouldBeExpanded;
                
                console.log('Mobile menu:', shouldBeExpanded ? 'opened' : 'closed');
            });
        } else {
            console.warn('Mobile menu toggle button not found');
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
    
    // Add test function for verifying enhancement
    window.testNavigationEnhancement = function() {
        const tests = {
            navigationEnhanced: false,
            themeToggleWorks: false,
            mobileMenuWorks: false,
        };
        
        // Test 1: Check if navigation was enhanced
        const nav = document.querySelector('[data-svelte-hydrate="navigation"]');
        tests.navigationEnhanced = !!nav;
        
        // Test 2: Check if theme toggle has event listener
        const themeToggles = document.querySelectorAll('.theme-toggle');
        tests.themeToggleWorks = themeToggles.length > 0;
        
        // Test 3: Check if mobile menu toggle has event listener
        const mobileToggle = document.querySelector('.nav-primary__mobile-toggle, .mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');
        tests.mobileMenuWorks = !!(mobileToggle && mobileMenu);
        
        // Log results
        console.log('🧪 Navigation Enhancement Tests:');
        console.log('  ✅ Navigation enhanced:', tests.navigationEnhanced);
        console.log('  ✅ Theme toggle found:', tests.themeToggleWorks);
        console.log('  ✅ Mobile menu found:', tests.mobileMenuWorks);
        
        const allPassed = Object.values(tests).every(v => v === true);
        console.log(allPassed ? '✅ All tests passed!' : '❌ Some tests failed');
        
        return {
            passed: allPassed,
            results: tests
        };
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceNavigation);
    } else {
        enhanceNavigation();
    }
}
