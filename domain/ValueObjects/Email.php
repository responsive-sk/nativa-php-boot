<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Email Value Object
 */
final class Email
{
    private function __construct(
        private readonly string $value
    ) {
    }

    /**
     * Create from string with validation
     */
    public static function fromString(string $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address: ' . $value);
        }
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
