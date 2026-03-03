<?php

declare(strict_types = 1);

namespace Domain\Events;

/**
 * Event Dispatcher Interface.
 */
interface EventDispatcherInterface
{
    /**
     * Add event listener.
     */
    public function addListener(string $eventClass, callable $listener): void;

    /**
     * Dispatch event to all listeners.
     */
    public function dispatch(DomainEventInterface $event): void;

    /**
     * Get all listeners for an event class.
     *
     * @return array<callable>
     */
    public function getListeners(string $eventClass): array;

    /**
     * Check if has listeners for an event class.
     */
    public function hasListeners(string $eventClass): bool;
}
