<?php

declare(strict_types=1);

namespace Infrastructure\Persistence;

use Infrastructure\Paths\AppPaths;
use PDO;

/**
 * Database Connection Manager - Manages multiple SQLite connections
 */
class DatabaseConnectionManager
{
    /** @var array<string, PDO> */
    private array $connections = [];

    /** @var array<string, string> */
    private array $dsnConfig = [];

    private AppPaths $paths;

    public function __construct()
    {
        $this->paths = AppPaths::instance();
        $this->loadDsnConfig();
    }

    /**
     * Load database configuration from environment
     */
    private function loadDsnConfig(): void
    {
        // CMS Database (main application data)
        $cmsDb = $_ENV['DB_CMS'] ?? 'cms.db';
        $cmsPath = str_starts_with($cmsDb, '/') ? $cmsDb : $this->paths->data($cmsDb);
        $this->dsnConfig['cms'] = 'sqlite:' . $cmsPath;

        // Jobs Database (queue, outbox)
        $jobsDb = $_ENV['DB_JOBS'] ?? 'jobs.db';
        $jobsPath = str_starts_with($jobsDb, '/') ? $jobsDb : $this->paths->data($jobsDb);
        $this->dsnConfig['jobs'] = 'sqlite:' . $jobsPath;

        // Additional databases can be added via env
        // DB_CUSTOM_NAME=path/to/file.db
        foreach ($_ENV as $key => $value) {
            if (str_starts_with($key, 'DB_') && !in_array($key, ['DB_CMS', 'DB_JOBS'])) {
                $name = strtolower(str_replace('DB_', '', $key));
                $dbPath = str_starts_with($value, '/') ? $value : $this->paths->data($value);
                $this->dsnConfig[$name] = 'sqlite:' . $dbPath;
            }
        }
    }

    /**
     * Get database connection by name
     *
     * @param string $name Connection name (cms, jobs, etc.)
     */
    public function getConnection(string $name = 'cms'): PDO
    {
        // Return existing connection if already created (e.g., :memory: for tests)
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        if (!isset($this->dsnConfig[$name])) {
            throw new \InvalidArgumentException("Database connection '{$name}' is not configured");
        }

        $this->connections[$name] = $this->createConnection($this->dsnConfig[$name]);

        return $this->connections[$name];
    }

    /**
     * Create a new PDO connection
     */
    private function createConnection(string $dsn): PDO
    {
        // Extract database path from DSN and ensure directory exists
        if (str_starts_with($dsn, 'sqlite:')) {
            $dbPath = substr($dsn, 7); // Remove 'sqlite:' prefix
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
        }

        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // SQLite specific optimizations
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');

        return $pdo;
    }

    /**
     * Check if connection exists
     */
    public function hasConnection(string $name): bool
    {
        return isset($this->connections[$name]);
    }

    /**
     * Get all configured connection names
     *
     * @return array<string>
     */
    public function getConnectionNames(): array
    {
        return array_keys($this->dsnConfig);
    }

    /**
     * Close a specific connection
     */
    public function closeConnection(string $name): void
    {
        if (isset($this->connections[$name])) {
            $this->connections[$name] = null;
            unset($this->connections[$name]);
        }
    }

    /**
     * Close all connections
     */
    public function closeAll(): void
    {
        $this->connections = [];
    }
}
