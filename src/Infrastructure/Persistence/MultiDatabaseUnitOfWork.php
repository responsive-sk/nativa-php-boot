<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use PDO;

/**
 * Multi-Database Unit of Work - Manages transactions across multiple databases
 */
class MultiDatabaseUnitOfWork
{
    /** @var array<string, PDO> */
    private array $connections = [];

    /** @var array<string, bool> */
    private array $transactionStarted = [];

    public function __construct(
        private readonly DatabaseConnectionManager $manager
    ) {
    }

    /**
     * Begin transaction on specific database connection
     */
    public function beginTransaction(string $connection = 'cms'): void
    {
        if (!isset($this->transactionStarted[$connection]) || !$this->transactionStarted[$connection]) {
            $this->getConnection($connection)->beginTransaction();
            $this->transactionStarted[$connection] = true;
        }
    }

    /**
     * Commit transaction on specific database connection
     */
    public function commit(string $connection = 'cms'): void
    {
        if (isset($this->transactionStarted[$connection]) && $this->transactionStarted[$connection]) {
            $this->getConnection($connection)->commit();
            $this->transactionStarted[$connection] = false;
        }
    }

    /**
     * Rollback transaction on specific database connection
     */
    public function rollback(string $connection = 'cms'): void
    {
        if (isset($this->transactionStarted[$connection]) && $this->transactionStarted[$connection]) {
            $this->getConnection($connection)->rollBack();
            $this->transactionStarted[$connection] = false;
        }
    }

    /**
     * Get connection by name
     */
    public function getConnection(string $connection = 'cms'): PDO
    {
        if (!isset($this->connections[$connection])) {
            $this->connections[$connection] = $this->manager->getConnection($connection);
        }
        return $this->connections[$connection];
    }

    /**
     * Check if transaction is active on connection
     */
    public function inTransaction(string $connection = 'cms'): bool
    {
        return isset($this->transactionStarted[$connection]) && $this->transactionStarted[$connection];
    }

    /**
     * Commit all active transactions
     */
    public function commitAll(): void
    {
        foreach (array_keys($this->transactionStarted) as $connection) {
            if ($this->transactionStarted[$connection]) {
                $this->commit($connection);
            }
        }
    }

    /**
     * Rollback all active transactions
     */
    public function rollbackAll(): void
    {
        foreach (array_keys($this->transactionStarted) as $connection) {
            if ($this->transactionStarted[$connection]) {
                $this->rollback($connection);
            }
        }
    }

    /**
     * Close all connections
     */
    public function closeAll(): void
    {
        $this->connections = [];
        $this->transactionStarted = [];
    }
}
