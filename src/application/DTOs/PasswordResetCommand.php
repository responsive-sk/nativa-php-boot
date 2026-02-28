<?php

declare(strict_types=1);

namespace Application\DTOs;

/**
 * DTO: Password Reset Command
 */
class PasswordResetCommand
{
    public function __construct(
        public readonly string $token,
        public readonly string $newPassword,
    ) {
        $this->validate();
    }

    /**
     * Create command from array
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'] ?? '',
            newPassword: $data['password'] ?? '',
        );
    }

    /**
     * Validate command data
     */
    private function validate(): void
    {
        $errors = [];

        if ($this->token === '') {
            $errors[] = 'Reset token is required';
        }

        if (strlen($this->newPassword) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (count($errors) > 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }
    }
}
