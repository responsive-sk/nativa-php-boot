<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

use Infrastructure\Paths\PresetInterface;

/**
 * Lightweight Mezzio preset
 *
 * Memory-efficient Mezzio paths configuration.
 * Uses var/ directory by default for better practices.
 */
class MezzioPresetLite implements PresetInterface
{
    private string $basePath;
    
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }
    
    /**
     * Get Mezzio-specific paths
     * 
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return [
            // Core Mezzio directories
            'config' => $this->buildPath('config'),
            'src' => $this->buildPath('src'),
            'public' => $this->buildPath('public'),
            'vendor' => $this->buildPath('vendor'),
            
            // Module directories
            'modules' => $this->buildPath('modules'),
            
            // Template directories - use namespace names that match config
            'templates' => $this->buildPath('templates'),
            'layout' => $this->buildPath('src/App/templates/layout'),
            'app' => $this->buildPath('src/App/templates/app'),
            'error' => $this->buildPath('src/App/templates/error'),
            'page' => $this->buildPath('src/Page/templates/page'),
            'partial' => $this->buildPath('src/App/templates/partial'),
            
            // Data directories - use var/ for best practices
            'var' => $this->buildPath('var'),
            'data' => $this->buildPath('var/data'),
            'cache' => $this->buildPath('var/cache'),
            'logs' => $this->buildPath('var/logs'),
            'tmp' => $this->buildPath('var/tmp'),
            'storage' => $this->buildPath('var/storage'),
            'uploads' => $this->buildPath('var/uploads'),
            'database' => $this->buildPath('var/database'),
            
            // Configuration cache
            'config_cache' => $this->buildPath('var/cache/config'),
            'route_cache' => $this->buildPath('var/cache/routes'),
            
            // Test directories
            'test' => $this->buildPath('test'),
            'tests' => $this->buildPath('test'),
            
            // Documentation
            'docs' => $this->buildPath('docs'),
            
            // Binary files
            'bin' => $this->buildPath('bin'),
        ];
    }
    
    /**
     * Build path from relative path
     * 
     * @param string $relativePath
     * @return string
     */
    private function buildPath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/\\');
    }
}
