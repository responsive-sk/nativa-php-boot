<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use PDOStatement;
use RuntimeException;

/**
 * PDO Statement Helper
 * 
 * Provides safe PDO statement execution with proper type assertions
 */
trait StatementExecutor
{
    /**
     * Prepare and execute SQL statement with proper error handling
     * 
     * @param array<string, mixed> $params
     * @return PDOStatement
     * @throws RuntimeException
     */
    protected function executeQuery(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        assert($stmt instanceof PDOStatement, 'Failed to prepare SQL statement');
        
        if ($params !== []) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        
        return $stmt;
    }

    /**
     * Fetch single row from database
     * 
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->executeQuery($sql, $params);
        $result = $stmt->fetch();
        
        if ($result === false) {
            return null;
        }
        
        /** @var array<string, mixed> $result */
        return $result;
    }

    /**
     * Fetch all rows from database
     * 
     * @param array<string, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->executeQuery($sql, $params);
        
        /** @var array<int, array<string, mixed>> */
        return $stmt->fetchAll();
    }

    /**
     * Fetch single column value
     * 
     * @param array<string, mixed> $params
     * @return mixed
     */
    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Get database connection
     */
    abstract protected function getConnection(): \PDO;
}
