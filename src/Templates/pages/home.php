<?php

use Domain\Model\Article;
use Interfaces\HTTP\View\TemplateRenderer;

/*
 * Homepage Template - Main Content (without hero)
 * Inspired by BRAND Napa Valley design.
 *
 * @var TemplateRenderer    $this
 * @var array<int, Article> $articles
 * @var string              $pageTitle
 */
?>

<!-- Main Content -->
<main class="site-body">
    <!-- Featured Section -->
    <section class="section section--featured" id="featured">
        <div class="container">
            <header class="section__header">
                <span class="anim-block__label">01 / 03</span>
                <h2 class="anim-block anim-block--heading">
                    <span class="anim-block__line">
                        <span class="anim-block__inner">
                            <span class="anim-block__text">Built on</span>
                        </span>
                    </span>
                    <span class="anim-block__line">
                        <span class="anim-block__inner">
                            <span class="anim-block__text"><em>Modern PHP</em></span>
                        </span>
                    </span>
                </h2>
                <p class="section__subtitle" style="margin-top: 1.5rem; opacity: 0.8;">Rooted in PHP 8.4+, reaching for excellence</p>
            </header>

            <div class="featured-grid">
                <article class="featured-item" data-animate="scaleIn">
                    <div class="featured-item__content">
                        <h3 class="featured-item__title">Domain-Driven Design</h3>
                        <p class="featured-item__desc">Clean separation of concerns with DDD architecture</p>
                    </div>
                    <span class="featured-item__number">01</span>
                </article>

                <article class="featured-item" data-animate="scaleIn">
                    <div class="featured-item__content">
                        <h3 class="featured-item__title">Full-Text Search</h3>
                        <p class="featured-item__desc">Lightning-fast SQLite FTS5 search engine</p>
                    </div>
                    <span class="featured-item__number">02</span>
                </article>

                <article class="featured-item" data-animate="scaleIn">
                    <div class="featured-item__content">
                        <h3 class="featured-item__title">Native PHP Templates</h3>
                        <p class="featured-item__desc">Lightweight templating without Twig</p>
                    </div>
                    <span class="featured-item__number">03</span>
                </article>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <?php if (!empty($articles)) { ?>
    <section class="section section--articles">
        <div class="container">
            <header class="section__header" data-animate="scaleIn">
                <span class="section__number">02</span>
                <h2 class="section__title">Latest Articles</h2>
                <p class="section__subtitle">Fresh insights from our platform</p>
            </header>

            <div class="articles-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--space-8);">
                <?php foreach ($articles as $article) { ?>
                <article class="article-card" data-animate="scaleIn">
                    <div class="article-card__image-wrapper">
                        <img
                            src="https://res.cloudinary.com/demo/image/upload/f_auto,q_auto:best,w_800/v1699999999/cld-sample-4.jpg"
                            alt="<?php echo $this->e($article->title()); ?>"
                            class="article-card__image"
                            loading="lazy"
                            decoding="async"
                            width="800"
                            height="450"
                            crossorigin="anonymous"
                        >
                    </div>
                    <div class="article-card__content">
                        <h3 class="article-card__title">
                            <a href="/blog/<?php echo $this->e($article->slug()); ?>"><?php echo $this->e($article->title()); ?></a>
                        </h3>
                        <p class="article-card__excerpt"><?php echo $this->e($article->excerpt() ?: substr($article->content(), 0, 150) . '...'); ?></p>
                        <div class="article-card__meta">
                            <div class="article-card__author">
                                <div class="article-card__author-avatar">
                                    <?php echo strtoupper(substr($article->authorId(), 0, 2)); ?>
                                </div>
                                <span>Author</span>
                            </div>
                            <?php if ($article->publishedAt()) { ?>
                            <div class="article-card__date">
                                <svg class="article-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <?php echo date('M d, Y', strtotime($article->publishedAt())); ?>
                            </div>
                            <?php } ?>
                        </div>
                        <a href="/blog/<?php echo $this->e($article->slug()); ?>" class="article-card__link">
                            Read more
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                </article>
                <?php } ?>
            </div>

            <div style="text-align: center; margin-top: var(--space-12);">
                <a href="/blog" class="btn btn--primary btn--lg">
                    View All Articles
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php } ?>
</main>
