<?php

declare(strict_types=1);

namespace Infrastructure\Http;

/**
 * Flash message bag
 * 
 * Messages that are available only for the next request
 */
final class FlashBag
{
    /**
     * Get flash messages for a key
     *
     * @return array<int, mixed>
     */
    public function get(string $key): array
    {
        $messages = $_SESSION['_flash'][$key] ?? [];
        unset($_SESSION['_flash'][$key]);
        return $messages;
    }

    /**
     * Set flash message
     */
    public function set(string $key, mixed $message): void
    {
        $_SESSION['_flash'][$key] = [$message];
    }

    /**
     * Check if flash has messages for a key
     */
    public function has(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]) && count($_SESSION['_flash'][$key]) > 0;
    }

    /**
     * Get all flash messages
     *
     * @return array<string, array<int, mixed>>
     */
    public function all(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $messages;
    }
}
