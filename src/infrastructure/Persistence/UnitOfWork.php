<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use PDO;

/**
 * Unit of Work Pattern Implementation
 * Ensures transactional consistency across repositories
 */
class UnitOfWork
{
    private DatabaseConnection $db;
    private bool $transactionStarted = false;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    public function beginTransaction(): void
    {
        if (!$this->transactionStarted) {
            $this->db->beginTransaction();
            $this->transactionStarted = true;
        }
    }

    public function commit(): void
    {
        if ($this->transactionStarted) {
            $this->db->commit();
            $this->transactionStarted = false;
        }
    }

    public function rollback(): void
    {
        if ($this->transactionStarted) {
            $this->db->rollBack();
            $this->transactionStarted = false;
        }
    }

    public function getConnection(): PDO
    {
        return $this->db->getConnection();
    }

    public function __destruct()
    {
        if ($this->transactionStarted) {
            $this->rollback();
        }
    }
}
