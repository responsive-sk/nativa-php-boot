/**
 * CSRF Token Manager
 * Handles CSRF token retrieval and validation for forms
 */

export class CsrfManager {
    private static token: string | null = null;
    private static tokenName: string = "_csrf";

    /**
     * Get CSRF token from meta tag or hidden input
     */
    static getToken(): string | null {
        if (this.token) {
            return this.token;
        }

        // Try meta tag first
        const metaTag = document.querySelector(
            'meta[name="csrf-token"]',
        ) as HTMLMetaElement;
        if (metaTag) {
            this.token = metaTag.content;
            return this.token;
        }

        // Try hidden input
        const input = document.querySelector(
            `input[name="${this.tokenName}"]`,
        ) as HTMLInputElement;
        if (input) {
            this.token = input.value;
            return this.token;
        }

        return null;
    }

    /**
     * Get CSRF token name
     */
    static getTokenName(): string {
        return this.tokenName;
    }

    /**
     * Set CSRF token name
     */
    static setTokenName(name: string): void {
        this.tokenName = name;
    }

    /**
     * Add CSRF token to form
     */
    static addToForm(form: HTMLFormElement): void {
        const token = this.getToken();
        if (!token) return;

        const existing = form.querySelector(
            `input[name="${this.tokenName}"]`,
        );
        if (existing) return;

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = this.tokenName;
        input.value = token;
        form.appendChild(input);
    }

    /**
     * Get CSRF token header for AJAX requests
     */
    static getHeader(): Record<string, string> {
        const token = this.getToken();
        if (!token) return {};

        return { "X-CSRF-Token": token };
    }
}
