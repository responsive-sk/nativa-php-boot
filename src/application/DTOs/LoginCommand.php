<?php

declare(strict_types=1);

namespace Application\DTOs;

/**
 * DTO: Login Command
 */
class LoginCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $rememberMe = false,
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
            email: self::getString($data, 'email'),
            password: self::getString($data, 'password'),
            rememberMe: (bool) ($data['remember_me'] ?? false),
        );
    }

    /**
     * Get string value from array
     *
     * @param array<string, mixed> $data
     */
    private static function getString(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? $default;
        return is_string($value) ? $value : $default;
    }

    /**
     * Validate command data
     */
    private function validate(): void
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if ($this->password === '') {
            $errors[] = 'Password is required';
        }

        if (count($errors) > 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }
    }
}
