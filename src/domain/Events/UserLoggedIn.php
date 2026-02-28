<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: User Logged In
 */
final class UserLoggedIn implements DomainEventInterface
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $ipAddress,
        public readonly string $timestamp,
    ) {
    }

    public static function create(
        string $userId,
        string $userEmail,
        string $ipAddress,
    ): self {
        return new self(
            $userId,
            $userEmail,
            $ipAddress,
            date('Y-m-d H:i:s')
        );
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'ip_address' => $this->ipAddress,
            'timestamp' => $this->timestamp,
        ];
    }

    public function occurredAt(): string
    {
        return $this->timestamp;
    }
}
