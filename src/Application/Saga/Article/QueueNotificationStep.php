<?php

declare(strict_types = 1);

namespace Application\Saga\Article;

use Application\Saga\SagaStep;
use Infrastructure\Queue\Entities\Job;
use Infrastructure\Queue\QueueRepository;

/**
 * Queue Notification Step.
 *
 * Executes: Adds notification job to queue
 * Compensates: Nothing to rollback (notification already queued)
 */
final class QueueNotificationStep extends SagaStep
{
    public function __construct(
        private readonly QueueRepository $queue,
        private readonly string $articleId,
        private readonly string $articleTitle,
    ) {}

    #[\Override]
    public function execute(): void
    {
        // Create notification job
        $job = new Job(
            queue: 'notifications',
            payload: [
                'job'  => 'send-article-notification',
                'data' => [
                    'article_id'    => $this->articleId,
                    'article_title' => $this->articleTitle,
                ],
            ]
        );

        $this->queue->push($job);
    }

    #[\Override]
    public function compensate(): void
    {
        // Notification already queued - can't really undo
        // Could add a "cancel notification" job if needed
    }

    #[\Override]
    public function getName(): string
    {
        return 'Queue Notification';
    }
}
