/**
 * Scroll Animations using Intersection Observer
 */

import {
    fadeIn,
    slideInLeft,
    slideInRight,
    scaleIn,
    type AnimationOptions,
} from "./motion.js";

export interface ScrollAnimationOptions {
    threshold?: number;
    rootMargin?: string;
}

/**
 * Initialize scroll-triggered animations
 */
export function initScrollAnimations(
    options: ScrollAnimationOptions = {},
): void {
    const threshold = options.threshold ?? 0.1;
    const rootMargin = options.rootMargin ?? "50px";

    const animatedElements = document.querySelectorAll("[data-animate]");

    if (!animatedElements.length) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const element = entry.target as HTMLElement;
                    const animation =
                        element.dataset.animate || "fadeIn";
                    const duration = parseInt(
                        element.dataset.duration || "300",
                    );

                    element.classList.add("animate-in");

                    switch (animation) {
                        case "fadeIn":
                            fadeIn(element, { duration });
                            break;
                        case "slideInLeft":
                            slideInLeft(element, { duration });
                            break;
                        case "slideInRight":
                            slideInRight(element, { duration });
                            break;
                        case "scaleIn":
                            scaleIn(element, { duration });
                            break;
                    }

                    observer.unobserve(element);
                }
            });
        },
        { threshold, rootMargin },
    );

    animatedElements.forEach((el) => observer.observe(el));
}
