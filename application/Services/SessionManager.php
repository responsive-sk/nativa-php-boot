<?php

declare(strict_types=1);

namespace Application\Services;

/**
 * Session Manager
 *
 * Handles PHP session operations with security best practices
 */
final class SessionManager
{
    private bool $started = false;

    public function __construct()
    {
        $this->configure();
    }

    /**
     * Configure session settings
     */
    private function configure(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return;
        }

        // Use secure session configuration
        SessionSecurityConfig::configure();

        // Override with app-specific settings
        // Session name
        ini_set('session.name', $_ENV['SESSION_NAME'] ?? 'nativa_session');

        // Garbage collection - 30 minutes default
        $maxLifetime = (int) ($_ENV['AUTH_SESSION_LIFETIME'] ?? '1800');
        ini_set('session.gc_maxlifetime', (string) $maxLifetime);
    }

    /**
     * Start session if not started
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $status = session_status();
        if ($status === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
            SessionSecurityConfig::setLastActivity();

            $this->logDebug('[SessionManager] Session started: ' . session_id());
        }
    }

    /**
     * Regenerate session ID (call after login for security)
     *
     * @param bool $deleteOldSession Delete old session data
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
            SessionSecurityConfig::setLastActivity();
            
            $this->logDebug('[SessionManager] Session ID regenerated');
        }
    }

    /**
     * Check if session has timed out
     *
     * @param int $timeoutSeconds Timeout in seconds (default: 30 minutes)
     * @return bool True if session is still active
     */
    public function isActive(int $timeoutSeconds = 1800): bool
    {
        return SessionSecurityConfig::isSessionActive($timeoutSeconds);
    }

    /**
     * Update last activity timestamp
     */
    public function touch(): void
    {
        SessionSecurityConfig::setLastActivity();
    }

    /**
     * Set session value
     */
    public function set(string $key, string $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;

        $this->logDebug('[SessionManager] Set session key: ' . $key);
    }

    /**
     * Get session value
     *
     * @param null|string $default
     *
     * @psalm-param '/admin'|null $default
     */
    public function get(string $key, string|null $default = null): mixed
    {
        $this->ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public function has(string $key): bool
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     */
    public function remove(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);

        $this->logDebug('[SessionManager] Removed session key: ' . $key);
    }

    /**
     * Destroy session
     */
    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear all session data
            $_SESSION = [];

            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    '/',
                    '',
                    false,
                    true
                );
            }

            // Destroy session
            session_destroy();
            $this->started = false;

            $this->logDebug('[SessionManager] Session destroyed');
        }
    }

    /**
     * Flash message - set message for next request
     */
    public function flash(string $key, mixed $value): void
    {
        $this->set('_flash_' . $key, $value);
    }

    /**
     * Flash message - get and remove
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    /**
     * Get all session data
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $this->ensureStarted();
        return $_SESSION ?? [];
    }

    /**
     * Clear all session data
     */
    public function clear(): void
    {
        $this->ensureStarted();
        $_SESSION = [];

        $this->logDebug('[SessionManager] Session cleared');
    }

    /**
     * Get session ID
     *
     * @return false|string
     */
    public function getId(): string|false
    {
        $this->ensureStarted();
        return session_id();
    }

    /**
     * Ensure session is started
     */
    private function ensureStarted(): void
    {
        if (!$this->started) {
            $this->start();
        }
    }

    /**
     * Debug logging
     */
    private function logDebug(string $message): void
    {
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            error_log($message);
        }
    }
}
