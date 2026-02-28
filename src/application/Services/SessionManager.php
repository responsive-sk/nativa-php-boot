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

        // Security settings
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        // Cookie settings
        $cookieParams = [
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => ($_ENV['SESSION_SECURE_COOKIE'] ?? 'false') === 'true',
            'httponly' => true,
            'samesite' => ($_ENV['SESSION_SAME_SITE'] ?? 'Lax'),
        ];

        session_set_cookie_params($cookieParams);

        // Session name
        ini_set('session.name', $_ENV['SESSION_NAME'] ?? 'nativa_session');

        // Garbage collection
        ini_set('session.gc_maxlifetime', (string) ($_ENV['AUTH_SESSION_LIFETIME'] ?? '7200'));
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

            $this->logDebug('[SessionManager] Session started: ' . session_id());
        }
    }

    /**
     * Set session value
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;

        $this->logDebug('[SessionManager] Set session key: ' . $key);
    }

    /**
     * Get session value
     */
    public function get(string $key, mixed $default = null): mixed
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
     * Regenerate session ID (prevents session fixation)
     */
    public function regenerate(): void
    {
        $this->ensureStarted();
        session_regenerate_id(true);

        $this->logDebug('[SessionManager] Session ID regenerated');
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
     */
    public function getId(): string
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
