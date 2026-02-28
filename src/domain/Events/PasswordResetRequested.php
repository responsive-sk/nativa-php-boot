<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: Password Reset Requested
 */
final class PasswordResetRequested implements DomainEventInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $resetToken,
        public readonly string $timestamp,
    ) {
    }

    public function occurredAt(): string
    {
        return $this->timestamp;
    }

    public function payload(): array
    {
        return [
            'userId' => $this->userId,
            'userEmail' => $this->userEmail,
            'resetToken' => $this->resetToken,
            'timestamp' => $this->timestamp,
        ];
    }
}
