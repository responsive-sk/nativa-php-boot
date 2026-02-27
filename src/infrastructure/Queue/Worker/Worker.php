<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Worker;

use Infrastructure\Queue\QueueRepository;
use Infrastructure\Queue\Entities\Job;

/**
 * Queue Worker - Processes jobs from the queue
 */
class Worker
{
    private bool $shouldQuit = false;

    public function __construct(
        private readonly QueueRepository $queue,
        private readonly JobHandler $handler,
    ) {
    }

    /**
     * Process jobs from the queue
     */
    public function work(string $queue, int $maxTries = 3, int $timeout = 60): void
    {
        echo "Starting worker for queue: {$queue}\n";
        echo "Press Ctrl+C to stop\n\n";

        while (!$this->shouldQuit) {
            $job = $this->queue->reserve($queue, $timeout);

            if ($job === null) {
                sleep(1); // No job available, wait
                continue;
            }

            $this->processJob($job, $maxTries);
        }

        echo "Worker stopped\n";
    }

    /**
     * Process a single job
     */
    private function processJob(Job $job, int $maxTries): void
    {
        $jobName = $job->payload()['job'] ?? 'Unknown';
        echo "[" . date('Y-m-d H:i:s') . "] Processing: {$jobName}\n";

        try {
            // Check if job has exceeded max attempts
            if ($job->attempts() > $maxTries) {
                throw new \RuntimeException("Job has exceeded maximum attempts ({$maxTries})");
            }

            // Process the job
            $this->handler->handle($job);

            // Delete successful job
            $this->queue->delete($job);
            echo "[" . date('Y-m-d H:i:s') . "] Completed: {$jobName}\n";

        } catch (\Throwable $e) {
            echo "[" . date('Y-m-d H:i:s') . "] Failed: {$jobName} - {$e->getMessage()}\n";

            // Release job back to queue or mark as failed
            if ($job->attempts() >= $maxTries) {
                $this->queue->fail($job, $e);
                echo "[" . date('Y-m-d H:i:s') . "] Marked as failed: {$jobName}\n";
            } else {
                // Release with delay (exponential backoff)
                $delay = pow(2, $job->attempts()) * 5; // 5s, 10s, 20s...
                $this->queue->release($job, $delay);
                echo "[" . date('Y-m-d H:i:s') . "] Released with {$delay}s delay: {$jobName}\n";
            }
        }
    }

    /**
     * Stop the worker
     */
    public function stop(): void
    {
        $this->shouldQuit = true;
    }

    /**
     * Process a single job (for testing)
     */
    public function processSingle(string $queue): void
    {
        $job = $this->queue->reserve($queue);
        if ($job !== null) {
            $this->processJob($job, 3);
        }
    }
}
