<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: User Registered
 */
class UserRegistered
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $userName,
        public readonly string $role,
        public readonly string $timestamp,
    ) {
    }

    public static function create(
        string $userId,
        string $userEmail,
        string $userName,
        string $role,
    ): self {
        return new self(
            $userId,
            $userEmail,
            $userName,
            $role,
            date('Y-m-d H:i:s')
        );
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'user_name' => $this->userName,
            'role' => $this->role,
            'timestamp' => $this->timestamp,
        ];
    }
}
