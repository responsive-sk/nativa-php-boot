/**
 * Active Navigation State
 * Highlights current nav item based on URL
 */

/**
 * Initialize active navigation state
 */
export function initNavActive(): void {
    const currentPath = window.location.pathname;

    document.querySelectorAll(".nav a, .sidebar-link").forEach((link) => {
        const href = link.getAttribute("href");
        if (!href) return;

        // Handle root path
        if (currentPath === "/" && href === "/") {
            link.classList.add("active");
            return;
        }

        // Handle other paths
        if (href !== "/" && currentPath.startsWith(href)) {
            link.classList.add("active");
        }
    });
}
