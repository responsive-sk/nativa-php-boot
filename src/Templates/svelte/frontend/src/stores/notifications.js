// Notification Store - Svelte 5 with runes
import { writable } from 'svelte/store';

let notificationId = 0;

export const notifications = (() => {
    const { subscribe, update } = writable([]);
    
    function addNotification(notification) {
        const id = ++notificationId;
        const newNotification = {
            id,
            ...notification
        };
        
        update(notifications => [...notifications, newNotification]);
        
        // Auto-dismiss after duration
        if (notification.duration) {
            setTimeout(() => {
                dismiss(id);
            }, notification.duration);
        }
    }
    
    function dismiss(id) {
        update(notifications =>
            notifications.filter(n => n.id !== id)
        );
    }
    
    return {
        subscribe,
        
        success: (message, duration = 5000) => {
            addNotification({
                type: 'success',
                message,
                duration
            });
        },
        
        error: (message, duration = 5000) => {
            addNotification({
                type: 'error',
                message,
                duration
            });
        },
        
        info: (message, duration = 3000) => {
            addNotification({
                type: 'info',
                message,
                duration
            });
        },
        
        dismiss: (id) => {
            dismiss(id);
        }
    };
})();
