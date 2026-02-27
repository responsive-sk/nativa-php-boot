<?php

declare(strict_types=1);

namespace Application\DTOs;

use Domain\ValueObjects\Email;
use Domain\ValueObjects\Password;
use Domain\ValueObjects\Role as RoleVO;

/**
 * DTO: Create User Command
 */
class CreateUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role = 'user',
        public readonly ?string $avatar = null,
    ) {
        $this->validate();
    }

    /**
     * Create command from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role: $data['role'] ?? 'user',
            avatar: $data['avatar'] ?? null,
        );
    }

    /**
     * Convert to value objects
     */
    public function toValueObjects(): array
    {
        return [
            'email' => Email::fromString($this->email),
            'password' => Password::fromPlain($this->password),
            'role' => RoleVO::fromString($this->role),
        ];
    }

    /**
     * Validate command data
     */
    private function validate(): void
    {
        $errors = [];

        if (trim($this->name) === '') {
            $errors[] = 'Name is required';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (strlen($this->password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (!in_array($this->role, ['admin', 'editor', 'viewer', 'user'], true)) {
            $errors[] = 'Invalid role';
        }

        if (count($errors) > 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }
    }
}
