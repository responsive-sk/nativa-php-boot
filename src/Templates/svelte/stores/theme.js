// Theme Store - Global theme state
import { writable } from 'svelte/store';

// Check localStorage or system preference
function getInitialTheme() {
    if (typeof window !== 'undefined') {
        const saved = localStorage.getItem('theme');
        if (saved) {
            return saved === 'dark';
        }
        // Check system preference
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    return false;
}

function createThemeStore() {
    const { subscribe, set, update } = writable(getInitialTheme());
    
    return {
        subscribe,
        toggle: () => update(isDark => {
            const newIsDark = !isDark;
            localStorage.setItem('theme', newIsDark ? 'dark' : 'light');
            applyTheme(newIsDark);
            return newIsDark;
        }),
        set: (value) => {
            localStorage.setItem('theme', value ? 'dark' : 'light');
            applyTheme(value);
            set(value);
        }
    };
}

function applyTheme(isDark) {
    if (typeof document !== 'undefined') {
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.add('light');
            document.documentElement.classList.remove('dark');
        }
    }
}

export const theme = createThemeStore();
