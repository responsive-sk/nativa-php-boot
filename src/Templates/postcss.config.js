export default {
  plugins: {
    '@fullhuman/postcss-purgecss': {
      content: [
        './**/*.php',
        './**/*.js',
        './**/*.ts',
        './src/**/*.css',
      ],
      // Safelist - keep these classes even if not found in content
      safelist: {
        standard: [
          // Navigation (critical for all pages)
          'nav-primary',
          'nav-primary__inner',
          'nav-primary__logo',
          'nav-primary__list',
          'nav-primary__link',
          'nav-primary__number',
          'nav-primary__text',
          'nav-primary__actions',
          'nav-primary__mobile-toggle',
          'mobile-toggle__icon',
          'mobile-toggle__icon::before',
          'mobile-toggle__icon::after',
          'mobile-menu',
          'mobile-menu__inner',
          'mobile-menu__link',
          'mobile-menu__number',
          'mobile-menu__text',
          'mobile-menu__close',
          'mobile-close__icon',
          'mobile-close__icon::before',
          
          // Theme toggle
          'theme-toggle',
          'theme-toggle__icon',
          'theme-toggle__icon--sun',
          'theme-toggle__icon--moon',
          'icon-sun',
          'icon-moon',
          
          // Buttons (used everywhere)
          'btn',
          'btn--primary',
          'btn--outline',
          'btn--sm',
          'btn--lg',
          'btn--block',
          
          // Animations (critical for hero)
          'anim-block',
          'anim-block__line',
          'anim-block__inner',
          'anim-block__text',
          'anim-block--hero',
          'anim-block--heading',
          'anim-block__label',
          'anim-block.is-visible',
          'is-visible',
          
          // Data attributes (not detected by PurgeCSS)
          'data-animate',
          'data-theme',
          
          // Critical CSS ID
          'critical-css',
          
          // Form elements
          'blog-search',
          'blog-search__form',
          'blog-search__input',
          'blog-search__input-group',
          'blog-search__submit',
          'htmx-indicator',
          'animate-spin',
          
          // Article cards
          'article-card',
          'article-card__image-wrapper',
          'article-card__image',
          'article-card__content',
          'article-card__title',
          'article-card__excerpt',
          'article-card__meta',
          'article-card__author',
          'article-card__author-avatar',
          'article-card__date',
          'article-card__icon',
          'article-card__link',
          'article-card__tag',
          'article-card__tags',
          
          // Sections
          'section',
          'section__header',
          'section__number',
          'section__title',
          'section__subtitle',
          'section--featured',
          'section--articles',
          'section--cta',
          
          // Featured items
          'featured-grid',
          'featured-item',
          'featured-item__content',
          'featured-item__title',
          'featured-item__desc',
          'featured-item__number',
          
          // Blog hero
          'blog-hero',
          'blog-hero__bg',
          'blog-hero__picture',
          'blog-hero__bg-image',
          'blog-hero__bg-overlay',
          'blog-hero__content',
          'blog-hero__title',
          'blog-hero__subtitle',
          'blog-hero__categories',
          'blog-hero__category-list',
          'blog-hero__category',
          'blog-hero__category-count',
          'blog-hero__tags',
          'blog-hero__tag-cloud',
          'blog-hero__tag',
          'blog-hero__label',
          'blog-hero__actions',
          
          // CTA
          'cta-content',
          'cta-content__title',
          'cta-content__text',
          'cta-content__actions',
          
          // Pagination
          'pagination',
          'pagination__link',
          'pagination__link--prev',
          'pagination__link--next',
          'pagination__info',
          
          // Layout
          'container',
          'site-body',
          
          // Grid
          'articles-grid',
          'services__grid',
          
          // Service cards
          'service-card',
          'service-card__icon',
          'service-card__title',
          'service-card__description',
          'service-card__meta',
          'service-card__date',
          'service-card__views',
          'service-card__icon-sm',
          'service-card__link',
          'service-card__features',
          
          // Empty state
          'empty-state',
          
          // Footer
          'site-footer',
          'site-footer__inner',
          'site-footer__top',
          'site-footer__brand',
          'site-footer__logo',
          'site-footer__dot',
          'site-footer__tagline',
          'site-footer__nav',
          'site-footer__link',
          'site-footer__bottom',
          'site-footer__copyright',
          'site-footer__legal',
          'site-footer__legal-link',
          
          // Hero manifesto
          'hero-manifesto',
          'hero-manifesto__bg',
          'hero-manifesto__picture',
          'hero-manifesto__bg-image',
          'hero-manifesto__bg-overlay',
          'hero-manifesto__bg-shapes',
          'hero-manifesto__bg-shape',
          'hero-manifesto__bg-shape--1',
          'hero-manifesto__bg-shape--2',
          'hero-manifesto__bg-shape--3',
          'hero-manifesto__content',
          'hero-manifesto__text',
          'hero-manifesto__link',
          'hero-manifesto__scroll',
          'hero-manifesto__scroll-text',
          'hero-manifesto__scroll-line',
          'hero-manifesto__scroll-number',
          
          // Utilities
          'sr-only',
          'text-center',
          'text-left',
          'text-right',
          'hidden',
          'block',
          'inline-block',
          
          // Light theme variants
          '[data-theme="light"]',
        ],
        // Regex patterns for dynamic classes
        regex: [
          /nav-primary__link--.*/,
          /mobile-menu__item--.*/,
          /btn--.*/,
          /section--.*/,
          /article-card--.*/,
          /pagination__link--.*/,
          /service-card__link.*/,
          /blog-article__.*$/,
          /author-bio__.*$/,
          /related-article-card__.*$/,
        ],
      },
      // Default extraction
      defaultExtractor: (content) => {
        const broadMatch = content.match(/[A-Za-z0-9_:/-]+/g) || [];
        return broadMatch;
      },
      // Keep CSS variables
      variables: true,
    },
    cssnano: process.env.NODE_ENV === 'production' ? {
      preset: ['default', {
        discardComments: {
          removeAll: true,
        },
        minifySelectors: true,
      }],
    } : false,
  },
};
