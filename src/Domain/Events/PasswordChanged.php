<?php

declare(strict_types = 1);

namespace Domain\Events;

/**
 * Domain Event: Password Changed.
 */
final class PasswordChanged implements DomainEventInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $timestamp,
    ) {}

    #[\Override]
    public function occurredAt(): string
    {
        return $this->timestamp;
    }

    #[\Override]
    public function payload(): array
    {
        return [
            'userId'    => $this->userId,
            'userEmail' => $this->userEmail,
            'timestamp' => $this->timestamp,
        ];
    }
}
