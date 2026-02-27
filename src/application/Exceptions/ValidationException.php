<?php

declare(strict_types=1);

namespace Application\Exceptions;

/**
 * Validation Exception
 */
class ValidationException extends \InvalidArgumentException
{
    /**
     * @param array<string, string[]> $errors
     */
    public function __construct(
        public readonly array $errors,
        string $message = 'Validation failed'
    ) {
        parent::__construct($message);
    }

    /**
     * Get all validation errors
     *
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message for a field
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Check if field has errors
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && count($this->errors[$field]) > 0;
    }
}
