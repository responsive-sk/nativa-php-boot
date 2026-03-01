<?php

declare(strict_types=1);

namespace Infrastructure;

/**
 * Simple .env file loader
 * 
 * Replaces vlucas/phpdotenv
 * Supports: key=value, #comments, "quoted values", 'single quoted'
 */
final class Env
{
    /**
     * Load .env file and populate $_ENV
     */
    public static function load(string $path): void
    {
        $files = [
            $path . '/.env',
            $path . '/.env.local',
            $path . '/.env.' . ($_ENV['APP_ENV'] ?? 'development'),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                self::parseFile($file);
            }
        }
    }

    /**
     * Load .env file immutably (only if not already set)
     */
    public static function loadImmutable(string $path): void
    {
        $files = [
            $path . '/.env',
            $path . '/.env.local',
            $path . '/.env.' . ($_ENV['APP_ENV'] ?? 'development'),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                self::parseFile($file, true);
            }
        }
    }

    /**
     * Parse .env file
     */
    private static function parseFile(string $path, bool $immutable = false): void
    {
        $content = file_get_contents($path);
        if ($content === false) {
            return;
        }

        $lines = explode(PHP_EOL, $content);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Parse key=value
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes
                $value = self::unquote($value);

                // Set environment variable
                if ($immutable) {
                    if (!array_key_exists($key, $_ENV)) {
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                } else {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    /**
     * Remove quotes from value
     */
    private static function unquote(string $value): string
    {
        // Remove surrounding quotes
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        // Handle escaped characters
        $value = str_replace(['\\"', "\\'"], ['"', "'"], $value);

        return $value;
    }

    /**
     * Get environment variable
     *
     * @param mixed $default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Check if environment variable exists
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, $_ENV) || array_key_exists($key, $_SERVER);
    }
}
