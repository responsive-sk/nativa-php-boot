/**
 * Motion / GSAP-like animations using native Web Animations API
 * Lightweight alternative to GSAP for simple animations
 */

export interface AnimationOptions {
    duration?: number;
    delay?: number;
    easing?: string;
}

const defaultOptions: AnimationOptions = {
    duration: 300,
    delay: 0,
    easing: "ease-out",
};

/**
 * Fade in element
 */
export function fadeIn(
    element: HTMLElement,
    options: AnimationOptions = {},
): Promise<void> {
    const { duration, delay, easing } = { ...defaultOptions, ...options };

    return new Promise((resolve) => {
        setTimeout(() => {
            element.animate(
                [
                    { opacity: 0, transform: "translateY(20px)" },
                    { opacity: 1, transform: "translateY(0)" },
                ],
                { duration, easing },
            ).onfinish = () => resolve();
        }, delay);
    });
}

/**
 * Fade out element
 */
export function fadeOut(
    element: HTMLElement,
    options: AnimationOptions = {},
): Promise<void> {
    const { duration, delay, easing } = { ...defaultOptions, ...options };

    return new Promise((resolve) => {
        setTimeout(() => {
            element.animate(
                [
                    { opacity: 1, transform: "translateY(0)" },
                    { opacity: 0, transform: "translateY(-20px)" },
                ],
                { duration, easing },
            ).onfinish = () => resolve();
        }, delay);
    });
}

/**
 * Slide in from left
 */
export function slideInLeft(
    element: HTMLElement,
    options: AnimationOptions = {},
): Promise<void> {
    const { duration, delay, easing } = { ...defaultOptions, ...options };

    return new Promise((resolve) => {
        setTimeout(() => {
            element.animate(
                [
                    { opacity: 0, transform: "translateX(-100%)" },
                    { opacity: 1, transform: "translateX(0)" },
                ],
                { duration, easing },
            ).onfinish = () => resolve();
        }, delay);
    });
}

/**
 * Slide in from right
 */
export function slideInRight(
    element: HTMLElement,
    options: AnimationOptions = {},
): Promise<void> {
    const { duration, delay, easing } = { ...defaultOptions, ...options };

    return new Promise((resolve) => {
        setTimeout(() => {
            element.animate(
                [
                    { opacity: 0, transform: "translateX(100%)" },
                    { opacity: 1, transform: "translateX(0)" },
                ],
                { duration, easing },
            ).onfinish = () => resolve();
        }, delay);
    });
}

/**
 * Scale in animation
 */
export function scaleIn(
    element: HTMLElement,
    options: AnimationOptions = {},
): Promise<void> {
    const { duration, delay, easing } = { ...defaultOptions, ...options };

    return new Promise((resolve) => {
        setTimeout(() => {
            element.animate(
                [
                    { opacity: 0, transform: "scale(0.9)" },
                    { opacity: 1, transform: "scale(1)" },
                ],
                { duration, easing },
            ).onfinish = () => resolve();
        }, delay);
    });
}

/**
 * Stagger animation for multiple elements
 */
export function stagger(
    elements: HTMLElement[],
    animation: (el: HTMLElement, index: number) => Promise<void>,
    staggerDelay: number = 100,
): Promise<void[]> {
    return Promise.all(
        elements.map(
            (el, i) =>
                new Promise<void>((resolve) => {
                    setTimeout(() => {
                        animation(el, i).then(resolve);
                    }, i * staggerDelay);
                }),
        ),
    );
}
