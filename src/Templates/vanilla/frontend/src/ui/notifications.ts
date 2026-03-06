/**
 * Notification System
 * Modern notification manager with HTMX integration
 */

export interface NotificationOptions {
    type: "success" | "error" | "warning" | "info";
    message: string;
    title?: string;
    duration?: number;
    persistent?: boolean;
}

export class NotificationManager {
    private container: HTMLElement | null = null;
    private notifications: Map<string, HTMLElement> = new Map();

    constructor() {
        this.init();
    }

    /**
     * Initialize notification container
     */
    private init(): void {
        this.container = document.querySelector(".alerts-container");

        if (!this.container) {
            this.container = document.createElement("div");
            this.container.className = "alerts-container";
            document.body.appendChild(this.container);
        }
    }

    /**
     * Show notification with options
     */
    show(options: NotificationOptions): string {
        const id = this.generateId();
        const notification = this.createNotification(options, id);

        this.container?.appendChild(notification);
        this.notifications.set(id, notification);

        if (!options.persistent) {
            setTimeout(() => this.remove(id), options.duration || 5000);
        }

        return id;
    }

    /**
     * Show success notification
     */
    success(message: string, title?: string): string {
        return this.show({ type: "success", message, title });
    }

    /**
     * Show error notification (persistent)
     */
    error(message: string, title?: string): string {
        return this.show({ type: "error", message, title, persistent: true });
    }

    /**
     * Show warning notification
     */
    warning(message: string, title?: string): string {
        return this.show({ type: "warning", message, title });
    }

    /**
     * Show info notification
     */
    info(message: string, title?: string): string {
        return this.show({ type: "info", message, title });
    }

    /**
     * Remove notification by ID
     */
    remove(id: string): void {
        const notification = this.notifications.get(id);
        if (notification) {
            notification.style.opacity = "0";
            notification.style.transform = "translateX(-100%)";

            setTimeout(() => {
                notification.remove();
                this.notifications.delete(id);
            }, 300);
        }
    }

    /**
     * Clear all notifications
     */
    clear(): void {
        this.notifications.forEach((_, id) => this.remove(id));
    }

    /**
     * Create notification element
     */
    private createNotification(
        options: NotificationOptions,
        id: string,
    ): HTMLElement {
        const notification = document.createElement("div");
        notification.className = `alert alert-${options.type}`;
        notification.setAttribute("role", "alert");
        notification.dataset.id = id;

        const icon = this.getIcon(options.type);
        const title = options.title ? `<strong>${options.title}</strong>` : "";

        notification.innerHTML = `
      <div class="alert-icon">${icon}</div>
      <div class="alert-message">
        ${title ? `${title}<br>` : ""}${options.message}
      </div>
      <button class="alert-close" onclick="window.notifications.remove('${id}')">×</button>
    `;

        return notification;
    }

    /**
     * Get icon SVG for notification type
     */
    private getIcon(type: NotificationOptions["type"]): string {
        const icons = {
            success:
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
            error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
            warning:
                '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        };
        return icons[type];
    }

    /**
     * Generate unique notification ID
     */
    private generateId(): string {
        return `notification-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    }
}

// Global instance
export const notifications = new NotificationManager();

/**
 * Initialize HTMX notification integration
 */
export function initHtmxNotifications(): void {
    document.body.addEventListener("htmx:afterRequest", (event: any) => {
        const xhr = event.detail.xhr;
        const response = xhr.response;

        if (response) {
            try {
                const data =
                    typeof response === "string"
                        ? JSON.parse(response)
                        : response;

                if (data.type && data.message) {
                    if (data.success) {
                        notifications.success(data.message);
                    } else {
                        notifications.error(data.message);
                    }
                }
            } catch (e) {
                if (response.includes("alert-success")) {
                    const message = extractMessage(response, "alert-success");
                    notifications.success(message);
                } else if (response.includes("alert-error")) {
                    const message = extractMessage(response, "alert-error");
                    notifications.error(message);
                } else if (response.includes("alert-warning")) {
                    const message = extractMessage(response, "alert-warning");
                    notifications.warning(message);
                }
            }
        }
    });
}

/**
 * Extract message from HTML alert
 */
function extractMessage(html: string, alertClass: string): string {
    const div = document.createElement("div");
    div.innerHTML = html;
    const alert = div.querySelector(`.${alertClass} .alert-message`);
    return alert?.textContent?.trim() || "";
}
