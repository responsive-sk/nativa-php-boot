<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Handlers;

use Application\CQRS\Article\Queries\GetArticleBySlug;
use Application\Services\ArticleManager;
use Domain\Model\Article;

/**
 * Get Article By Slug Handler
 */
class GetArticleBySlugHandler
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    public function __invoke(GetArticleBySlug $query): ?Article
    {
        return $this->articleManager->findBySlug($query->slug);
    }
}
