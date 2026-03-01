/**
 * Theme Toggle
 * Dark/Light theme switching with localStorage persistence
 */

/**
 * Initialize theme from localStorage
 * Defaults to dark mode if no preference saved
 */
export function initTheme(): void {
    const html = document.documentElement;
    const savedTheme = localStorage.getItem("theme");
    const theme = savedTheme ?? "dark";

    html.setAttribute("data-theme", theme);
}

/**
 * Initialize theme toggle buttons
 * Handles click and touch events
 */
export function initThemeToggle(): void {
    const toggles =
        document.querySelectorAll<HTMLButtonElement>(".theme-toggle");

    const handleToggle = () => {
        const html = document.documentElement;
        const currentTheme = html.getAttribute("data-theme") ?? "dark";
        const newTheme = currentTheme === "dark" ? "light" : "dark";

        html.setAttribute("data-theme", newTheme);
        localStorage.setItem("theme", newTheme);
    };

    toggles.forEach((toggle) => {
        toggle.addEventListener("click", handleToggle);
        toggle.addEventListener("touchend", (e) => {
            e.preventDefault();
            handleToggle();
        });
    });
}
