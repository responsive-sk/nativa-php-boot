<?php

declare(strict_types=1);

namespace Infrastructure\Events;

use Domain\Events\DomainEventInterface;
use Domain\Events\EventDispatcherInterface;

/**
 * Event Dispatcher Implementation
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var array<string, array<callable>> */
    private array $listeners = [];

    /**
     * Add event listener
     */
    public function addListener(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
        $this->logDebug("[EventDispatcher] Listener registered for: {$eventClass}");
    }

    /**
     * Dispatch event to all listeners
     */
    public function dispatch(DomainEventInterface $event): void
    {
        $eventClass = get_class($event);
        $this->logDebug("[EventDispatcher] Dispatching: {$eventClass}");

        if (!isset($this->listeners[$eventClass])) {
            $this->logDebug("[EventDispatcher] No listeners for: {$eventClass}");
            return;
        }

        foreach ($this->listeners[$eventClass] as $listener) {
            try {
                $listener($event);
                $this->logDebug("[EventDispatcher] Listener executed for: {$eventClass}");
            } catch (\Throwable $e) {
                $this->logError("[EventDispatcher] Listener failed for {$eventClass}: " . $e->getMessage());
            }
        }
    }

    /**
     * Get all listeners for an event class
     */
    public function getListeners(string $eventClass): array
    {
        return $this->listeners[$eventClass] ?? [];
    }

    /**
     * Check if has listeners for an event class
     */
    public function hasListeners(string $eventClass): bool
    {
        return isset($this->listeners[$eventClass]) && count($this->listeners[$eventClass]) > 0;
    }

    /**
     * Clear all listeners (useful for testing)
     */
    public function clearListeners(): void
    {
        $this->listeners = [];
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

    /**
     * Error logging
     */
    private function logError(string $message): void
    {
        error_log('[EventDispatcher ERROR] ' . $message);
    }
}
