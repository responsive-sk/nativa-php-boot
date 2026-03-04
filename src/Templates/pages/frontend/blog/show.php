<?php declare(strict_types = 1);
use Domain\Model\Article;

/**
 * Blog Article Detail Template - CMS Integration.
 *
 * @var Article $article
 * @var array   $relatedArticles Array of related articles
 * @var string  $pageTitle Page title
 * @var string  $page Page identifier
 */
$articleList = $relatedArticles ?? [];

?>

<!-- Article Header -->
<article class="blog-article">
    <header class="blog-article__header">
        <div class="container">
            <div class="blog-article__meta">
                <a href="/blog" class="blog-article__back">← Back to Blog</a>
                <?php if ($article->categoryId()) { ?>
                <span class="blog-article__category">
                    <span>Category ID: <?php echo $this->e($article->categoryId()); ?></span>
                </span>
                <?php } ?>
            </div>

            <h1 class="blog-article__title"><?php echo $this->e($article->title()); ?></h1>

            <div class="blog-article__meta-info">
                <span class="blog-article__author">
                    By <?php echo $this->e($article->authorId()); ?>
                </span>
                <span class="blog-article__separator">•</span>
                <span class="blog-article__date">
                    <?php echo $this->date($article->publishedAt(), 'F j, Y'); ?>
                </span>
                <span class="blog-article__separator">•</span>
                <span class="blog-article__views">
                    <?php echo $article->views(); ?> <?php echo 1 === $article->views() ? 'view' : 'views'; ?>
                </span>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <div class="blog-article__content">
        <div class="container">
            <div class="blog-article__body">
                <?php echo $this->nl2br($this->e($article->content())); ?>
            </div>
        </div>
    </div>

    <!-- Tags -->
    <?php if (!$this->isEmpty($article->tags())) { ?>
    <div class="blog-article__tags">
        <div class="container">
            <span class="blog-article__tags-label">Tags:</span>
            <div class="blog-article__tags-list">
                <?php foreach ($article->tags() as $tag) { ?>
                <a href="/tag/<?php echo $this->e(is_string($tag) ? $tag : $tag->slug()); ?>" class="blog-article__tag">
                    #<?php echo $this->e(is_string($tag) ? $tag : $tag->name()); ?>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Share Section -->
    <div class="blog-article__share">
        <div class="container">
            <span class="blog-article__share-label">Share this article:</span>
            <div class="blog-article__share-links">
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($article->title()); ?>&url=<?php echo urlencode($this->url('/blog/' . $article->slug())); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="blog-article__share-link">
                    Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($this->url('/blog/' . $article->slug())); ?>&title=<?php echo urlencode($article->title()); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="blog-article__share-link">
                    LinkedIn
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($this->url('/blog/' . $article->slug())); ?>"
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
<?php if (!empty($articleList)) { ?>
<section class="services">
    <div class="container">
        <div class="services__hero">
            <h2>Related Articles</h2>
            <p>Continue reading</p>
        </div>
        <div class="services__grid">
            <?php foreach ($articleList as $relatedArticle) { ?>
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
                <h3 class="service-card__title">
                    <a href="/blog/<?php echo $this->e($relatedArticle->slug()); ?>">
                        <?php echo $this->e($relatedArticle->title()); ?>
                    </a>
                </h3>
                <p class="service-card__description">
                    <?php echo $this->e($relatedArticle->excerpt() ?: substr(strip_tags($relatedArticle->content()), 0, 120) . '...'); ?>
                </p>
                <a href="/blog/<?php echo $this->e($relatedArticle->slug()); ?>" class="service-card__link">
                    Read more →
                </a>
            </article>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>

<!-- Author Bio Section -->
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
