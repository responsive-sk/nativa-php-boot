/**
 * Admin Dashboard JavaScript
 */

import './admin.css';

// Theme Toggle
function initThemeToggle() {
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;
    
    const body = document.body;
    const STORAGE_KEY = 'admin-theme';
    
    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    
    if (savedTheme === 'light' || (!savedTheme && prefersLight)) {
        body.classList.add('light-mode');
        toggle.classList.add('theme-toggle--light');
    }
    
    toggle.addEventListener('click', function() {
        body.classList.toggle('light-mode');
        toggle.classList.toggle('theme-toggle--light');
        
        const isLight = body.classList.contains('light-mode');
        localStorage.setItem(STORAGE_KEY, isLight ? 'light' : 'dark');
    });
}

// Update Clock
function initClock() {
    const timeEl = document.getElementById('time');
    const dateEl = document.getElementById('date');
    
    if (!timeEl || !dateEl) return;
    
    function update() {
        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString('sk-SK', { hour: '2-digit', minute: '2-digit' });
        dateEl.textContent = now.toLocaleDateString('sk-SK');
    }
    
    update();
    setInterval(update, 1000);
}

// Copy to Clipboard
function initCopyButtons() {
    document.querySelectorAll('.copy').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text);
            this.textContent = 'Copied';
            setTimeout(() => this.textContent = 'Copy', 2000);
        });
    });
}

// Auto-save Notes
function initNotes() {
    const notes = document.getElementById('notes');
    if (!notes) return;
    
    notes.value = localStorage.getItem('admin-notes') || '';
    notes.addEventListener('input', () => localStorage.setItem('admin-notes', notes.value));
}

// Initialize all
document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initClock();
    initCopyButtons();
    initNotes();
    
    console.log('%c✅ Admin Dashboard Loaded', 'color: #7c5cff; font-size: 14px; font-weight: bold');
});
