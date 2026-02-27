<?php

declare(strict_types=1);

namespace Application\Saga\Article;

use Application\Saga\SagaStep;
use Infrastructure\Queue\QueueRepository;
use Infrastructure\Queue\Entities\Job;

/**
 * Queue Notification Step
 * 
 * Executes: Adds notification job to queue
 * Compensates: Nothing to rollback (notification already queued)
 */
class QueueNotificationStep extends SagaStep
{
    public function __construct(
        private readonly QueueRepository $queue,
        private readonly string $articleId,
        private readonly string $articleTitle,
    ) {
    }

    public function execute(): void
    {
        // Create notification job
        $job = new Job(
            queue: 'notifications',
            payload: [
                'job' => 'send-article-notification',
                'data' => [
                    'article_id' => $this->articleId,
                    'article_title' => $this->articleTitle,
                ],
            ]
        );

        $this->queue->push($job);
    }

    public function compensate(): void
    {
        // Notification already queued - can't really undo
        // Could add a "cancel notification" job if needed
    }

    public function getName(): string
    {
        return 'Queue Notification';
    }
}
