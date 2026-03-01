<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\Domain\Search\SearchResult $result
 * @var string $query
 * @var string $mode
 * @var array $queryParams
 * @var int $totalCount
 * @var float $executionTime
 * @var bool $hasResults
 * @var array $articles
 * @var string $page
 */

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Aliases\Aliases;

$urlGenerator = $this->container->get(UrlGeneratorInterface::class);
$aliases = $this->container->get(Aliases::class);
?>

<div class="search-results">
    <div class="search-results__header">
        <h1 class="search-results__title">Search Results</h1>
        <?php if ($query !== ''): ?>
            <p class="search-results__query">
                Results for: <strong><?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        <?php endif; ?>
    </div>

    <div class="search-results__filters">
        <form class="search-form" method="get" action="<?= $urlGenerator->generate('blog') ?>">
            <div class="search-form__group">
                <input
                    type="text"
                    name="q"
                    class="search-form__input"
                    value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Search articles..."
                    autofocus
                >
                <select name="mode" class="search-form__select">
                    <option value="mixed" <?= $mode === 'mixed' ? 'selected' : '' ?>>Mixed</option>
                    <option value="exact" <?= $mode === 'exact' ? 'selected' : '' ?>>Exact</option>
                    <option value="fuzzy" <?= $mode === 'fuzzy' ? 'selected' : '' ?>>Fuzzy</option>
                    <option value="boolean" <?= $mode === 'boolean' ? 'selected' : '' ?>>Boolean</option>
                </select>
                <button type="submit" class="search-form__button">Search</button>
            </div>
        </form>
    </div>

    <div class="search-results__info">
        <?php if ($hasResults): ?>
            <p class="search-results__count">
                Found <strong><?= $totalCount ?></strong> result<?= $totalCount !== 1 ? 's' : '' ?>
                in <?= number_format($executionTime, 2) ?>ms
            </p>
        <?php elseif ($query !== ''): ?>
            <p class="search-results__empty">
                No results found for <strong>"<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>"</strong>
            </p>
            <p class="search-results__suggestions">
                Suggestions:
            </p>
            <ul class="search-results__suggestions-list">
                <li>Try different keywords</li>
                <li>Use fewer or different keywords</li>
                <li>Try "fuzzy" mode for typo tolerance</li>
            </ul>
        <?php else: ?>
            <p class="search-results__prompt">
                Enter a search term to find articles
            </p>
        <?php endif; ?>
    </div>

    <?php if ($hasResults): ?>
        <div class="search-results__list">
            <?php foreach ($articles as $article): ?>
                <article class="search-result-item">
                    <div class="search-result-item__header">
                        <h2 class="search-result-item__title">
                            <a href="<?= $urlGenerator->generate('blog/article/view', ['slug' => $article->getSlug()]) ?>">
                                <?= htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <span class="search-result-item__date">
                            <?= $article->getCreatedAt()->format('Y-m-d') ?>
                        </span>
                    </div>
                    
                    <?php if ($article->getExcerpt()): ?>
                        <p class="search-result-item__excerpt">
                            <?= htmlspecialchars($article->getExcerpt(), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="search-result-item__meta">
                        <?php if ($article->getCategories()): ?>
                            <div class="search-result-item__categories">
                                <?php foreach ($article->getCategories() as $category): ?>
                                    <span class="search-result-item__category">
                                        <?= htmlspecialchars($category->getName(), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php 
                        $relevanceScore = $article->getMeta()['relevance_score'] ?? null;
                        if ($relevanceScore !== null): ?>
                            <span class="search-result-item__relevance">
                                Relevance: <?= number_format($relevanceScore * 100, 1) ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalCount > 20): ?>
            <div class="search-results__pagination">
                <button class="search-results__load-more" onclick="loadMoreResults()">
                    Load more results
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function loadMoreResults() {
    const currentOffset = <?= $result->getQuery()->getOffset() + $result->getQuery()->getLimit() ?>;
    const url = new URL(window.location.href);
    url.searchParams.set('offset', currentOffset.toString());
    window.location.href = url.toString();
}
</script>
