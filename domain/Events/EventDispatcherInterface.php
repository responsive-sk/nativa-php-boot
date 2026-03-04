<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Event Dispatcher Interface
 */
interface EventDispatcherInterface
{
    /**
     * Add event listener
     *
     * @param string $eventClass
     * @param callable $listener
     */
    public function addListener(string $eventClass, callable $listener): void;

    /**
     * Dispatch event to all listeners
     *
     * @param DomainEventInterface $event
     */
    public function dispatch(DomainEventInterface $event): void;

    /**
     * Get all listeners for an event class
     *
     * @param string $eventClass
     * @return array<callable>
     */
    public function getListeners(string $eventClass): array;

    /**
     * Check if has listeners for an event class
     *
     * @param string $eventClass
     * @return bool
     */
    public function hasListeners(string $eventClass): bool;
}
