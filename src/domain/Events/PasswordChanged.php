<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: Password Changed
 */
class PasswordChanged
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $timestamp,
    ) {
    }

    public static function create(
        string $userId,
        string $userEmail,
    ): self {
        return new self(
            $userId,
            $userEmail,
            date('Y-m-d H:i:s')
        );
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'timestamp' => $this->timestamp,
        ];
    }
}
