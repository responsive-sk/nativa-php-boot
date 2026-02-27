<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Worker;

use Infrastructure\Queue\Entities\Job;
use Infrastructure\Queue\Handlers\JobHandlerRegistry;

/**
 * Job Handler - Dispatches jobs to their handlers
 */
class JobHandler implements JobHandlerInterface
{
    public function __construct(
        private readonly JobHandlerRegistry $registry,
    ) {
    }

    public function handle(Job $job): void
    {
        $jobName = $job->payload()['job'] ?? null;

        if ($jobName === null) {
            throw new \RuntimeException('Job name not found in payload');
        }

        $handler = $this->registry->getHandler($jobName);

        if ($handler === null) {
            throw new \RuntimeException("No handler registered for job: {$jobName}");
        }

        // Call the handler
        $handler($job);
    }
}
