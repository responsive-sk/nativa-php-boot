<?php

declare(strict_types=1);

namespace Application\Saga\Article;

use Application\Saga\SagaOrchestrator;
use Application\Services\ArticleManager;
use Infrastructure\Queue\QueueRepository;
use Domain\Model\Article;

/**
 * Publish Article Saga
 * 
 * Orchestrates the article publishing process:
 * 1. Publish article (change status)
 * 2. Invalidate cache
 * 3. Queue notification to subscribers
 * 4. Queue reindex for search
 * 
 * If any step fails, all previous steps are compensated (rolled back)
 */
class PublishArticleSaga
{
    private SagaOrchestrator $orchestrator;

    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly QueueRepository $queue,
    ) {
        $this->orchestrator = new SagaOrchestrator();
    }

    /**
     * Execute the publish article saga
     * 
     * @param string $articleId ID of article to publish
     * @return Article Published article
     * @throws \Application\Saga\SagaExecutionFailedException If any step fails
     */
    public function execute(string $articleId): Article
    {
        // Get article for title
        $article = $this->articleManager->findById($articleId);
        
        if ($article === null) {
            throw new \RuntimeException("Article {$articleId} not found");
        }

        // Build saga steps
        $this->orchestrator
            ->addStep(new PublishArticleStep($this->articleManager, $articleId))
            ->addStep(new InvalidateCacheStep($articleId))
            ->addStep(new QueueNotificationStep($this->queue, $articleId, $article->title()));

        // Execute all steps
        $this->orchestrator->execute();

        // Return published article
        return $this->articleManager->findById($articleId);
    }

    /**
     * Get saga status for debugging
     *
     * @return array Status information
     */
    public function getStatus(): array
    {
        return $this->orchestrator->getStatus();
    }

    /**
     * Check if saga completed successfully
     */
    public function isCompleted(): bool
    {
        return $this->orchestrator->isCompleted();
    }
}
