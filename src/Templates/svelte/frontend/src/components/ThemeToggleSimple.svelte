<script>
    console.log('ThemeToggle Svelte component loaded!');
    
    let isDark = false;
    
    function toggle() {
        isDark = !isDark;
        console.log('Theme toggled:', isDark ? 'dark' : 'light');
    }
</script>

<button onclick={toggle} type="button">
    {isDark ? '🌙 Dark' : '☀️ Light'}
</button>

<style>
    button {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        border: 2px solid #ccc;
        background: white;
        cursor: pointer;
    }
    
    button:hover {
        background: #f0f0f0;
    }
</style>
