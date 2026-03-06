<script>
    import { notifications } from '../stores/notifications.js';
    import { fade, slide } from 'svelte/transition';
    import { onDestroy } from 'svelte';
    
    let items;
    
    // Subscribe to notifications store
    const unsubscribe = notifications.subscribe(value => {
        items = value;
    });
    
    // Cleanup on destroy
    onDestroy(() => {
        unsubscribe();
    });
    
    function dismiss(id) {
        notifications.dismiss(id);
    }
</script>

<div class="toast-container">
    {#each items as item (item.id)}
        <div 
            class="toast toast--{item.type}"
            in:slide={{ duration: 300 }}
            out:fade={{ duration: 200 }}
        >
            <div class="toast-content">
                <span class="toast-icon">
                    {#if item.type === 'success'}
                        ✓
                    {:else if item.type === 'error'}
                        ✕
                    {:else}
                        ℹ
                    {/if}
                </span>
                <span class="toast-message">{item.message}</span>
            </div>
            
            <button 
                class="toast-dismiss"
                on:click={() => dismiss(item.id)}
                aria-label="Dismiss"
            >
                ×
            </button>
        </div>
    {/each}
</div>

<style>
    .toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        pointer-events: none;
    }
    
    .toast {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: var(--toast-bg, #fff);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 300px;
        max-width: 500px;
        pointer-events: auto;
        border-left: 4px solid;
    }
    
    .toast--success {
        border-left-color: var(--success, #28a745);
    }
    
    .toast--error {
        border-left-color: var(--error, #dc3545);
    }
    
    .toast--info {
        border-left-color: var(--info, #17a2b8);
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }
    
    .toast-icon {
        font-size: 1.25rem;
        font-weight: bold;
    }
    
    .toast--success .toast-icon {
        color: var(--success, #28a745);
    }
    
    .toast--error .toast-icon {
        color: var(--error, #dc3545);
    }
    
    .toast--info .toast-icon {
        color: var(--info, #17a2b8);
    }
    
    .toast-message {
        color: var(--text-primary, #1a1a1a);
        flex: 1;
    }
    
    .toast-dismiss {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-secondary, #666);
        cursor: pointer;
        padding: 0;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }
    
    .toast-dismiss:hover {
        color: var(--text-primary, #1a1a1a);
    }
    
    @media (max-width: 768px) {
        .toast-container {
            top: auto;
            bottom: 1rem;
            right: 1rem;
            left: 1rem;
        }
        
        .toast {
            min-width: auto;
            max-width: none;
        }
    }
</style>
