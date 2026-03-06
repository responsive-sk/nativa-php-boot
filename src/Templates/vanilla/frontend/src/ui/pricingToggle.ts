/**
 * Pricing Toggle
 * Monthly/Yearly pricing switch
 */

/**
 * Initialize pricing toggle functionality
 */
export function initPricingToggle(): void {
    const toggle = document.querySelector<HTMLInputElement>(
        ".pricing-toggle",
    );
    const monthlyPrices = document.querySelectorAll(".price-monthly");
    const yearlyPrices = document.querySelectorAll(".price-yearly");

    if (!toggle) return;

    toggle.addEventListener("change", () => {
        const isYearly = toggle.checked;

        monthlyPrices.forEach((el) => {
            (el as HTMLElement).style.display = isYearly ? "none" : "block";
        });

        yearlyPrices.forEach((el) => {
            (el as HTMLElement).style.display = isYearly ? "block" : "none";
        });
    });
}
