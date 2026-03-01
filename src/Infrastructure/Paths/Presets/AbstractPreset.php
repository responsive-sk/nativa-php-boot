<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Abstract base class for framework presets
 */
abstract class AbstractPreset implements PresetInterface
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    /**
     * Get base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Build full path from relative path
     */
    protected function buildPath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/\\');
    }

    /**
     * Get common paths that most frameworks share
     * 
     * @return array<string, string>
     */
    protected function getCommonPaths(): array
    {
        return [
            'base' => $this->basePath,
            'vendor' => $this->buildPath('vendor'),
            'public' => $this->buildPath('public'),
            'tests' => $this->buildPath('tests'),
            'docs' => $this->buildPath('docs'),
        ];
    }

    /**
     * Get default helper methods
     * 
     * @return array<string, string>
     */
    public function getHelperMethods(): array
    {
        return [
            'base' => 'Get base application path',
            'public' => 'Get public web directory path',
            'config' => 'Get configuration directory path',
            'storage' => 'Get storage directory path',
            'logs' => 'Get logs directory path',
            'cache' => 'Get cache directory path',
        ];
    }
}
