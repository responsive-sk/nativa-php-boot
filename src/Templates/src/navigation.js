// Navigation Entry Point
import Navigation from '../svelte/components/Navigation.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('nav-container');
    
    if (container) {
        const currentPage = container.dataset.currentPage || 'home';
        
        new Navigation({
            target: container,
            props: {
                currentPage: currentPage
            }
        });
        
        console.log('✅ Navigation mounted');
    }
});

// Export for manual mounting if needed
export default Navigation;
