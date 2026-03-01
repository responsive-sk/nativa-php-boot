<?php

declare(strict_types=1);

namespace Infrastructure\Paths;

/**
 * AppPaths - Application-specific paths helper
 * Extends Paths with project-specific path methods
 */
class AppPaths extends Paths
{
    private static ?self $instance = null;

    /**
     * Get singleton instance using Paths::fromHere()
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            // Go up 3 levels from src/infrastructure/Paths to project root
            self::$instance = new self(dirname(__DIR__, 3));
        }
        return self::$instance;
    }

    /**
     * Reset singleton instance (for testing)
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    // Override parent constructor to ensure singleton
    public function __construct(string $basePath = '', array $customPaths = [])
    {
        parent::__construct($basePath, $customPaths);
    }

    // Project-specific path methods

    public function domain(): string
    {
        return $this->basePath . '/domain';
    }

    public function application(): string
    {
        return $this->basePath . '/application';
    }

    public function infrastructure(): string
    {
        return $this->basePath . '/infrastructure';
    }

    public function interfaces(): string
    {
        return $this->basePath . '/interfaces';
    }

    public function storage(string $path = ''): string
    {
        $base = $this->basePath . '/storage';
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
            $base = $this->basePath . '/src/interfaces/Templates/admin';
            $subPath = substr($path, 6); // Remove 'admin/' prefix
            return $subPath ? $base . '/' . ltrim($subPath, '/') : $base;
        }
        
        // Frontend templates (default)
        $base = $this->basePath . '/src/interfaces/Templates/frontend';
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
