<?php

declare(strict_types=1);

namespace Application\Saga\Article;

use Application\Saga\SagaStep;
use Infrastructure\Paths\AppPaths;

/**
 * Invalidate Cache Step
 * 
 * Executes: Clears article cache
 * Compensates: Nothing to rollback (cache will rebuild)
 */
class InvalidateCacheStep extends SagaStep
{
    private AppPaths $paths;
    
    public function __construct(
        private readonly string $articleId,
    ) {
        $this->paths = AppPaths::instance();
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

        $cacheDir = $this->paths->cache('templates');
        
        foreach ($keys as $key) {
            $cacheFile = $cacheDir . '/' . md5($key) . '.php';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        }

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
