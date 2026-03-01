/**
 * Gallery Lightbox
 * Image gallery with lightbox modal
 */

/**
 * Initialize gallery lightbox functionality
 */
export function initGalleryLightbox(): void {
    const galleryItems = document.querySelectorAll(".gallery-item");

    galleryItems.forEach((item) => {
        const img = item.querySelector("img");
        const caption = item.querySelector(".gallery-overlay h3")?.textContent;

        if (!img) return;

        item.addEventListener("click", () => {
            openLightbox(img.src, caption);
        });
    });
}

/**
 * Open lightbox modal with image
 */
function openLightbox(src: string, caption?: string): void {
    const lightbox = document.createElement("div");
    lightbox.className = "gallery-lightbox";
    lightbox.innerHTML = `
        <div class="lightbox-backdrop"></div>
        <img src="${src}" alt="${caption || ""}">
        ${caption ? `<div class="lightbox-caption">${caption}</div>` : ""}
        <button class="lightbox-close">&times;</button>
    `;

    document.body.appendChild(lightbox);

    // Trigger animation
    requestAnimationFrame(() => {
        lightbox.classList.add("active");
    });

    // Close handlers
    const close = () => {
        lightbox.classList.remove("active");
        setTimeout(() => lightbox.remove(), 300);
    };

    lightbox.querySelector(".lightbox-backdrop")?.addEventListener("click", close);
    lightbox.querySelector(".lightbox-close")?.addEventListener("click", close);

    // Close on escape
    const handleEscape = (e: KeyboardEvent) => {
        if (e.key === "Escape") {
            close();
            document.removeEventListener("keydown", handleEscape);
        }
    };
    document.addEventListener("keydown", handleEscape);
}
