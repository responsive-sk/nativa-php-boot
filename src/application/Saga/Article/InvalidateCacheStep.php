<?php

declare(strict_types=1);

namespace Application\Saga\Article;

use Application\Saga\SagaStep;

/**
 * Invalidate Cache Step
 * 
 * Executes: Clears article cache
 * Compensates: Nothing to rollback (cache will rebuild)
 */
class InvalidateCacheStep extends SagaStep
{
    private bool $cacheCleared = false;

    public function __construct(
        private readonly string $articleId,
    ) {
    }

    public function execute(): bool
    {
        // Clear article-related cache items
        $keys = [
            "article_{$this->articleId}",
            "article_slug_{$this->articleId}",
            'article_list_published',
            'article_list_latest',
        ];

        foreach ($keys as $key) {
            // Simple file-based cache deletion
            $cacheFile = __DIR__ . '/../../../../storage/cache/templates/' . md5($key) . '.php';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        }

        $this->cacheCleared = true;
        return true;
    }

    public function compensate(): void
    {
        // Cache invalidation is idempotent - no need to rollback
        // Cache will naturally rebuild on next request
    }

    public function getName(): string
    {
        return 'Invalidate Cache';
    }
}
