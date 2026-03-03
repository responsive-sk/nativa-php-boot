/**
 * Documentation Page JavaScript
 * Search, navigation, and scroll handling
 */

import './docs.css';

document.addEventListener('DOMContentLoaded', () => {
    console.log('Documentation page loaded');

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                }
            }
        });
    });

    // Active navigation highlighting
    const sections = document.querySelectorAll('.docs__section');
    const navLinks = document.querySelectorAll('.docs__nav-link');

    const observerOptions: IntersectionObserverInit = {
        root: null,
        rootMargin: '-20% 0px -60% 0px',
        threshold: 0,
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                if (id) {
                    navLinks.forEach((link) => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${id}`) {
                            link.classList.add('active');
                        }
                    });
                }
            }
        });
    }, observerOptions);

    sections.forEach((section) => observer.observe(section));

    // Search functionality
    const searchInput = document.getElementById('docsSearch') as HTMLInputElement;
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = (e.target as HTMLInputElement).value.toLowerCase();
            const sections = document.querySelectorAll('.docs__section');

            sections.forEach((section) => {
                const text = section.textContent?.toLowerCase() || '';
                const matches = text.includes(query);
                (section as HTMLElement).style.display = matches ? 'block' : 'none';
            });
        });
    }

    // Back to top button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        const toggleBackToTop = () => {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        };

        window.addEventListener('scroll', toggleBackToTop);

        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });
    }

    // Code block copy buttons
    document.querySelectorAll('.code-block').forEach((block) => {
        const pre = block.querySelector('pre');
        if (pre) {
            const copyBtn = document.createElement('button');
            copyBtn.className = 'code-block__copy';
            copyBtn.textContent = 'Copy';
            copyBtn.setAttribute('aria-label', 'Copy code');

            block.appendChild(copyBtn);

            copyBtn.addEventListener('click', async () => {
                const code = pre.textContent || '';
                try {
                    await navigator.clipboard.writeText(code);
                    copyBtn.textContent = 'Copied!';
                    setTimeout(() => {
                        copyBtn.textContent = 'Copy';
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
            });
        }
    });

    // Theme consistency check
    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);

    console.log(`[Docs] Theme initialized: ${savedTheme}`);
});
