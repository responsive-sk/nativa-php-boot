// Toast Entry Point - Global notifications
import Toast from '../svelte/components/Toast.svelte';

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Create container if it doesn't exist
    let container = document.getElementById('toast-container');
    
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    
    new Toast({
        target: container
    });
    
    console.log('✅ Toast notifications ready');
});

// Export for manual mounting if needed
export default Toast;
