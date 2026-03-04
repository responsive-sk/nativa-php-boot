<?php

declare(strict_types = 1);

/**
 * @var WebView      $this
 * @var SearchResult $result
 * @var string       $query
 * @var string       $mode
 * @var array        $queryParams
 * @var int          $totalCount
 * @var float        $executionTime
 * @var bool         $hasResults
 * @var array        $articles
 * @var string       $page
 */

use App\Domain\Search\SearchResult;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;

$urlGenerator = $this->container->get(UrlGeneratorInterface::class);
$aliases = $this->container->get(Aliases::class);
?>

<div class="search-results">
    <div class="search-results__header">
        <h1 class="search-results__title">Search Results</h1>
        <?php if ('' !== $query) { ?>
            <p class="search-results__query">
                Results for: <strong><?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>
        <?php } ?>
    </div>

    <div class="search-results__filters">
        <form class="search-form" method="get" action="<?php echo $urlGenerator->generate('blog'); ?>">
            <div class="search-form__group">
                <input
                    type="text"
                    name="q"
                    class="search-form__input"
                    value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Search articles..."
                    autofocus
                >
                <select name="mode" class="search-form__select">
                    <option value="mixed" <?php echo 'mixed' === $mode ? 'selected' : ''; ?>>Mixed</option>
                    <option value="exact" <?php echo 'exact' === $mode ? 'selected' : ''; ?>>Exact</option>
                    <option value="fuzzy" <?php echo 'fuzzy' === $mode ? 'selected' : ''; ?>>Fuzzy</option>
                    <option value="boolean" <?php echo 'boolean' === $mode ? 'selected' : ''; ?>>Boolean</option>
                </select>
                <button type="submit" class="search-form__button">Search</button>
            </div>
        </form>
    </div>

    <div class="search-results__info">
        <?php if ($hasResults) { ?>
            <p class="search-results__count">
                Found <strong><?php echo $totalCount; ?></strong> result<?php echo 1 !== $totalCount ? 's' : ''; ?>
                in <?php echo number_format($executionTime, 2); ?>ms
            </p>
        <?php } elseif ('' !== $query) { ?>
            <p class="search-results__empty">
                No results found for <strong>"<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"</strong>
            </p>
            <p class="search-results__suggestions">
                Suggestions:
            </p>
            <ul class="search-results__suggestions-list">
                <li>Try different keywords</li>
                <li>Use fewer or different keywords</li>
                <li>Try "fuzzy" mode for typo tolerance</li>
            </ul>
        <?php } else { ?>
            <p class="search-results__prompt">
                Enter a search term to find articles
            </p>
        <?php } ?>
    </div>

    <?php if ($hasResults) { ?>
        <div class="search-results__list">
            <?php foreach ($articles as $article) { ?>
                <article class="search-result-item">
                    <div class="search-result-item__header">
                        <h2 class="search-result-item__title">
                            <a href="<?php echo $urlGenerator->generate('blog/article/view', ['slug' => $article->getSlug()]); ?>">
                                <?php echo htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h2>
                        <span class="search-result-item__date">
                            <?php echo $article->getCreatedAt()->format('Y-m-d'); ?>
                        </span>
                    </div>

                    <?php if ($article->getExcerpt()) { ?>
                        <p class="search-result-item__excerpt">
                            <?php echo htmlspecialchars($article->getExcerpt(), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    <?php } ?>

                    <div class="search-result-item__meta">
                        <?php if ($article->getCategories()) { ?>
                            <div class="search-result-item__categories">
                                <?php foreach ($article->getCategories() as $category) { ?>
                                    <span class="search-result-item__category">
                                        <?php echo htmlspecialchars($category->getName(), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php
                        $relevanceScore = $article->getMeta()['relevance_score'] ?? null;
                if (null !== $relevanceScore) { ?>
                            <span class="search-result-item__relevance">
                                Relevance: <?php echo number_format($relevanceScore * 100, 1); ?>%
                            </span>
                        <?php } ?>
                    </div>
                </article>
            <?php } ?>
        </div>

        <?php if ($totalCount > 20) { ?>
            <div class="search-results__pagination">
                <button class="search-results__load-more" onclick="loadMoreResults()">
                    Load more results
                </button>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script>
function loadMoreResults() {
    const currentOffset = <?php echo $result->getQuery()->getOffset() + $result->getQuery()->getLimit(); ?>;
    const url = new URL(window.location.href);
    url.searchParams.set('offset', currentOffset.toString());
    window.location.href = url.toString();
}
</script>
