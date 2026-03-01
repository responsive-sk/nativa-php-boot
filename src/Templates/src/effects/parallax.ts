/**
 * Parallax Scrolling Effect
 */

export interface ParallaxOptions {
    speed?: number;
    breakpoint?: number;
}

/**
 * Initialize parallax scrolling effect
 */
export function initParallax(options: ParallaxOptions = {}): void {
    const speed = options.speed ?? 0.5;
    const breakpoint = options.breakpoint ?? 768;

    const hero = document.querySelector<HTMLElement>(".app-hero");
    if (!hero || window.innerWidth <= breakpoint) return;

    let ticking = false;

    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -speed;
                hero.style.transform = `translate3d(0, ${rate}px, 0)`;
                ticking = false;
            });
            ticking = true;
        }
    });
}
