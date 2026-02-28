<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use Infrastructure\Paths\AppPaths;
use PDO;
use PDOException;

/**
 * SQLite Database Connection - Legacy wrapper for backwards compatibility
 * Uses DatabaseConnectionManager internally
 */
class DatabaseConnection
{
    private DatabaseConnectionManager $manager;
    private string $connectionName;
    private ?string $dbPath;

    public function __construct(?string $dbPath = null)
    {
        $this->dbPath = $dbPath;
        $this->manager = new DatabaseConnectionManager();

        if ($dbPath !== null) {
            $this->connectionName = 'custom';

            // Handle :memory: specially for tests
            if ($dbPath === ':memory:') {
                $pdo = new PDO('sqlite::memory:');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->exec('PRAGMA foreign_keys = ON');

                // Register via reflection
                $reflection = new \ReflectionClass($this->manager);
                $property = $reflection->getProperty('connections');
                $property->setAccessible(true);
                $connections = $property->getValue($this->manager);
                $connections['custom'] = $pdo;
                $property->setValue($this->manager, $connections);
            } else {
                // Use AppPaths for consistent path resolution
                $paths = AppPaths::instance();
                $fullPath = str_starts_with($dbPath, '/') ? $dbPath : $paths->getBasePath() . '/' . $dbPath;

                // Ensure directory exists
                $dir = dirname($fullPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                // Create custom connection
                $dsn = 'sqlite:' . $fullPath;
                $pdo = new PDO($dsn);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->exec('PRAGMA foreign_keys = ON');
                $pdo->exec('PRAGMA journal_mode = WAL');

                // Register via reflection
                $reflection = new \ReflectionClass($this->manager);
                $property = $reflection->getProperty('connections');
                $property->setAccessible(true);
                $connections = $property->getValue($this->manager);
                $connections['custom'] = $pdo;
                $property->setValue($this->manager, $connections);
            }
        } else {
            $this->connectionName = 'cms';
        }
    }

    public function getConnection(): PDO
    {
        return $this->manager->getConnection($this->connectionName);
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollBack(): bool
    {
        return $this->getConnection()->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    public function close(): void
    {
        $this->manager->closeConnection($this->connectionName);
    }

    public function __destruct()
    {
        $this->close();
    }
}
