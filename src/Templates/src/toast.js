// Toast Entry Point - Direct Svelte 5 mount
import { mount } from 'svelte';
import Toast from '../svelte/components/Toast.svelte';
import { notifications } from '../svelte/stores/notifications.js';

// Make notifications globally available for demo
if (typeof window !== 'undefined') {
    window.notifications = notifications;
}

// Auto-mount when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Create container if it doesn't exist
    let container = document.getElementById('toast-container');
    
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    
    mount(Toast, {
        target: container
    });
    
    console.log('✅ Toast notifications ready');
});
