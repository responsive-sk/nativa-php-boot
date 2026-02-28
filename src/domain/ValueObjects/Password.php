<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Password Value Object
 *
 * Handles password hashing and validation
 */
final class Password
{
    private string $hashedPassword;

    private function __construct(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * Create from plain text password (automatically hashes)
     */
    public static function fromPlain(string $plainPassword): self
    {
        self::validate($plainPassword);
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
        return new self($hashed);
    }

    /**
     * Create from already hashed password
     */
    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    /**
     * Verify plain password against stored hash
     */
    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedPassword);
    }

    /**
     * Check if hash needs rehash (algorithm updated)
     */
    public function needsRehash(): bool
    {
        return password_needs_rehash($this->hashedPassword, PASSWORD_DEFAULT);
    }

    /**
     * Get the hashed password
     */
    public function hash(): string
    {
        return $this->hashedPassword;
    }

    /**
     * Validate password requirements
     * - Minimum 8 characters
     * - At least one uppercase letter
     * - At least one lowercase letter
     * - At least one number
     */
    private static function validate(string $password): void
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (count($errors) > 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }
    }
}
