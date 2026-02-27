<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Handlers;

use Application\CQRS\Article\Commands\CreateArticle;
use Application\Services\ArticleManager;
use Domain\Model\Article;

/**
 * Create Article Handler
 */
class CreateArticleHandler
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    public function __invoke(CreateArticle $command): Article
    {
        return $this->articleManager->create(
            title: $command->title,
            content: $command->content,
            authorId: $command->authorId,
            categoryId: $command->categoryId,
            excerpt: $command->excerpt,
            tags: $command->tags,
            image: $command->image,
        );
    }
}
