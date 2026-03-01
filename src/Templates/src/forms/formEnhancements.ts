/**
 * Form Enhancements
 * Auto-resize textareas and focus states
 */

/**
 * Initialize form enhancement features
 */
export function initFormEnhancements(): void {
    document.querySelectorAll(".form-control").forEach((input) => {
        // Focus state handling
        input.addEventListener("focus", () => {
            input.parentElement?.classList.add("is-focused");
        });

        input.addEventListener("blur", () => {
            if (!(input as HTMLInputElement).value) {
                input.parentElement?.classList.remove("is-focused");
            }
        });

        // Auto-resize textarea
        if (input.tagName === "TEXTAREA") {
            input.addEventListener("input", function (this: HTMLTextAreaElement) {
                this.style.height = "auto";
                this.style.height = `${this.scrollHeight}px`;
            });
        }
    });
}
