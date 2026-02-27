<?php

declare(strict_types=1);

namespace Application\Validation;

/**
 * Validator - Static validation methods
 */
class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, array<string>> $rules
     * @throws \Application\Exceptions\ValidationException
     */
    public static function validate(array $data, array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;

            foreach ($ruleSet as $rule) {
                $error = self::applyRule($field, $value, $rule);

                if ($error !== null) {
                    if (!isset($errors[$field])) {
                        $errors[$field] = [];
                    }
                    $errors[$field][] = $error;
                }
            }
        }

        if (!empty($errors)) {
            throw new \Application\Exceptions\ValidationException($errors);
        }
    }

    /**
     * Apply single validation rule
     */
    private static function applyRule(string $field, mixed $value, string $rule): ?string
    {
        // Parse rule: "required", "min:3", "max:255", "email", "uuid", etc.
        [$ruleName, $params] = self::parseRule($rule);

        return match ($ruleName) {
            'required' => self::required($field, $value),
            'min' => self::min($field, $value, (int) $params[0]),
            'max' => self::max($field, $value, (int) $params[0]),
            'email' => self::email($field, $value),
            'uuid' => self::uuid($field, $value),
            'alpha' => self::alpha($field, $value),
            'numeric' => self::numeric($field, $value),
            'url' => self::url($field, $value),
            default => null,
        };
    }

    /**
     * Parse rule string into name and parameters
     *
     * @return array{0: string, 1: array<string>}
     */
    private static function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $paramString] = explode(':', $rule, 2);
            return [$name, explode(',', $paramString)];
        }

        return [$rule, []];
    }

    /**
     * Validation: Required
     */
    private static function required(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return ucfirst($field) . ' is required';
        }
        return null;
    }

    /**
     * Validation: Minimum length
     */
    private static function min(string $field, mixed $value, int $min): ?string
    {
        if ($value !== null && is_string($value) && strlen($value) < $min) {
            return ucfirst($field) . " must be at least {$min} characters";
        }
        return null;
    }

    /**
     * Validation: Maximum length
     */
    private static function max(string $field, mixed $value, int $max): ?string
    {
        if ($value !== null && is_string($value) && strlen($value) > $max) {
            return ucfirst($field) . " must not exceed {$max} characters";
        }
        return null;
    }

    /**
     * Validation: Email format
     */
    private static function email(string $field, mixed $value): ?string
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ucfirst($field) . ' must be a valid email address';
        }
        return null;
    }

    /**
     * Validation: UUID format
     */
    private static function uuid(string $field, mixed $value): ?string
    {
        if ($value !== null && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            return ucfirst($field) . ' must be a valid UUID';
        }
        return null;
    }

    /**
     * Validation: Alphabetic characters only
     */
    private static function alpha(string $field, mixed $value): ?string
    {
        if ($value !== null && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
            return ucfirst($field) . ' may only contain letters';
        }
        return null;
    }

    /**
     * Validation: Numeric
     */
    private static function numeric(string $field, mixed $value): ?string
    {
        if ($value !== null && !is_numeric($value)) {
            return ucfirst($field) . ' must be numeric';
        }
        return null;
    }

    /**
     * Validation: URL format
     */
    private static function url(string $field, mixed $value): ?string
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_URL)) {
            return ucfirst($field) . ' must be a valid URL';
        }
        return null;
    }
}
