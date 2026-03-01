<?php declare(strict_types=1);

/**
 * Portfolio Template - CMS Integration
 *
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

// Cloudinary hero images - primary image server
$portfolioHeroImageMobile = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_768/v1658528027/cld-sample-5.jpg';
$portfolioHeroImageDesktop = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_1280/v1658528027/cld-sample-5.jpg';

error_log("DEBUG: portfolio.php template rendering");
?>

<section class="portfolio">
    <div class="portfolio-hero">
        <div class="portfolio__overlay"></div>
        <picture class="portfolio__hero-picture">
            <source media="(min-width: 769px)" srcset="<?= $portfolioHeroImageDesktop ?>" crossorigin="anonymous">
            <img src="<?= $portfolioHeroImageMobile ?>" alt="Portfolio hero background" fetchpriority="high" loading="eager" decoding="async" class="portfolio__hero-image" width="1280" height="720" crossorigin="anonymous">
        </picture>
        <div class="portfolio__hero-content">
            <h1>Our Portfolio</h1>
            <p>Check out our latest projects and creative work</p>
        </div>
    </div>

    <div class="portfolio-filters">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Web</button>
        <button class="filter-btn">Mobile</button>
        <button class="filter-btn">Design</button>
    </div>

    <div class="gallery-masonry">
        <div class="gallery-item wide">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_800,h_400,c_fill/v1658528027/cld-sample-5.jpg" alt="Project 1" width="800" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>E-Commerce Platform</h4>
                <p>Web Development</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_400,c_fill/v1658528026/cld-sample-4.jpg" alt="Project 2" width="400" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>Mobile App</h4>
                <p>iOS & Android</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item tall">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_600,c_fill/v1658528026/cld-sample-3.jpg" alt="Project 3" width="400" height="600" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>Brand Identity</h4>
                <p>Logo Design</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_400,c_fill/v1658528025/cld-sample-2.jpg" alt="Project 4" width="400" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>Dashboard UI</h4>
                <p>UX Design</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_400,c_fill/v1658527998/sample.jpg" alt="Project 5" width="400" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>API Integration</h4>
                <p>Backend</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item wide">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_800,h_400,c_fill/v1698868564/samples/outdoor-woman.jpg" alt="Project 6" width="800" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>Landing Page</h4>
                <p>Web Design</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_400,c_fill/v1658528007/samples/bike.jpg" alt="Project 7" width="400" height="400" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>E-Learning Platform</h4>
                <p>Web App</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>

        <div class="gallery-item tall">
            <img src="https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto:best,w_400,h_600,c_fill/v1658528005/samples/sheep.jpg" alt="Project 8" width="400" height="600" loading="lazy" decoding="async" crossorigin="anonymous">
            <div class="gallery-shine"></div>
            <div class="gallery-caption">
                <h4>Fintech App</h4>
                <p>Mobile</p>
            </div>
            <div class="gallery-zoom">🔍</div>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="gallery-lightbox">
        <button class="gallery-lightbox-close">✕</button>
        <button class="gallery-lightbox-nav gallery-lightbox-prev">←</button>
        <button class="gallery-lightbox-nav gallery-lightbox-next">→</button>
        <img src="" alt="Gallery Image" width="800" height="600">
    </div>
</section>
