<?php
/**
 * Homepage Hero - Manifesto Section
 * Rendered before main content in layout
 */
?>

<!-- Hero / Manifesto -->
<section class="hero-manifesto">
    <div class="hero-manifesto__bg">
        <picture class="hero-manifesto__picture">
            <!-- Desktop Large: 1366w -->
            <source
                media="(min-width: 1440px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1366,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
            >
            <!-- Desktop: 1024w -->
            <source
                media="(min-width: 1024px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1024,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
            >
            <!-- Tablet: 768w -->
            <source
                media="(min-width: 769px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
            >
            <!-- Mobile: 480w -->
            <source
                media="(min-width: 480px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_480,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
            >
            <!-- Mobile Small: 320w -->
            <source
                media="(max-width: 479px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_320,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
            >
            <!-- Fallback -->
            <img
                src="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-5.jpg"
                alt="Modern office background"
                class="hero-manifesto__bg-image"
                fetchpriority="high"
                loading="eager"
                decoding="async"
                width="1366"
                height="768"
                crossorigin="anonymous"
            >
        </picture>
        <div class="hero-manifesto__bg-overlay"></div>
        <div class="hero-manifesto__bg-shapes">
            <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--1"></div>
            <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--2"></div>
            <div class="hero-manifesto__bg-shape hero-manifesto__bg-shape--3"></div>
        </div>
    </div>

    <div class="hero-manifesto__content container">
        <h1 class="anim-block anim-block--hero">
            <span class="anim-block__line">
                <span class="anim-block__inner">
                    <span class="anim-block__text">Nativa</span>
                </span>
            </span>
            <span class="anim-block__line">
                <span class="anim-block__inner">
                    <span class="anim-block__text">CMS</span>
                </span>
            </span>
        </h1>
        <p class="hero-manifesto__text" style="margin-top: 2rem; opacity: 0.8;">
            Modern PHP 8.4+ Blog Platform with DDD Architecture
        </p>
        <div style="margin-top: 3rem;">
            <a href="/blog" class="btn btn--primary">Read Blog</a>
            <a href="/contact" class="btn btn--outline" style="margin-left: 1rem;">Contact Us</a>
        </div>
    </div>
</section>
