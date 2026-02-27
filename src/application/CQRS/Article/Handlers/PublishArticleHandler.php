<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Handlers;

use Application\CQRS\Article\Commands\PublishArticle;
use Application\Services\ArticleManager;
use Domain\Model\Article;

/**
 * Publish Article Handler
 */
class PublishArticleHandler
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    public function __invoke(PublishArticle $command): Article
    {
        return $this->articleManager->publish($command->articleId);
    }
}
