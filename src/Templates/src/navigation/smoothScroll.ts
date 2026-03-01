/**
 * Smooth Scroll Utility
 * Handles anchor link scrolling with offset support
 */

export interface ScrollOptions {
    offset?: number;
    behavior?: ScrollBehavior;
}

/**
 * Smooth scroll to an element
 */
export function smoothScroll(
    anchor: HTMLElement,
    options: ScrollOptions = {},
): void {
    const offset = options.offset ?? 80;
    const behavior = options.behavior ?? "smooth";

    window.scrollTo({
        top: (anchor as HTMLElement).offsetTop - offset,
        behavior,
    });
}

/**
 * Initialize smooth scroll for anchor links
 */
export function initSmoothScroll(): void {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (this: HTMLAnchorElement, e) {
            e.preventDefault();
            const href = this.getAttribute("href");
            if (!href || href === "#") return;

            const target = document.querySelector<HTMLElement>(href);
            if (target) {
                smoothScroll(target);
            }
        });
    });
}
