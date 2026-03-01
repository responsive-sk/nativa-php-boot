<?php

declare(strict_types=1);

namespace Infrastructure\Queue;

use Infrastructure\Persistence\DatabaseConnectionManager;
use Infrastructure\Queue\Entities\Job;
use Infrastructure\Queue\Entities\FailedJob;
use PDO;

/**
 * SQLite Queue Repository
 */
class QueueRepository
{
    private PDO $connection;

    public function __construct(
        DatabaseConnectionManager $dbManager
    ) {
        $this->connection = $dbManager->getConnection('jobs');
        $this->ensureTablesExist();
    }

    /**
     * Create queue tables if they don't exist
     */
    private function ensureTablesExist(): void
    {
        // Jobs table
        $this->connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS jobs (
                id VARCHAR(36) PRIMARY KEY,
                queue VARCHAR(50) NOT NULL,
                payload TEXT NOT NULL,
                attempts INTEGER DEFAULT 0,
                reserved_at DATETIME,
                available_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        SQL);

        // Indexes for performance
        $this->connection->exec(<<<SQL
            CREATE INDEX IF NOT EXISTS idx_jobs_queue_available 
            ON jobs(queue, available_at)
        SQL);

        $this->connection->exec(<<<SQL
            CREATE INDEX IF NOT EXISTS idx_jobs_reserved 
            ON jobs(reserved_at)
        SQL);

        // Failed jobs table
        $this->connection->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS failed_jobs (
                id VARCHAR(36) PRIMARY KEY,
                queue VARCHAR(50) NOT NULL,
                payload TEXT NOT NULL,
                exception TEXT NOT NULL,
                failed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        SQL);
    }

    /**
     * Push a new job to the queue
     */
    public function push(Job $job): void
    {
        $job->setId($this->generateId());
        
        $stmt = $this->connection->prepare(<<<SQL
            INSERT INTO jobs (id, queue, payload, attempts, reserved_at, available_at, created_at)
            VALUES (:id, :queue, :payload, :attempts, :reserved_at, :available_at, :created_at)
        SQL);

        $data = $job->toArray();
        $stmt->execute($data);
    }

    /**
     * Reserve a job from the queue
     */
    public function reserve(string $queue, int $timeout = 60): ?Job
    {
        $now = date('Y-m-d H:i:s');
        $reservedAt = date('Y-m-d H:i:s', time() + $timeout);

        // Find available job
        $stmt = $this->connection->prepare(<<<SQL
            SELECT * FROM jobs
            WHERE queue = :queue
              AND reserved_at IS NULL
              AND available_at <= :now
            ORDER BY available_at ASC
            LIMIT 1
        SQL);

        $stmt->execute([
            ':queue' => $queue,
            ':now' => $now,
        ]);

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $job = Job::fromArray($data);
        $job->setReservedAt($reservedAt);
        $job->incrementAttempts();

        // Update the job
        $this->update($job);

        return $job;
    }

    /**
     * Delete a job from the queue
     */
    public function delete(Job $job): void
    {
        $stmt = $this->connection->prepare('DELETE FROM jobs WHERE id = ?');
        $stmt->execute([$job->id()]);
    }

    /**
     * Release a job back to the queue
     */
    public function release(Job $job, int $delay = 0): void
    {
        $availableAt = date('Y-m-d H:i:s', time() + $delay);
        $job->setReservedAt(null);

        $stmt = $this->connection->prepare(<<<SQL
            UPDATE jobs
            SET reserved_at = NULL,
                available_at = :available_at,
                attempts = :attempts
            WHERE id = :id
        SQL);

        $stmt->execute([
            ':available_at' => $availableAt,
            ':attempts' => $job->attempts(),
            ':id' => $job->id(),
        ]);
    }

    /**
     * Mark a job as failed
     */
    public function fail(Job $job, \Throwable $exception): void
    {
        $failedJob = new FailedJob(
            queue: $job->queue(),
            payload: $job->payload(),
            exception: $exception->getMessage() . "\n" . $exception->getTraceAsString()
        );
        $failedJob->setId($this->generateId());

        $stmt = $this->connection->prepare(<<<SQL
            INSERT INTO failed_jobs (id, queue, payload, exception, failed_at)
            VALUES (:id, :queue, :payload, :exception, :failed_at)
        SQL);

        $stmt->execute($failedJob->toArray());

        // Delete the original job
        $this->delete($job);
    }

    /**
     * Get job by ID
     */
    public function getJob(string $id): ?Job
    {
        $stmt = $this->connection->prepare('SELECT * FROM jobs WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Job::fromArray($data);
    }

    /**
     * Get queue size
     */
    public function size(string $queue): int
    {
        $stmt = $this->connection->prepare(<<<SQL
            SELECT COUNT(*) FROM jobs
            WHERE queue = :queue AND reserved_at IS NULL AND available_at <= :now
        SQL);

        $stmt->execute([
            ':queue' => $queue,
            ':now' => date('Y-m-d H:i:s'),
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get failed jobs count
     */
    public function failedCount(): int
    {
        $stmt = $this->connection->query('SELECT COUNT(*) FROM failed_jobs');
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all failed jobs
     *
     * @return array<FailedJob>
     */
    public function getFailedJobs(int $limit = 100): array
    {
        $stmt = $this->connection->prepare(<<<SQL
            SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT :limit
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => FailedJob::fromArray($row), $stmt->fetchAll());
    }

    /**
     * Delete a failed job
     */
    public function deleteFailed(string $id): void
    {
        $stmt = $this->connection->prepare('DELETE FROM failed_jobs WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Clear all jobs from queue
     */
    public function clear(string $queue): void
    {
        $stmt = $this->connection->prepare('DELETE FROM jobs WHERE queue = ?');
        $stmt->execute([$queue]);
    }

    /**
     * Update a job
     */
    private function update(Job $job): void
    {
        $stmt = $this->connection->prepare(<<<SQL
            UPDATE jobs
            SET queue = :queue,
                payload = :payload,
                attempts = :attempts,
                reserved_at = :reserved_at,
                available_at = :available_at
            WHERE id = :id
        SQL);

        $stmt->execute($job->toArray());
    }

    /**
     * Generate unique ID
     */
    private function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
