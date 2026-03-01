<?php declare(strict_types=1);

use App\Domain\Article\Article;
use App\Shared\Render\ContentBlockRenderer;

/**
 * @var Article $article
 * @var list<Article> $relatedArticles
 */

$contentBlockRenderer = new ContentBlockRenderer();
$contentBlocks = $article->getContentBlocks();

$content = '';
if ($contentBlocks !== []) {
    foreach ($contentBlocks as $block) {
        $content .= $contentBlockRenderer->render($block);
    }
} else {
    // Article body contains markdown, convert it to HTML
    // Use league/commonmark directly since we don't have ContentBlock objects
    $environment = new \League\CommonMark\Environment\Environment([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);
    $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
    $environment->addExtension(new \League\CommonMark\Extension\GithubFlavoredMarkdownExtension());
    $converter = new \League\CommonMark\MarkdownConverter($environment);
    $content = $converter->convert($article->body)->getContent();
}

$author = $article->getCreatedByUser();
$categories = $article->getCategories();
$publicationDate = $article->publication_date ?? $article->created_at;
$relatedList = $relatedArticles ?? [];
$authorInitials = strtoupper(substr($author->name ?? 'A', 0, 2));
$articleUrl = 'https://example.com/blog/article/' . $article->slug;
?>

<section class="article-page">
    <div class="article-page__hero">
        <a href="/blog" class="btn btn--outline article-page__back-link">
            Back to Blog
        </a>
    </div>

    <article class="article-page__content">
        <header class="article-page__header">
            <?php if ($categories !== []): ?>
            <div class="article-page__categories">
                <?php foreach ($categories as $category): ?>
                <a href="/blog/category/<?= htmlspecialchars($category->slug) ?>" class="article-page__category">
                    <?= htmlspecialchars($category->name) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h1 class="article-page__title"><?= htmlspecialchars($article->title) ?></h1>

            <div class="article-page__meta">
                <div class="article-page__author-mini">
                    <span class="article-page__author-avatar"><?= $authorInitials ?></span>
                    <span class="article-page__author-name">By <?= htmlspecialchars($author->name ?? 'Admin') ?></span>
                </div>
                <span class="article-page__meta-divider">-</span>
                <time class="article-page__date" datetime="<?= $publicationDate->format('c') ?>">
                    <?= $publicationDate->format('F d, Y') ?>
                </time>
            </div>
        </header>

        <div class="article-page__body">
            <?= $content ?>
        </div>

        <footer class="article-page__footer">
            <div class="article-page__actions">
                <div class="article-page__likes">
                    <button class="article-page__action-btn" type="button" aria-label="Like article">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span class="article-page__action-count">0</span>
                    </button>
                </div>
                <div class="article-page__share">
                    <span class="article-page__share-label">Share:</span>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($articleUrl) ?>&text=<?= urlencode($article->title) ?>" class="article-page__share-btn" target="_blank" rel="noopener">Twitter</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($articleUrl) ?>" class="article-page__share-btn" target="_blank" rel="noopener">LinkedIn</a>
                    <a href="mailto:?subject=<?= urlencode($article->title) ?>&body=<?= urlencode($articleUrl) ?>" class="article-page__share-btn">Email</a>
                </div>
            </div>
        </footer>

        <div class="article-page__author-section">
            <div class="article-page__author-bio">
                <div class="article-page__author-avatar-lg"><?= $authorInitials ?></div>
                <div class="article-page__author-info">
                    <h3 class="article-page__author-title"><?= htmlspecialchars($author->name ?? 'Admin') ?></h3>
                    <p class="article-page__author-desc">Author and contributor to our blog. Sharing insights on web development and technology.</p>
                </div>
            </div>
        </div>

        <?php if (!empty($relatedList)): ?>
        <section class="article-page__related">
            <h2 class="article-page__related-title">Related Articles</h2>
            <div class="article-page__related-grid">
                <?php foreach ($relatedList as $related): ?>
                <article class="article-page__related-card">
                    <a href="/blog/article/<?= htmlspecialchars($related->slug) ?>" class="article-page__related-link">
                        <h3 class="article-page__related-card-title"><?= htmlspecialchars($related->title) ?></h3>
                        <time class="article-page__related-card-date">
                            <?= ($related->publication_date ?? $related->created_at)->format('F d, Y') ?>
                        </time>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="article-page__comments">
            <h2 class="article-page__comments-title">Comments</h2>
            <div class="article-page__comments-placeholder">
                <p>Comments are closed for this article.</p>
            </div>
        </section>
    </article>
</section>
