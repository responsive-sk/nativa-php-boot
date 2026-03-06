<?php declare(strict_types = 1);

/**
 * Portfolio Template - Abstract Cards + Fullscreen Gallery.
 *
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

// Cloudinary images for gallery - using demo account with CORS enabled
$portfolioImages = [
    ['id' => 'cld-sample-5', 'title' => 'Mountain Landscape', 'category' => 'Photography'],
    ['id' => 'cld-sample-4', 'title' => 'Urban Architecture', 'category' => 'Architecture'],
    ['id' => 'cld-sample-3', 'title' => 'Ocean Sunset', 'category' => 'Nature'],
    ['id' => 'cld-sample-2', 'title' => 'Forest Path', 'category' => 'Nature'],
    ['id' => 'sample', 'title' => 'Desert Dunes', 'category' => 'Travel'],
    ['id' => 'cld-sample', 'title' => 'City Lights', 'category' => 'Urban'],
];

// Use Cloudinary demo account that allows CORS
$cloudinaryCloud = 'demo';

?>

<!-- Portfolio Hero -->
<section class="portfolio-hero">
    <div class="portfolio-hero__bg">
        <picture class="portfolio-hero__picture">
            <source media="(min-width: 769px)" srcset="https://res.cloudinary.com/<?php echo $cloudinaryCloud; ?>/image/upload/f_auto,q_auto:best,w_1280/v1658528027/cld-sample-5.jpg">
            <img src="https://res.cloudinary.com/<?php echo $cloudinaryCloud; ?>/image/upload/f_auto,q_auto:best,w_768/v1658528027/cld-sample-5.jpg" alt="Portfolio" class="portfolio-hero__image" loading="eager" fetchpriority="high" width="1280" height="720" crossorigin="anonymous">
        </picture>
        <div class="portfolio-hero__overlay"></div>
    </div>
    <div class="portfolio-hero__content">
        <h1 class="portfolio-hero__title">Our Portfolio</h1>
        <p class="portfolio-hero__subtitle">Explore our latest creative work</p>
    </div>
</section>

<!-- Portfolio Filters -->
<section class="portfolio-filters section">
    <div class="container">
        <div class="portfolio-filters__list">
            <button class="portfolio-filters__item active" data-filter="all">All</button>
            <button class="portfolio-filters__item" data-filter="photography">Photography</button>
            <button class="portfolio-filters__item" data-filter="architecture">Architecture</button>
            <button class="portfolio-filters__item" data-filter="nature">Nature</button>
            <button class="portfolio-filters__item" data-filter="travel">Travel</button>
        </div>
    </div>
</section>

<!-- Portfolio Grid -->
<section class="portfolio-grid section">
    <div class="container">
        <div class="portfolio-grid__wrapper">
            <?php foreach ($portfolioImages as $index => $image) { ?>
            <article class="portfolio-card" data-category="<?php echo strtolower($image['category']); ?>" data-index="<?php echo $index; ?>">
                <div class="portfolio-card__image-wrapper">
                    <img
                        src="https://res.cloudinary.com/<?php echo $cloudinaryCloud; ?>/image/upload/f_auto,q_auto:best,w_600,c_fill/v1658528027/<?php echo $image['id']; ?>.jpg"
                        alt="<?php echo htmlspecialchars($image['title']); ?>"
                        class="portfolio-card__image"
                        loading="lazy"
                        decoding="async"
                        width="600"
                        height="400"
                        crossorigin="anonymous"
                    >
                    <div class="portfolio-card__overlay">
                        <button class="portfolio-card__zoom" aria-label="View full size" data-index="<?php echo $index; ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="portfolio-card__content">
                    <h3 class="portfolio-card__title"><?php echo htmlspecialchars($image['title']); ?></h3>
                    <p class="portfolio-card__category"><?php echo htmlspecialchars($image['category']); ?></p>
                </div>
            </article>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Fullscreen Gallery Modal -->
<div class="portfolio-gallery" id="portfolio-gallery" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="portfolio-gallery__overlay"></div>
    <div class="portfolio-gallery__container">
        <button class="portfolio-gallery__close" aria-label="Close gallery">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"></path>
            </svg>
        </button>
        <button class="portfolio-gallery__nav portfolio-gallery__nav--prev" aria-label="Previous image">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </button>
        <button class="portfolio-gallery__nav portfolio-gallery__nav--next" aria-label="Next image">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </button>
        <div class="portfolio-gallery__content">
            <img src="" alt="" class="portfolio-gallery__image" loading="eager" crossorigin="anonymous">
            <div class="portfolio-gallery__caption">
                <h3 class="portfolio-gallery__title"></h3>
                <p class="portfolio-gallery__category"></p>
            </div>
        </div>
    </div>
</div>
