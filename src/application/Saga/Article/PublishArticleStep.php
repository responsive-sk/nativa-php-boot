<?php

declare(strict_types=1);

namespace Application\Saga\Article;

use Application\Services\ArticleManager;
use Application\Saga\SagaStep;
use Domain\Model\Article;

/**
 * Publish Article Step
 * 
 * Executes: Publishes the article
 * Compensates: Unpublishes the article (rollback)
 */
class PublishArticleStep extends SagaStep
{
    private ?Article $originalArticle = null;

    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly string $articleId,
    ) {
    }

    public function execute(): Article
    {
        // Store original state for compensation
        $this->originalArticle = $this->articleManager->findById($this->articleId);
        
        // Publish the article
        return $this->articleManager->publish($this->articleId);
    }

    public function compensate(): void
    {
        // Rollback: Unpublish the article
        if ($this->originalArticle !== null && $this->originalArticle->isPublished()) {
            try {
                $this->articleManager->unpublish($this->articleId);
            } catch (\Throwable $e) {
                error_log(sprintf(
                    "Failed to compensate PublishArticleStep: %s",
                    $e->getMessage()
                ));
                throw $e; // Re-throw to indicate compensation failure
            }
        }
    }

    public function getName(): string
    {
        return 'Publish Article';
    }
}
