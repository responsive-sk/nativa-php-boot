<?php

declare(strict_types = 1);

namespace Application\Saga\Article;

use Application\Saga\SagaStep;
use Application\Services\ArticleManager;
use Domain\Model\Article;

/**
 * Publish Article Step.
 *
 * Executes: Publishes the article
 * Compensates: Unpublishes the article (rollback)
 */
final class PublishArticleStep extends SagaStep
{
    private ?Article $originalArticle = null;

    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly string $articleId,
    ) {}

    #[\Override]
    public function execute(): Article
    {
        // Store original state for compensation
        $this->originalArticle = $this->articleManager->findById($this->articleId);

        // Publish the article
        return $this->articleManager->publish($this->articleId);
    }

    #[\Override]
    public function compensate(): void
    {
        // Rollback: Unpublish the article
        if (null !== $this->originalArticle && $this->originalArticle->isPublished()) {
            try {
                $this->articleManager->unpublish($this->articleId);
            } catch (\Throwable $e) {
                error_log(\sprintf(
                    'Failed to compensate PublishArticleStep: %s',
                    $e->getMessage()
                ));

                throw $e; // Re-throw to indicate compensation failure
            }
        }
    }

    #[\Override]
    public function getName(): string
    {
        return 'Publish Article';
    }
}
