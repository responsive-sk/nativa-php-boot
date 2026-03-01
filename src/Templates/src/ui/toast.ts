/**
 * Toast Notifications
 * Lightweight toast alternative to full notifications
 */

import { Icons } from "@core/icons.js";

export type ToastType = "success" | "error" | "warning" | "info";

export interface ToastOptions {
    duration?: number;
    fadeDuration?: number;
}

const toastIcons: Record<ToastType, string> = {
    success: Icons.success,
    error: Icons.error,
    warning: Icons.warning,
    info: Icons.info,
};

function getToastIcon(type: ToastType): string {
    return toastIcons[type] ?? toastIcons.info;
}

/**
 * Show toast notification
 */
export function showToast(
    message: string,
    type: ToastType = "success",
    options: ToastOptions = {},
): void {
    const duration = options.duration ?? 5000;
    const fadeDuration = options.fadeDuration ?? 300;

    const toast = document.createElement("div");
    toast.className = "app-toast toast-" + type;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${getToastIcon(type)}</span>
            <span class="toast-message">${message}</span>
        </div>
        <button class="toast-close" aria-label="Close">&times;</button>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add("fade-out");
        setTimeout(() => toast.remove(), fadeDuration);
    }, duration);

    toast.querySelector(".toast-close")?.addEventListener("click", () => {
        toast.classList.add("fade-out");
        setTimeout(() => toast.remove(), fadeDuration);
    });
}
