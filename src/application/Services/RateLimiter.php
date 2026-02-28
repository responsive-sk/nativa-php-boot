<?php

declare(strict_types=1);

namespace Application\Services;

/**
 * Rate Limiter Service
 *
 * Implements sliding window rate limiting algorithm using SQLite.
 * Tracks request counts per key within time windows.
 *
 * Usage:
 *   $limiter = new RateLimiter($db);
 *   if (!$limiter->isAllowed('login:192.168.1.1', 5, 60)) {
 *       throw new RateLimitException('Too many attempts');
 *   }
 */
class RateLimiter
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->initializeTable();
    }

    /**
     * Check if request is allowed under rate limit
     *
     * @param string $key Unique identifier (e.g., "login:192.168.1.1")
     * @param int $maxRequests Maximum requests allowed in window
     * @param int $windowSeconds Time window in seconds
     * @return bool True if allowed, false if rate limited
     */
    public function isAllowed(string $key, int $maxRequests, int $windowSeconds): bool
    {
        $now = time();
        $windowStart = $now - $windowSeconds;

        // Clean up old entries
        $this->cleanup($key, $windowStart);

        // Count recent requests
        $count = $this->countRequests($key, $windowStart);

        if ($count >= $maxRequests) {
            return false;
        }

        // Record this request
        $this->recordRequest($key, $now);

        return true;
    }

    /**
     * Get remaining requests in current window
     */
    public function getRemaining(string $key, int $maxRequests, int $windowSeconds): int
    {
        $now = time();
        $windowStart = $now - $windowSeconds;

        $count = $this->countRequests($key, $windowStart);

        return max(0, $maxRequests - $count);
    }

    /**
     * Get retry-after seconds (when to try again)
     */
    public function getRetryAfter(string $key, int $windowSeconds): int
    {
        $now = time();
        $windowStart = $now - $windowSeconds;

        // Get oldest request in current window
        $stmt = $this->db->prepare(
            'SELECT timestamp FROM rate_limits 
             WHERE key = :key AND timestamp > :window_start 
             ORDER BY timestamp ASC 
             LIMIT 1'
        );
        $stmt->execute([
            ':key' => $key,
            ':window_start' => $windowStart,
        ]);

        $oldest = $stmt->fetchColumn();

        if ($oldest === false) {
            return 0;
        }

        return (int) ($oldest + $windowSeconds - $now);
    }

    /**
     * Reset rate limit for a key
     */
    public function reset(string $key): void
    {
        $stmt = $this->db->prepare('DELETE FROM rate_limits WHERE key = :key');
        $stmt->execute([':key' => $key]);
    }

    /**
     * Initialize rate limits table
     */
    private function initializeTable(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS rate_limits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key VARCHAR(255) NOT NULL,
                timestamp INTEGER NOT NULL
            )'
        );

        // Create index for faster lookups
        $this->db->exec(
            'CREATE INDEX IF NOT EXISTS idx_rate_limits_key_timestamp 
             ON rate_limits(key, timestamp)'
        );
    }

    /**
     * Count requests in current window
     */
    private function countRequests(string $key, int $windowStart): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM rate_limits 
             WHERE key = :key AND timestamp > :window_start'
        );
        $stmt->execute([
            ':key' => $key,
            ':window_start' => $windowStart,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Record a new request
     */
    private function recordRequest(string $key, int $timestamp): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rate_limits (key, timestamp) VALUES (:key, :timestamp)'
        );
        $stmt->execute([
            ':key' => $key,
            ':timestamp' => $timestamp,
        ]);
    }

    /**
     * Clean up old entries outside the window
     */
    private function cleanup(string $key, int $windowStart): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM rate_limits 
             WHERE key = :key AND timestamp <= :window_start'
        );
        $stmt->execute([
            ':key' => $key,
            ':window_start' => $windowStart,
        ]);
    }
}
