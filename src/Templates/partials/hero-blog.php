<?php
/**
 * Blog Page Hero Section
 * Displayed at top of blog listing page.
 *
 * @var array  $categories Optional array of categories
 * @var array  $tags Optional array of popular tags
 */

$categories = $categories ?? [];
$tags = $tags ?? [];

// Sample categories if none provided (from CMS)
if (empty($categories)) {
    $categories = [
        ['name' => 'PHP', 'slug' => 'php', 'count' => 12],
        ['name' => 'JavaScript', 'slug' => 'javascript', 'count' => 8],
        ['name' => 'Architecture', 'slug' => 'architecture', 'count' => 6],
        ['name' => 'Tutorials', 'slug' => 'tutorials', 'count' => 15],
    ];
}

// Sample tags if none provided
if (empty($tags)) {
    $tags = [
        ['name' => 'Laravel', 'slug' => 'laravel'],
        ['name' => 'Symfony', 'slug' => 'symfony'],
        ['name' => 'DDD', 'slug' => 'ddd'],
        ['name' => 'Testing', 'slug' => 'testing'],
        ['name' => 'Performance', 'slug' => 'performance'],
        ['name' => 'Security', 'slug' => 'security'],
    ];
}
?>

<!-- Blog Hero -->
<section class="blog-hero">
    <div class="blog-hero__bg">
        <picture class="blog-hero__picture">
            <!-- Desktop Large: 1366w -->
            <source
                media="(min-width: 1440px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1366,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
            >
            <!-- Desktop: 1024w -->
            <source
                media="(min-width: 1024px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_1024,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
            >
            <!-- Tablet: 768w -->
            <source
                media="(min-width: 769px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
            >
            <!-- Mobile: 480w -->
            <source
                media="(min-width: 480px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_480,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
            >
            <!-- Mobile Small: 320w -->
            <source
                media="(max-width: 479px)"
                srcset="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_320,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
            >
            <!-- Fallback -->
            <img
                src="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_768,c_fill,g_auto/v1699999999/cld-sample-3.jpg"
                alt="Blog background"
                class="blog-hero__bg-image"
                fetchpriority="high"
                loading="eager"
                decoding="async"
                width="1366"
                height="768"
                crossorigin="anonymous"
            >
        </picture>
        <div class="blog-hero__bg-overlay"></div>
    </div>

    <div class="blog-hero__content container">
        <h1 class="blog-hero__title anim-block anim-block--heading">
            <span class="anim-block__line">
                <span class="anim-block__inner">
                    <span class="anim-block__text">Our</span>
                </span>
            </span>
            <span class="anim-block__line">
                <span class="anim-block__inner">
                    <span class="anim-block__text">Blog</span>
                </span>
            </span>
        </h1>
        <p class="blog-hero__subtitle" style="margin-top: 1.5rem; opacity: 0.8;">
            Latest insights and tutorials from Nativa CMS
        </p>

        <!-- Categories -->
        <?php if (!empty($categories)) { ?>
        <div class="blog-hero__categories" data-animate="fadeInUp" data-delay="200">
            <span class="blog-hero__label">Categories:</span>
            <div class="blog-hero__category-list">
                <?php foreach ($categories as $category) { ?>
                <a href="/blog/category/<?php echo $this->e($category['slug']); ?>" class="blog-hero__category">
                    <?php echo $this->e($category['name']); ?>
                    <span class="blog-hero__category-count"><?php echo $category['count']; ?></span>
                </a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <!-- Tags -->
        <?php if (!empty($tags)) { ?>
        <div class="blog-hero__tags" data-animate="fadeInUp" data-delay="400">
            <span class="blog-hero__label">Popular Tags:</span>
            <div class="blog-hero__tag-cloud">
                <?php foreach ($tags as $tag) { ?>
                <a href="/blog/tag/<?php echo $this->e($tag['slug']); ?>" class="blog-hero__tag">
                    #<?php echo $this->e($tag['name']); ?>
                </a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <!-- Quick Actions -->
        <div class="blog-hero__actions" data-animate="fadeInUp" data-delay="600">
            <a href="#blog-search" class="btn btn--primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                Search Articles
            </a>
            <a href="/blog/subscribe" class="btn btn--outline">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                Subscribe
            </a>
        </div>
    </div>
</section>
