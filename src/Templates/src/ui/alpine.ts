/**
 * Alpine.js Integration
 * Setup and data registration for Alpine.js
 */

/**
 * Initialize Alpine.js on body and HTMX swaps
 */
export function setupAlpine(): void {
    if (typeof window !== "undefined" && (window as any).Alpine) {
        (window as any).Alpine.init(document.body);

        document.addEventListener("htmx:afterSwap", () => {
            (window as any).Alpine.initTree(document.body);
        });
    }
}

/**
 * Register Alpine.js data
 */
export function alpineData<T>(name: string, data: T): T {
    if (typeof window !== "undefined" && (window as any).Alpine) {
        return (window as any).Alpine.data(name, () => data);
    }
    return data;
}
