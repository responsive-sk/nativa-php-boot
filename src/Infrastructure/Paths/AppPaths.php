<?php

declare(strict_types = 1);

namespace Infrastructure\Paths;

/**
 * AppPaths - Application-specific paths helper
 * Uses composition instead of inheritance since Paths is final.
 */
final class AppPaths
{
    private Paths $paths;

    private static ?self $instance = null;

    public function __construct(string $basePath = '', array $customPaths = [])
    {
        $this->paths = new Paths($basePath, $customPaths);
    }

    /**
     * Get base path.
     */
    public function getBasePath(): string
    {
        return $this->paths->getBasePath();
    }

    /**
     * Get singleton instance using Paths::fromHere().
     */
    public static function instance(): self
    {
        if (null === self::$instance) {
            // Go up 3 levels from src/infrastructure/Paths to project root
            self::$instance = new self(\dirname(__DIR__, 3));
        }

        return self::$instance;
    }

    /**
     * Reset singleton instance (for testing).
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    // Project-specific path methods

    public function domain(): string
    {
        return $this->paths->getBasePath() . '/domain';
    }

    public function application(): string
    {
        return $this->paths->getBasePath() . '/application';
    }

    public function infrastructure(): string
    {
        return $this->paths->getBasePath() . '/infrastructure';
    }

    public function interfaces(): string
    {
        return $this->paths->getBasePath() . '/interfaces';
    }

    public function storage(string $path = ''): string
    {
        $base = $this->paths->getBasePath() . '/storage';

        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function data(string $path = ''): string
    {
        // Data directory for SQLite databases (under storage/)
        return $this->storage('data') . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function templates(string $path = ''): string
    {
        // Check if it's an admin template (with or without 'admin/' prefix)
        if (str_starts_with($path, 'admin/') || str_starts_with($path, 'admin\\')) {
            $base = $this->paths->getBasePath() . '/src/Interfaces/Templates/admin';
            $subPath = substr($path, 6); // Remove 'admin/' prefix

            return $subPath ? $base . '/' . ltrim($subPath, '/') : $base;
        }

        // Frontend templates (default)
        $base = $this->paths->getBasePath() . '/src/Interfaces/Templates/frontend';

        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function cache(string $path = ''): string
    {
        return $this->storage('cache') . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function logs(string $file = ''): string
    {
        return $this->storage('logs') . ($file ? '/' . ltrim($file, '/') : '');
    }
}
