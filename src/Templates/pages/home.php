<?php
/**
 * Homepage Template - Main Content (without hero)
 * Inspired by BRAND Napa Valley design
 *
 * @var \Interfaces\HTTP\View\TemplateRenderer $this
 * @var array<int, \Domain\Model\Article> $articles
 * @var string $pageTitle
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
    <?php if (!empty($articles)): ?>
    <section class="section section--articles">
        <div class="container">
            <header class="section__header" data-animate="scaleIn">
                <span class="section__number">02</span>
                <h2 class="section__title">Latest Articles</h2>
                <p class="section__subtitle">Fresh insights from our platform</p>
            </header>

            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <div class="article-card__content">
                        <h3 class="article-card__title"><?= $this->e($article->title()) ?></h3>
                        <p class="article-card__excerpt"><?= $this->e($article->excerpt() ?: substr($article->content(), 0, 150) . '...') ?></p>
                        <a href="/blog/<?= $this->e($article->slug()) ?>" class="article-card__link">Read more →</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
