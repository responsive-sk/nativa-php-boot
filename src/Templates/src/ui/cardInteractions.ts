/**
 * Card Interactions
 * Hover effects and interactions for cards
 */

/**
 * Initialize card interaction effects
 */
export function initCardInteractions(): void {
    const cards = document.querySelectorAll(".card");

    cards.forEach((card) => {
        card.addEventListener("mouseenter", function (this: HTMLElement) {
            this.classList.add("card--hovering");
        });

        card.addEventListener("mouseleave", function (this: HTMLElement) {
            this.classList.remove("card--hovering");
        });
    });
}
