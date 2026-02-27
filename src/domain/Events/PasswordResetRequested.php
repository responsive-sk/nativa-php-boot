<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Domain Event: Password Reset Requested
 */
class PasswordResetRequested
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly string $resetToken,
        public readonly string $timestamp,
    ) {
    }

    public static function create(
        string $userId,
        string $userEmail,
        string $resetToken,
    ): self {
        return new self(
            $userId,
            $userEmail,
            $resetToken,
            date('Y-m-d H:i:s')
        );
    }

    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'reset_token' => $this->resetToken,
            'timestamp' => $this->timestamp,
        ];
    }
}
