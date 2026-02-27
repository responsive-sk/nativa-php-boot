<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Handlers;

use Infrastructure\Persistence\DatabaseConnectionManager;
use PDO;

/**
 * Outbox Processor - Processes events from the outbox table
 */
class OutboxProcessor
{
    private ?PDO $cmsConnection = null;
    private ?PDO $jobsConnection = null;

    public function __construct(
        private readonly DatabaseConnectionManager $dbManager
    ) {
    }

    private function getCmsConnection(): PDO
    {
        if ($this->cmsConnection === null) {
            $this->cmsConnection = $this->dbManager->getConnection('cms');
            $this->ensureOutboxTableExists();
        }
        return $this->cmsConnection;
    }

    private function getJobsConnection(): PDO
    {
        if ($this->jobsConnection === null) {
            $this->jobsConnection = $this->dbManager->getConnection('jobs');
        }
        return $this->jobsConnection;
    }

    /**
     * Create outbox table if it doesn't exist
     */
    private function ensureOutboxTableExists(): void
    {
        $this->getCmsConnection()->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS outbox (
                id VARCHAR(36) PRIMARY KEY,
                event_type VARCHAR(100) NOT NULL,
                event_data TEXT NOT NULL,
                processed BOOLEAN DEFAULT FALSE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                processed_at DATETIME
            )
        SQL);

        $this->getCmsConnection()->exec(<<<SQL
            CREATE INDEX IF NOT EXISTS idx_outbox_processed 
            ON outbox(processed, created_at)
        SQL);
    }

    /**
     * Add event to outbox (called within same transaction as entity save)
     */
    public function add(string $eventType, array $eventData): string
    {
        $id = $this->generateId();

        $stmt = $this->getCmsConnection()->prepare(<<<SQL
            INSERT INTO outbox (id, event_type, event_data, created_at)
            VALUES (:id, :event_type, :event_data, :created_at)
        SQL);

        $stmt->execute([
            ':id' => $id,
            ':event_type' => $eventType,
            ':event_data' => json_encode($eventData),
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        return $id;
    }

    /**
     * Process unprocessed outbox events
     */
    public function process(): int
    {
        // Get unprocessed events
        $stmt = $this->getCmsConnection()->query(<<<SQL
            SELECT * FROM outbox
            WHERE processed = FALSE
            ORDER BY created_at ASC
            LIMIT 100
        SQL);

        $events = $stmt->fetchAll();
        $processedCount = 0;

        foreach ($events as $event) {
            try {
                $this->dispatchEvent($event);
                $this->markAsProcessed($event['id']);
                $processedCount++;
            } catch (\Throwable $e) {
                error_log(sprintf(
                    "Failed to process outbox event %s: %s",
                    $event['id'],
                    $e->getMessage()
                ));
            }
        }

        return $processedCount;
    }

    /**
     * Dispatch event to queue
     */
    private function dispatchEvent(array $event): void
    {
        $eventType = $event['event_type'];
        $eventData = json_decode($event['event_data'], true);

        // Map event types to queue jobs
        $jobMap = [
            'ArticleCreated' => 'send-article-notification',
            'ArticlePublished' => 'reindex-article',
            'ArticleUpdated' => 'reindex-article',
            'ArticleDeleted' => 'remove-article-index',
            'FormSubmitted' => 'send-form-notification',
        ];

        $jobName = $jobMap[$eventType] ?? null;

        if ($jobName === null) {
            // Unknown event type, skip
            return;
        }

        // Push to queue
        $this->pushToQueue($jobName, $eventData);
    }

    /**
     * Push job to queue
     */
    private function pushToQueue(string $jobName, array $data): void
    {
        $id = $this->generateId();
        $now = date('Y-m-d H:i:s');

        $stmt = $this->getJobsConnection()->prepare(<<<SQL
            INSERT INTO jobs (id, queue, payload, attempts, reserved_at, available_at, created_at)
            VALUES (:id, :queue, :payload, 0, NULL, :available_at, :created_at)
        SQL);

        $stmt->execute([
            ':id' => $id,
            ':queue' => 'default',
            ':payload' => json_encode([
                'job' => $jobName,
                'data' => $data,
            ]),
            ':available_at' => $now,
            ':created_at' => $now,
        ]);
    }

    /**
     * Mark event as processed
     */
    private function markAsProcessed(string $id): void
    {
        $stmt = $this->getCmsConnection()->prepare(<<<SQL
            UPDATE outbox
            SET processed = TRUE, processed_at = :processed_at
            WHERE id = :id
        SQL);

        $stmt->execute([
            ':processed_at' => date('Y-m-d H:i:s'),
            ':id' => $id,
        ]);
    }

    /**
     * Get outbox stats
     */
    public function getStats(): array
    {
        $stmt = $this->getCmsConnection()->query(<<<SQL
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN processed = FALSE THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN processed = TRUE THEN 1 ELSE 0 END) as processed
            FROM outbox
        SQL);

        return $stmt->fetch();
    }

    /**
     * Generate unique ID
     */
    private function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
