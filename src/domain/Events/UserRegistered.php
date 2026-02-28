<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: User Registered
 */
final class UserRegistered implements DomainEventInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $userName,
        public readonly string $role,
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
            'userName' => $this->userName,
            'role' => $this->role,
            'timestamp' => $this->timestamp,
        ];
    }
}
