<?php

declare(strict_types=1);

use App\Domain\Search\SearchResult;

/**
 * @var SearchResult $searchResult
 * @var string $query
 * @var string $mode
 */

$result = $searchResult;
$articles = $result->getArticles();
$totalCount = $result->getTotalCount();
$executionTime = $result->getExecutionTimeMs();
$hasResults = $result->hasResults();
?>

<section class="docs-search-page">
    <div class="docs-search-hero">
        <div class="container">
            <div class="breadcrumbs">
                <a href="/" class="breadcrumb-link">Home</a>
                <span class="breadcrumb-separator">/</span>
                <a href="/docs" class="breadcrumb-link">Docs</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Search</span>
            </div>

            <h1 class="docs-search-hero__title">Documentation Search</h1>
            <p class="docs-search-hero__subtitle">Find components, patterns, and guides</p>

            <form class="docs-search-form" method="GET" action="/docs/search">
                <div class="search-input-group">
                    <div class="search-input-container">
                        <svg class="search-input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input
                            type="search"
                            name="q"
                            value="<?= htmlspecialchars($query) ?>"
                            placeholder="Search documentation..."
                            class="search-input"
                            required
                            autofocus
                        >
                    </div>

                    <div class="search-options">
                        <select name="mode" class="search-mode-select">
                            <option value="mixed" <?= $mode === 'mixed' ? 'selected' : '' ?>>Smart Search</option>
                            <option value="exact" <?= $mode === 'exact' ? 'selected' : '' ?>>Exact Phrase</option>
                            <option value="fuzzy" <?= $mode === 'fuzzy' ? 'selected' : '' ?>>Fuzzy Match</option>
                            <option value="boolean" <?= $mode === 'boolean' ? 'selected' : '' ?>>Boolean</option>
                        </select>

                        <button type="submit" class="search-submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="docs-search-content">
        <div class="container">
            <?php if (!empty($query)): ?>
                <div class="search-results-section">
                    <div class="search-results-header">
                        <h2 class="search-results-title">
                            Search Results for "<span class="search-query-highlight"><?= htmlspecialchars($query) ?></span>"
                        </h2>

                        <div class="search-results-meta">
                            <div class="search-stats">
                                <span class="result-count"><?= $totalCount ?> result<?= $totalCount !== 1 ? 's' : '' ?></span>
                                <span class="search-time">found in <?= number_format($executionTime, 2) ?>ms</span>
                            </div>
                            <div class="search-mode-indicator">
                                <span class="mode-label">Mode:</span>
                                <span class="mode-value mode-<?= $mode ?>"><?= ucfirst($mode) ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($hasResults): ?>
                        <div class="docs-results-list">
                            <?php foreach ($articles as $article): ?>
                                <article class="docs-result-card">
                                    <div class="docs-result-card__header">
                                        <h3 class="docs-result-card__title">
                                            <a href="/blog/article/<?= htmlspecialchars($article['slug']) ?>" class="docs-result-card__link">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </a>
                                        </h3>

                                        <?php if (!empty($article['category_name'])): ?>
                                            <span class="docs-result-card__category">
                                                <?= htmlspecialchars($article['category_name']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($article['snippet'])): ?>
                                        <div class="docs-result-card__excerpt">
                                            <?= $article['snippet'] ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="docs-result-card__footer">
                                        <?php if (!empty($article['rank'])): ?>
                                            <span class="relevance-score">
                                                Relevance: <?= number_format(abs((float) $article['rank']) * 1000000, 1) ?>
                                            </span>
                                        <?php endif; ?>

                                        <a href="/blog/article/<?= htmlspecialchars($article['slug']) ?>" class="docs-result-card__link-more">
                                            View documentation →
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results-section">
                            <div class="no-results-icon">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                </svg>
                            </div>

                            <h3 class="no-results-title">No documentation found</h3>
                            <p class="no-results-description">
                                We couldn't find any documentation matching "<strong><?= htmlspecialchars($query) ?></strong>".
                            </p>

                            <div class="search-suggestions">
                                <h4 class="suggestions-title">Try:</h4>
                                <ul class="suggestions-list">
                                    <li>Different or fewer keywords</li>
                                    <li>Check your spelling</li>
                                    <li>Use "Fuzzy" mode for typo tolerance</li>
                                    <li>Browse the <a href="/docs#components">Components</a> section</li>
                                </ul>
                            </div>

                            <div class="no-results-actions">
                                <a href="/docs" class="btn btn--primary">Browse All Documentation</a>
                                <button type="button" class="btn btn--outline" onclick="document.querySelector('.search-input').focus()">
                                    Try Another Search
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="search-help-section">
                    <h2 class="search-help-title">Search Documentation</h2>
                    <p class="search-help-description">
                        Find components, design tokens, patterns, and usage guides.
                    </p>

                    <div class="quick-links-grid">
                        <a href="/docs#components" class="quick-link-card">
                            <div class="quick-link-card__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </div>
                            <h3 class="quick-link-card__title">Components</h3>
                            <p class="quick-link-card__description">Buttons, cards, alerts, and more</p>
                        </a>

                        <a href="/docs#design-tokens" class="quick-link-card">
                            <div class="quick-link-card__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <circle cx="12" cy="12" r="4"></circle>
                                </svg>
                            </div>
                            <h3 class="quick-link-card__title">Design Tokens</h3>
                            <p class="quick-link-card__description">Colors, spacing, typography</p>
                        </a>

                        <a href="/docs#patterns" class="quick-link-card">
                            <div class="quick-link-card__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <h3 class="quick-link-card__title">Patterns</h3>
                            <p class="quick-link-card__description">Common UI patterns</p>
                        </a>

                        <a href="/docs#playground" class="quick-link-card">
                            <div class="quick-link-card__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                    <polyline points="2 17 12 22 22 17"></polyline>
                                    <polyline points="2 12 12 17 22 12"></polyline>
                                </svg>
                            </div>
                            <h3 class="quick-link-card__title">Playground</h3>
                            <p class="quick-link-card__description">Interactive component testing</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
