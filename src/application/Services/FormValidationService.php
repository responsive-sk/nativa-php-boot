<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\ValueObjects\Email;

/**
 * Form Validation Service
 *
 * Validates and sanitizes form input data based on field schema.
 * Prevents XSS, injection attacks, and ensures data integrity.
 *
 * Usage:
 *   $validator = new FormValidationService();
 *   $result = $validator->validate($formData, $formSchema);
 *   if ($result->fails()) { /* handle errors *\/ }
 */
class FormValidationService
{
    /** @var array<string> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $sanitizedData = [];

    /**
     * Validate form data against schema
     *
     * @param array<string, mixed> $data Form data
     * @param array<array<string, mixed>> $schema Form schema with field definitions
     * @return bool True if validation passes
     */
    public function validate(array $data, array $schema): bool
    {
        $this->errors = [];
        $this->sanitizedData = [];

        foreach ($schema as $field) {
            $name = $field['name'] ?? null;
            if ($name === null) {
                continue;
            }

            $value = $data[$name] ?? null;
            $type = $field['type'] ?? 'text';
            $required = $field['required'] ?? false;
            $label = $field['label'] ?? ucfirst($name);

            // Validate required fields
            if ($required && $this->isEmpty($value)) {
                $this->addError($name, "{$label} is required");
                continue;
            }

            // Skip further validation if empty and not required
            if ($this->isEmpty($value)) {
                continue;
            }

            // Validate and sanitize based on type
            $sanitizedValue = $this->sanitizeByType($value, $type, $field);

            if ($sanitizedValue === null && $this->hasError($name)) {
                continue;
            }

            $this->sanitizedData[$name] = $sanitizedValue;
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     *
     * @return array<string, string[]> Errors indexed by field name
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get sanitized data
     *
     * @return array<string, mixed>
     */
    public function getSanitizedData(): array
    {
        return $this->sanitizedData;
    }

    /**
     * Check if validation has errors
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Sanitize value based on field type
     *
     * @param mixed $value Raw value
     * @param string $type Field type
     * @param array<string, mixed> $field Field schema
     * @return mixed Sanitized value or null on error
     */
    private function sanitizeByType(mixed $value, string $type, array $field): mixed
    {
        return match ($type) {
            'email' => $this->sanitizeEmail($value, $field),
            'text', 'textarea' => $this->sanitizeText($value, $field),
            'number', 'tel' => $this->sanitizeNumber($value, $field),
            'url' => $this->sanitizeUrl($value, $field),
            'date' => $this->sanitizeDate($value, $field),
            'checkbox', 'radio' => $this->sanitizeBoolean($value),
            'select' => $this->sanitizeSelect($value, $field),
            default => $this->sanitizeText($value, $field),
        };
    }

    /**
     * Sanitize email
     */
    private function sanitizeEmail(mixed $value, array $field): ?string
    {
        $value = trim((string) $value);

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field['name'], 'Invalid email format');
            return null;
        }

        // Additional validation using Email VO
        try {
            Email::fromString($value);
        } catch (\InvalidArgumentException $e) {
            $this->addError($field['name'], 'Invalid email format');
            return null;
        }

        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize text
     */
    private function sanitizeText(mixed $value, array $field): string
    {
        $value = trim((string) $value);

        // Apply max length
        $maxLength = (int) ($field['max_length'] ?? $field['maxlength'] ?? 1000);
        if (strlen($value) > $maxLength) {
            $this->addError($field['name'], "{$field['label']} must be less than {$maxLength} characters");
        }

        // Sanitize based on allow_html flag
        if (($field['allow_html'] ?? false) === true) {
            // Allow limited HTML - use HTMLPurifier if available, otherwise strip tags
            $value = strip_tags($value, '<p><br><strong><em><u>');
        } else {
            // Strip all HTML tags
            $value = strip_tags($value);
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize number
     *
     * @return int|float|null
     */
    private function sanitizeNumber(mixed $value, array $field): int|float|null
    {
        $name = $field['name'];
        $label = $field['label'] ?? ucfirst($name);
        $type = $field['type'] ?? 'number';

        if (!is_numeric($value)) {
            $this->addError($name, "{$label} must be a number");
            return null;
        }

        $numericValue = $type === 'tel' ? (int) $value : (float) $value;

        // Validate min
        if (isset($field['min']) && $numericValue < (float) $field['min']) {
            $this->addError($name, "{$label} must be at least {$field['min']}");
        }

        // Validate max
        if (isset($field['max']) && $numericValue > (float) $field['max']) {
            $this->addError($name, "{$label} must be at most {$field['max']}");
        }

        return $type === 'tel' ? (int) $value : (float) $value;
    }

    /**
     * Sanitize URL
     */
    private function sanitizeUrl(mixed $value, array $field): ?string
    {
        $value = trim((string) $value);

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field['name'], 'Invalid URL format');
            return null;
        }

        return filter_var($value, FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize date
     */
    private function sanitizeDate(mixed $value, array $field): ?string
    {
        $value = trim((string) $value);

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            $this->addError($field['name'], 'Invalid date format');
            return null;
        }

        // Validate min date
        if (isset($field['min']) && $timestamp < strtotime($field['min'])) {
            $this->addError($field['name'], "Date must be after {$field['min']}");
        }

        // Validate max date
        if (isset($field['max']) && $timestamp > strtotime($field['max'])) {
            $this->addError($field['name'], "Date must be before {$field['max']}");
        }

        return date('Y-m-d', $timestamp);
    }

    /**
     * Sanitize boolean (checkbox)
     */
    private function sanitizeBoolean(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * Sanitize select value
     */
    private function sanitizeSelect(mixed $value, array $field): ?string
    {
        $value = trim((string) $value);

        // Validate against options if provided
        if (isset($field['options']) && is_array($field['options'])) {
            if (!in_array($value, $field['options'], true)) {
                $this->addError($field['name'], 'Invalid selection');
                return null;
            }
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Check if value is empty
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }

    /**
     * Add validation error
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Check if field has errors
     */
    private function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}
