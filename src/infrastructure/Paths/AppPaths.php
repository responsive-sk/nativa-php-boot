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
            // Go up 2 levels from Infrastructure/Paths to project root
            $paths = Paths::fromHere(__DIR__, 2);
            self::$instance = new self($paths->getBasePath());
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

    public function data(string $path = ''): string
    {
        $base = $this->basePath . '/data';
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function storage(string $path = ''): string
    {
        $base = $this->basePath . '/storage';
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function templates(string $path = ''): string
    {
        $base = $this->basePath . '/src/interfaces/Templates';
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
