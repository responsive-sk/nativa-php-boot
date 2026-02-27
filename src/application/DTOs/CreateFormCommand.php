<?php

declare(strict_types=1);

namespace Application\DTOs;

use Application\Validation\Validator;

/**
 * Create Form Command DTO
 */
class CreateFormCommand
{
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
     * Validate the command data
     *
     * @throws \Application\Exceptions\ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'name' => $this->name,
            'slug' => $this->slug,
            'schema' => $this->schema,
        ], [
            'name' => ['required', 'min:3', 'max:255'],
            'slug' => ['required', 'min:3', 'max:255'],
            'schema' => ['required'],
        ]);

        // Validate schema structure
        if (!is_array($this->schema) || empty($this->schema)) {
            throw new \Application\Exceptions\ValidationException(
                ['schema' => ['Form schema must be a non-empty array']],
                'Validation failed'
            );
        }

        // Validate each field in schema
        foreach ($this->schema as $index => $field) {
            if (!isset($field['type']) || !isset($field['name'])) {
                throw new \Application\Exceptions\ValidationException(
                    ['schema' => ["Field at index {$index} must have 'type' and 'name'"]],
                    'Validation failed'
                );
            }
        }
    }
}
