<?php

declare(strict_types=1);

namespace Application\DTOs;

use Application\Validation\Validator;

/**
 * Create User Command DTO
 */
class CreateUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $role = 'user',
    ) {
        $this->validate();
    }

    /**
     * Validate the command data
     *
     * @throws \Application\Exceptions\ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ], [
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'max:255'],
            'role' => ['required', 'max:50'],
        ]);
    }
}
