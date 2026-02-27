<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Base Domain Event
 */
abstract class DomainEvent implements DomainEventInterface
{
    private string $occurredAt;

    public function __construct()
    {
        $this->occurredAt = $this->now();
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }

    abstract public function payload(): array;

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
