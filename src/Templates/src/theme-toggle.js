// Theme Toggle - Direct Svelte 5 mount
import { mount } from 'svelte';
import ThemeToggle from '../svelte/components/ThemeToggle.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('theme-toggle-container');

    if (container) {
        mount(ThemeToggle, {
            target: container
        });

        console.log('✅ ThemeToggle mounted');
    }
});
