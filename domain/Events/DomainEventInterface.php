<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event Interface
 */
interface DomainEventInterface
{
    /**
     * Get event occurred at timestamp
     */
    public function occurredAt(): string;

    /**
     * Get event payload as array
     *
     * @return array<string, mixed>
     */
    public function payload(): array;
}
