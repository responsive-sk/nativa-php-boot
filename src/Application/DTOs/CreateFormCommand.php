<?php

declare(strict_types = 1);

namespace Application\DTOs;

use Application\Exceptions\ValidationException;
use Application\Validation\Validator;

/**
 * Create Form Command DTO.
 */
final class CreateFormCommand
{
    /**
     * @param array<string, mixed> $schema
     */
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly array $schema,
        public readonly ?string $emailNotification = null,
        public readonly ?string $successMessage = null,
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
            'name'   => $this->name,
            'slug'   => $this->slug,
            'schema' => $this->schema,
        ], [
            'name'   => ['required', 'min:3', 'max:255'],
            'slug'   => ['required', 'min:3', 'max:255'],
            'schema' => ['required'],
        ]);

        // Validate schema structure
        /** @var array<string, mixed> $schema */
        $schema = $this->schema;
        if (empty($schema)) {
            throw new ValidationException(
                ['schema' => ['Form schema must be a non-empty array']],
                'Validation failed'
            );
        }

        // Validate each field in schema
        foreach ($this->schema as $index => $field) {
            /** @var array<string, mixed> $field */
            if (!isset($field['type']) || !isset($field['name'])) {
                throw new ValidationException(
                    ['schema' => ["Field at index {$index} must have 'type' and 'name'"]],
                    'Validation failed'
                );
            }
        }

        // Remove redundant is_array check - $schema is already typed as array
    }
}
