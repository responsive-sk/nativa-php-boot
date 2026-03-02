/**
 * Homepage JavaScript - Luxury Winery Style
 * GSAP Scroll Animations
 */

import './home.css';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// GSAP Scroll Animations
document.addEventListener('DOMContentLoaded', () => {
    console.log('Homepage loaded with GSAP animations');

    // Register ScrollTrigger
    gsap.registerPlugin(ScrollTrigger);

    // Check if dark mode should be default
    const STORAGE_KEY = 'admin-theme';
    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Set dark mode as default if no preference saved
    if (!savedTheme || savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    // Hero Manifesto - Fade In
    gsap.from('.hero-manifesto__text', {
        duration: 1.5,
        opacity: 0,
        y: 60,
        ease: 'power3.out',
        delay: 0.5
    });

    gsap.from('.hero-manifesto__link', {
        duration: 1.2,
        opacity: 0,
        y: 30,
        ease: 'power3.out',
        delay: 0.8
    });

    gsap.from('.hero-manifesto__scroll', {
        duration: 1,
        opacity: 0,
        y: 20,
        ease: 'power3.out',
        delay: 1.2
    });

    // Animate background shapes
    gsap.from('.hero-manifesto__bg-shape', {
        duration: 2,
        opacity: 0,
        scale: 0.8,
        stagger: 0.3,
        ease: 'power3.out',
        delay: 0.2
    });

    // Parallax effect for background image
    gsap.to('.hero-manifesto__bg-image', {
        scrollTrigger: {
            trigger: '.hero-manifesto',
            start: 'top top',
            end: 'bottom top',
            scrub: true
        },
        y: 150,
        ease: 'none'
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Parallax Fade Effect - Hero content fades on scroll
    gsap.to('.hero-manifesto__content', {
        scrollTrigger: {
            trigger: '.hero-manifesto',
            start: 'top top',
            end: 'bottom top',
            scrub: true
        },
        opacity: 0,
        y: -100,
        ease: 'none'
    });

    // Section fade effects - current section fades as you scroll past
    gsap.utils.toArray('.section').forEach((section: Element) => {
        gsap.to(section, {
            scrollTrigger: {
                trigger: section,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
            },
            opacity: 0.3,
            y: -50,
            ease: 'none'
        });
    });

    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
        });
    }

    // Mobile Menu Toggle - Dual Toggle Pattern (class + inline styles)
    const mobileMenuBtn = document.querySelector<HTMLButtonElement>('.mobile-menu-btn');
    const mobileMenu = document.querySelector<HTMLElement>('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu__close');

    if (mobileMenuBtn && mobileMenu && mobileMenuClose) {
        // CRITICAL: Set initial inline style for maximum compatibility
        mobileMenu.style.display = 'none';
        mobileMenu.style.position = 'fixed';
        mobileMenu.style.top = '0';
        mobileMenu.style.left = '0';
        mobileMenu.style.right = '0';
        mobileMenu.style.bottom = '0';
        mobileMenu.style.background = 'rgba(11, 15, 23, 0.98)';
        mobileMenu.style.zIndex = '9999';
        mobileMenu.style.overflowY = 'auto';
        mobileMenu.style.opacity = '0';
        mobileMenu.style.visibility = 'hidden';

        const openMenu = () => {
            mobileMenu.removeAttribute('hidden');
            mobileMenuBtn.setAttribute('aria-expanded', 'true');
            
            // CRITICAL: Toggle BOTH class AND inline style
            mobileMenu.classList.add('active');
            mobileMenu.style.display = 'block';
            mobileMenu.style.opacity = '1';
            mobileMenu.style.visibility = 'visible';
            
            document.body.style.overflow = 'hidden';
            console.log('[Mobile Menu] Opened');
        };

        const closeMenu = () => {
            mobileMenu.setAttribute('hidden', '');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            
            // CRITICAL: Toggle BOTH class AND inline style
            mobileMenu.classList.remove('active');
            mobileMenu.style.display = 'none';
            mobileMenu.style.opacity = '0';
            mobileMenu.style.visibility = 'hidden';
            
            document.body.style.overflow = '';
            console.log('[Mobile Menu] Closed');
        };

        // CRITICAL: Use both click and touchend for maximum compatibility
        mobileMenuBtn.addEventListener('click', openMenu);
        mobileMenuBtn.addEventListener('touchend', (e) => {
            e.preventDefault();
            openMenu();
        });

        mobileMenuClose.addEventListener('click', closeMenu);
        mobileMenuClose.addEventListener('touchend', (e) => {
            e.preventDefault();
            closeMenu();
        });

        // Close on link click - CRITICAL: Don't use preventDefault on links!
        mobileMenu.querySelectorAll('.mobile-menu__link').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.hasAttribute('hidden')) {
                closeMenu();
            }
        });

        console.log('[Mobile Menu] Initialized with dual toggle pattern');
    }

    // Section Headers - Fade In Up
    gsap.utils.toArray('.section__header').forEach((header: Element) => {
        gsap.from(header, {
            scrollTrigger: {
                trigger: header,
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            duration: 0.8,
            opacity: 0,
            y: 40,
            ease: 'power3.out'
        });
    });

    // Featured Items - Stagger Fade In
    gsap.from('.featured-item', {
        scrollTrigger: {
            trigger: '.featured-grid',
            start: 'top 75%',
            toggleActions: 'play none none reverse'
        },
        duration: 0.8,
        opacity: 0,
        y: 60,
        stagger: 0.2,
        ease: 'power3.out'
    });

    // Article Showcase - Stagger Fade In
    gsap.from('.article-showcase', {
        scrollTrigger: {
            trigger: '.articles-showcase',
            start: 'top 75%',
            toggleActions: 'play none none reverse'
        },
        duration: 0.8,
        opacity: 0,
        y: 50,
        stagger: 0.15,
        ease: 'power3.out'
    });

    // Visit Items - Stagger Scale In
    gsap.from('.visit-item', {
        scrollTrigger: {
            trigger: '.visit-grid',
            start: 'top 75%',
            toggleActions: 'play none none reverse'
        },
        duration: 0.6,
        opacity: 0,
        scale: 0.95,
        stagger: 0.15,
        ease: 'power3.out'
    });

    // Join Content - Fade In Up
    gsap.from('.join-content', {
        scrollTrigger: {
            trigger: '.section--join',
            start: 'top 75%',
            toggleActions: 'play none none reverse'
        },
        duration: 0.8,
        opacity: 0,
        y: 40,
        ease: 'power3.out'
    });

    // Numbered elements - Counter animation
    gsap.utils.toArray('.featured-item__number').forEach((num: Element) => {
        gsap.from(num, {
            scrollTrigger: {
                trigger: num,
                start: 'top 80%',
                toggleActions: 'play none none reverse'
            },
            duration: 1,
            opacity: 0,
            y: 20,
            ease: 'power3.out'
        });
    });
});
