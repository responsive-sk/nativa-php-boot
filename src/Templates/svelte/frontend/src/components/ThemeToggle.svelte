<script>
    import { onMount } from 'svelte';
    
    let isDark = $state(false);
    
    $effect.pre(() => {
        // Apply theme when isDark changes
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    });
    
    onMount(() => {
        // Check localStorage
        const saved = localStorage.getItem('theme');
        if (saved) {
            isDark = saved === 'dark';
        } else {
            // Check system preference
            isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
    });
    
    function toggle() {
        isDark = !isDark;
    }
</script>

<button 
    class="theme-toggle-svelte" 
    onclick={toggle}
    aria-label="Toggle theme"
    type="button"
>
    <svg class="icon icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
    </svg>
    
    <svg class="icon icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
</button>

<style>
    .theme-toggle-svelte {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border: none;
        background: transparent;
        cursor: pointer;
        border-radius: 9999px;
        transition: background 0.3s ease;
    }
    
    .theme-toggle-svelte:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    
    .icon {
        width: 1.25rem;
        height: 1.25rem;
        transition: transform 0.5s ease, opacity 0.3s ease;
    }
    
    .icon--sun {
        color: #f59e0b;
    }
    
    .icon--moon {
        color: #6b7280;
    }
    
    :global(.dark) .icon--sun {
        opacity: 0;
        transform: rotate(90deg) scale(0.5);
    }
    
    :global(.dark) .icon--moon {
        opacity: 1;
        transform: rotate(0) scale(1);
    }
    
    :global(.light) .icon--sun {
        opacity: 1;
        transform: rotate(0) scale(1);
    }
    
    :global(.light) .icon--moon {
        opacity: 0;
        transform: rotate(-90deg) scale(0.5);
    }
</style>
