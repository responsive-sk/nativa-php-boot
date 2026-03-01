<?php declare(strict_types=1);

/**
 * Blog Article Detail Template - CMS Integration
 *
 * @var \Domain\Model\Article $article
 * @var array $relatedArticles Array of related articles
 * @var string $pageTitle Page title
 * @var string $page Page identifier
 */

$articleList = $relatedArticles ?? [];

error_log("DEBUG: blog/show.php template rendering article: " . $article->title());
?>

<!-- Article Header -->
<article class="blog-article">
    <header class="blog-article__header">
        <div class="container">
            <div class="blog-article__meta">
                <a href="/blog" class="blog-article__back">← Back to Blog</a>
                <?php if ($article->categoryId()): ?>
                <span class="blog-article__category">
                    <span>Category ID: <?= $this->e($article->categoryId()) ?></span>
                </span>
                <?php endif; ?>
            </div>
            
            <h1 class="blog-article__title"><?= $this->e($article->title()) ?></h1>
            
            <div class="blog-article__meta-info">
                <span class="blog-article__author">
                    By <?= $this->e($article->authorId()) ?>
                </span>
                <span class="blog-article__separator">•</span>
                <span class="blog-article__date">
                    <?= $this->date($article->publishedAt(), 'F j, Y') ?>
                </span>
                <span class="blog-article__separator">•</span>
                <span class="blog-article__views">
                    <?= $article->views() ?> <?= $article->views() === 1 ? 'view' : 'views' ?>
                </span>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <div class="blog-article__content">
        <div class="container">
            <div class="blog-article__body">
                <?= $this->nl2br($this->e($article->content())) ?>
            </div>
        </div>
    </div>

    <!-- Tags -->
    <?php if (!$this->isEmpty($article->tags())): ?>
    <div class="blog-article__tags">
        <div class="container">
            <span class="blog-article__tags-label">Tags:</span>
            <div class="blog-article__tags-list">
                <?php foreach ($article->tags() as $tag): ?>
                <a href="/tag/<?= $this->e(is_string($tag) ? $tag : $tag->slug()) ?>" class="blog-article__tag">
                    #<?= $this->e(is_string($tag) ? $tag : $tag->name()) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Share Section -->
    <div class="blog-article__share">
        <div class="container">
            <span class="blog-article__share-label">Share this article:</span>
            <div class="blog-article__share-links">
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article->title()) ?>&url=<?= urlencode($this->url('/blog/' . $article->slug())) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="blog-article__share-link">
                    Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($this->url('/blog/' . $article->slug())) ?>&title=<?= urlencode($article->title()) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="blog-article__share-link">
                    LinkedIn
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($this->url('/blog/' . $article->slug())) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="blog-article__share-link">
                    Facebook
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Related Articles -->
<?php if (!empty($articleList)): ?>
<section class="related-articles">
    <div class="container">
        <h2 class="related-articles__title">Related Articles</h2>
        <div class="related-articles__grid">
            <?php foreach ($articleList as $relatedArticle): ?>
            <article class="related-article-card">
                <h3 class="related-article-card__title">
                    <a href="/blog/<?= $this->e($relatedArticle->slug()) ?>">
                        <?= $this->e($relatedArticle->title()) ?>
                    </a>
                </h3>
                <p class="related-article-card__excerpt">
                    <?= $this->e($relatedArticle->excerpt() ?: substr(strip_tags($relatedArticle->content()), 0, 120) . '...') ?>
                </p>
                <a href="/blog/<?= $this->e($relatedArticle->slug()) ?>" class="related-article-card__link">
                    Read more →
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Author Bio Section (optional - can be enhanced later) -->
<section class="author-bio">
    <div class="container">
        <div class="author-bio__content">
            <div class="author-bio__avatar">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M20 21a8 8 0 1 0-16 0"/>
                </svg>
            </div>
            <div class="author-bio__info">
                <h3 class="author-bio__name">About the Author</h3>
                <p class="author-bio__text">
                    This article was written by a contributor to Nativa CMS. 
                    Want to share your knowledge? Contact us to become an author.
                </p>
            </div>
        </div>
    </div>
</section>
