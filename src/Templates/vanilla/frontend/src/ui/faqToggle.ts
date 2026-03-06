/**
 * FAQ Toggle
 * Accordion-style FAQ expand/collapse
 */

/**
 * Initialize FAQ toggle functionality
 */
export function initFaqToggle(): void {
    const faqItems = document.querySelectorAll(".faq-item");

    faqItems.forEach((item) => {
        const header = item.querySelector(".faq-header");
        if (!header) return;

        header.addEventListener("click", () => {
            const isOpen = item.classList.contains("faq-item--open");

            // Close all other items
            faqItems.forEach((otherItem) => {
                if (otherItem !== item) {
                    otherItem.classList.remove("faq-item--open");
                }
            });

            // Toggle current item
            item.classList.toggle("faq-item--open", !isOpen);
        });
    });
}
