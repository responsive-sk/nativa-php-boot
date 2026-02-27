<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Handlers;

use Application\CQRS\Article\Queries\ListArticles;
use Application\Services\ArticleManager;
use Domain\Model\Article;

/**
 * List Articles Handler
 */
class ListArticlesHandler
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    /**
     * @return array<Article>
     */
    public function __invoke(ListArticles $query): array
    {
        return $this->articleManager->listPublished($query->limit, $query->offset);
    }
}
