// Navigation Entry Point - Direct Svelte 5 mount
import { mount } from 'svelte';
import Navigation from '../svelte/components/Navigation.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('nav-container');
    
    if (container) {
        const currentPage = container.dataset.currentPage || 'home';
        
        mount(Navigation, {
            target: container,
            props: {
                currentPage: currentPage
            }
        });
        
        console.log('✅ Navigation mounted');
    }
});
