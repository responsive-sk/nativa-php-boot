<?php

declare(strict_types=1);

namespace Application\DTOs;

/**
 * DTO: Update User Command
 */
class UpdateUserCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $avatar = null,
        public readonly ?string $role = null,
        public readonly ?bool $isActive = null,
    ) {
        $this->validate();
    }

    /**
     * Create command from array
     */
    public static function fromArray(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            avatar: $data['avatar'] ?? null,
            role: $data['role'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
        );
    }

    /**
     * Check if has any changes
     */
    public function hasChanges(): bool
    {
        return $this->name !== null
            || $this->email !== null
            || $this->avatar !== null
            || $this->role !== null
            || $this->isActive !== null;
    }

    /**
     * Validate command data
     */
    private function validate(): void
    {
        $errors = [];

        if ($this->email !== null && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if ($this->role !== null && !in_array($this->role, ['admin', 'editor', 'viewer', 'user'], true)) {
            $errors[] = 'Invalid role';
        }

        if (count($errors) > 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }
    }
}
