<?php

declare(strict_types=1);

namespace Infrastructure\Http;

/**
 * Simplified Session class
 * 
 * Replaces Symfony\Component\HttpFoundation\Session\Session
 */
final class Session
{
    /**
     * Start session if not started
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get session value
     *
     * @param mixed $default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session has key
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Remove session value
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get flash bag
     */
    public function getFlashBag(): FlashBag
    {
        return new FlashBag();
    }

    /**
     * Destroy session
     */
    public function invalidate(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
