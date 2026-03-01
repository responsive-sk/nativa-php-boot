<?php

declare(strict_types=1);

/**
 * Homepage Template - CMS Integration
 * 
 * @var array $articles Array of Article entities
 * @var string $pageTitle Page title
 */

// Cloudinary hero image optimized for speed with responsive sizes
$heroImageMobile = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto,w_768/v1658528001/samples/ecommerce/analog-classic.jpg';
$heroImageDesktop = 'https://res.cloudinary.com/epithemic/image/upload/f_auto,q_auto,w_1920/v1658528001/samples/ecommerce/analog-classic.jpg';
$extraHeadLinks = '<link rel="preload" as="image" href="' . $heroImageDesktop . '" fetchpriority="high" media="(min-width: 769px)">' . "\n" .
                  '  <link rel="preload" as="image" href="' . $heroImageMobile . '" fetchpriority="high" media="(max-width: 768px)">';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero__overlay"></div>
    <picture class="hero__picture">
        <source media="(min-width: 769px)" srcset="<?= $heroImageDesktop ?>">
        <img src="<?= $heroImageMobile ?>" alt="Hero background" fetchpriority="high" loading="eager" decoding="async" class="hero__image" width="1280" height="720">
    </picture>
    <div class="hero__content">
        <h1 class="hero__title" data-greeting>Welcome to Nativa CMS</h1>
        <p class="hero__subtitle">Modern PHP 8.4+ Blog Platform with DDD Architecture</p>
        <div class="hero__actions">
            <a href="/blog" class="btn btn--primary">Read Blog</a>
            <a href="/contact" class="btn btn--outline">Contact Us</a>
        </div>
    </div>
</section>

<!-- Latest Articles Section -->
<?php if (!empty($articles)): ?>
<section class="services">
  <div class="services__hero" data-animate="scaleIn" data-duration="1500">
    <h2>Latest Articles</h2>
    <p>Fresh insights and tutorials from our blog</p>
  </div>
  <div class="services__grid">
    <?php foreach ($articles as $article): ?>
    <article class="service-card">
      <div class="service-card__icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
      </div>
      <h3 class="service-card__title"><?= $this->e($article->title()) ?></h3>
      <p class="service-card__description"><?= $this->e($article->excerpt() ?: substr($article->content(), 0, 150) . '...') ?></p>
      <a href="/blog/<?= $this->e($article->slug()) ?>" class="service-card__link">Read more →</a>
    </article>
    <?php endforeach; ?>
  </div>
  <div style="text-align: center; margin-top: 2rem;">
    <a href="/blog" class="btn btn--primary">View All Articles</a>
  </div>
</section>
<?php endif; ?>

<!-- Services Preview Section -->
<section class="services">
  <div class="services__hero" data-animate="scaleIn" data-duration="1500">
    <h2>Features</h2>
    <p>Everything you need for a modern blog</p>
  </div>
  <div class="services__grid">
    <article class="service-card">
      <div class="service-card__icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
      </div>
      <h3 class="service-card__title">Article Management</h3>
      <p class="service-card__description">Full CRUD operations with categories, tags, and scheduling.</p>
      <ul class="service-card__features">
        <li>Rich Content Editor</li>
        <li>Categories & Tags</li>
        <li>Draft & Publish</li>
      </ul>
      <a href="/blog" class="service-card__link">View Blog →</a>
    </article>
    <article class="service-card">
      <div class="service-card__icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 19l7-7 3 3-7 7-3-3z"/>
          <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
          <path d="M2 2l7.586 7.586"/>
          <circle cx="11" cy="11" r="2"/>
        </svg>
      </div>
      <h3 class="service-card__title">Admin Dashboard</h3>
      <p class="service-card__description">Intuitive admin panel for managing your content.</p>
      <ul class="service-card__features">
        <li>Statistics</li>
        <li>Media Library</li>
        <li>Form Builder</li>
      </ul>
      <a href="/admin" class="service-card__link">Go to Admin →</a>
    </article>
    <article class="service-card">
      <div class="service-card__icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <rect x="5" y="2" width="14" height="20" rx="2"/>
          <line x1="12" y1="18" x2="12.01" y2="18"/>
        </svg>
      </div>
      <h3 class="service-card__title">Custom Forms</h3>
      <p class="service-card__description">Build custom forms with our drag-and-drop builder.</p>
      <ul class="service-card__features">
        <li>Form Builder</li>
        <li>Submissions</li>
        <li>Email Notifications</li>
      </ul>
      <a href="/contact" class="service-card__link">Try Contact Form →</a>
    </article>
  </div>
</section>
