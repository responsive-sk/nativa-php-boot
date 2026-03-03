<?php

declare(strict_types = 1);

namespace Application\DTOs;

use Application\Exceptions\ValidationException;
use Application\Validation\Validator;

/**
 * Authenticate User Command DTO.
 */
final class AuthenticateUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
        $this->validate();
    }

    /**
     * Validate the command data.
     *
     * @throws ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'email'    => $this->email,
            'password' => $this->password,
        ], [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
    }
}
