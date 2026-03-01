/**
 * Visual Effects Module
 * Animations and visual enhancements
 */

import { fadeIn, slideInLeft, slideInRight, scaleIn } from "./motion.js";

export interface GoldTextOptions {
    animationDelay?: number;
}

/**
 * Apply gold text effect - splits text into animated characters
 */
export function initGoldTextEffect(options: GoldTextOptions = {}): void {
    const delay = options.animationDelay ?? 0.05;

    document.querySelectorAll(".text-gold-gradient").forEach((el) => {
        const text = el.textContent ?? "";
        el.innerHTML = "";

        text.split("").forEach((char, i) => {
            const span = document.createElement("span");
            span.textContent = char;
            span.style.animationDelay = `${i * delay}s`;
            span.classList.add("gold-char");
            el.appendChild(span);
        });
    });
}

export { fadeIn, fadeOut, slideInLeft, slideInRight, scaleIn } from "./motion.js";
export { initScrollAnimations } from "./scrollAnimations.js";
export { initParallax } from "./parallax.js";
