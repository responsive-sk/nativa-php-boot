<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Events\DomainEventInterface;
use Domain\Events\EventDispatcherInterface;

/**
 * Event Dispatcher Implementation
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var array<string, array<callable>> */
    private array $listeners = [];

    public function addListener(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }

        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(DomainEventInterface $event): void
    {
        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return;
        }

        foreach ($this->listeners[$eventClass] as $listener) {
            try {
                $listener($event);
            } catch (\Throwable $e) {
                error_log(sprintf(
                    'Event listener error for %s: %s',
                    $eventClass,
                    $e->getMessage()
                ));
            }
        }
    }

    public function getListeners(string $eventClass): array
    {
        return $this->listeners[$eventClass] ?? [];
    }

    public function hasListeners(string $eventClass): bool
    {
        return isset($this->listeners[$eventClass]) && count($this->listeners[$eventClass]) > 0;
    }
}
