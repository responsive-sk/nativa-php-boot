/**
 * Portfolio Page JavaScript
 * Fullscreen gallery with keyboard navigation
 */

import './portfolio.css';

interface PortfolioImage {
    id: string;
    title: string;
    category: string;
}

class PortfolioGallery {
    private images: PortfolioImage[];
    private currentIndex: number;
    private gallery: HTMLElement | null;
    private galleryImage: HTMLImageElement | null;
    private galleryTitle: HTMLElement | null;
    private galleryCategory: HTMLElement | null;

    constructor() {
        this.images = [];
        this.currentIndex = 0;
        this.gallery = null;
        this.galleryImage = null;
        this.galleryTitle = null;
        this.galleryCategory = null;
        
        this.init();
    }

    private init(): void {
        this.cacheElements();
        this.loadImages();
        this.bindEvents();
    }

    private cacheElements(): void {
        this.gallery = document.getElementById('portfolio-gallery');
        this.galleryImage = this.gallery?.querySelector('.portfolio-gallery__image') || null;
        this.galleryTitle = this.gallery?.querySelector('.portfolio-gallery__title') || null;
        this.galleryCategory = this.gallery?.querySelector('.portfolio-gallery__category') || null;
    }

    private loadImages(): void {
        const cards = document.querySelectorAll('.portfolio-card');
        cards.forEach(card => {
            const imageWrapper = card.querySelector('.portfolio-card__image-wrapper');
            const img = imageWrapper?.querySelector('img');
            const title = card.querySelector('.portfolio-card__title')?.textContent || '';
            const category = card.querySelector('.portfolio-card__category')?.textContent || '';
            
            if (img && img.src) {
                this.images.push({
                    id: img.src,
                    title,
                    category
                });
            }
        });
    }

    private bindEvents(): void {
        // Filter buttons
        const filterButtons = document.querySelectorAll('.portfolio-filters__item');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleFilter(e));
        });

        // Zoom buttons
        const zoomButtons = document.querySelectorAll('.portfolio-card__zoom');
        zoomButtons.forEach((btn, index) => {
            btn.addEventListener('click', () => this.openGallery(index));
        });

        // Gallery navigation
        const closeBtn = this.gallery?.querySelector('.portfolio-gallery__close');
        const prevBtn = this.gallery?.querySelector('.portfolio-gallery__nav--prev');
        const nextBtn = this.gallery?.querySelector('.portfolio-gallery__nav--next');

        closeBtn?.addEventListener('click', () => this.closeGallery());
        prevBtn?.addEventListener('click', () => this.prevImage());
        nextBtn?.addEventListener('click', () => this.nextImage());

        // Gallery overlay click
        this.gallery?.querySelector('.portfolio-gallery__overlay')
            ?.addEventListener('click', () => this.closeGallery());

        // Keyboard navigation
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    private handleFilter(e: Event): void {
        const target = e.target as HTMLElement;
        const filter = target.dataset.filter || 'all';
        
        // Update active state
        document.querySelectorAll('.portfolio-filters__item').forEach(btn => {
            btn.classList.remove('active');
        });
        target.classList.add('active');

        // Filter cards
        const cards = document.querySelectorAll('.portfolio-card');
        cards.forEach(card => {
            const category = card.getAttribute('data-category') || '';
            
            if (filter === 'all' || category === filter) {
                (card as HTMLElement).style.display = 'flex';
            } else {
                (card as HTMLElement).style.display = 'none';
            }
        });
    }

    private openGallery(index: number): void {
        if (!this.gallery || !this.galleryImage || !this.galleryTitle || !this.galleryCategory) return;
        
        this.currentIndex = index;
        this.updateGalleryContent();
        
        this.gallery.classList.add('is-active');
        this.gallery.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    private closeGallery(): void {
        if (!this.gallery) return;
        
        this.gallery.classList.remove('is-active');
        this.gallery.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    private updateGalleryContent(): void {
        if (!this.galleryImage || !this.galleryTitle || !this.galleryCategory) return;
        
        const image = this.images[this.currentIndex];
        
        // Use high-res Cloudinary demo image with CORS
        let highResUrl = image.id.replace('/w_600/', '/w_1200/');
        
        // Ensure we're using the demo cloud for CORS
        if (highResUrl.includes('epithemic')) {
            highResUrl = highResUrl.replace('epithemic', 'demo');
        }
        
        this.galleryImage.src = highResUrl;
        this.galleryImage.alt = image.title;
        this.galleryTitle.textContent = image.title;
        this.galleryCategory.textContent = image.category;
    }

    private prevImage(): void {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateGalleryContent();
    }

    private nextImage(): void {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateGalleryContent();
    }

    private handleKeyboard(e: KeyboardEvent): void {
        if (!this.gallery?.classList.contains('is-active')) return;
        
        switch (e.key) {
            case 'Escape':
                this.closeGallery();
                break;
            case 'ArrowLeft':
                this.prevImage();
                break;
            case 'ArrowRight':
                this.nextImage();
                break;
        }
    }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new PortfolioGallery());
} else {
    new PortfolioGallery();
}
