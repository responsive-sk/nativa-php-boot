// Theme Toggle - Auto-mounting Svelte Component
import ThemeToggle from '../svelte/components/ThemeToggle.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('theme-toggle-container');
    
    if (container) {
        new ThemeToggle({
            target: container
        });
        
        console.log('✅ ThemeToggle mounted');
    }
});

// Export for manual mounting if needed
export default ThemeToggle;
