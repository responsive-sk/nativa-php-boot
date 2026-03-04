<?php

declare(strict_types=1);

namespace Infrastructure\Paths;

use RuntimeException;

use function array_merge;
use function ltrim;
use function preg_match;
use function realpath;
use function rtrim;
use function str_contains;
use function str_replace;
use function strpos;
use function urldecode;

/**
 * Lightweight Paths management - Version 6.0
 * 
 * Memory-efficient implementation with minimal footprint.
 * Breaking change: Simplified API, removed heavy features.
 * Default paths use var/ directory for best practices.
 *
 * @package ResponsiveSk\Slim4Paths
 * @author  ResponsiveSk
 * @license MIT
 * @version 6.0.0
 */
class Paths
{
    protected string $basePath;

    /** @var array<string, string> */
    protected array $paths;

    protected ?string $preset = null;
    protected bool $presetLoaded = false;

    /**
     * Constructor - lightweight initialization
     * 
     * @param string $basePath Application base path
     * @param array<string, string> $customPaths Optional custom paths
     * @param string|null $preset Optional preset name for lazy loading
     */
    public function __construct(string $basePath, array $customPaths = [], ?string $preset = null)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->preset = $preset;
        $this->initializePaths($customPaths);
    }

    /**
     * Get path by name with lazy preset loading
     * 
     * @param string $name Path name
     * @param string $fallback Fallback value if path not found
     * @return string Full path
     */
    public function getPath(string $name, string $fallback = ''): string
    {
        // Load preset on first access if needed
        if ($this->preset && !$this->presetLoaded) {
            $this->loadPreset();
        }

        return $this->paths[$name] ?? $fallback;
    }

    /**
     * Get all paths with lazy preset loading
     * 
     * @return array<string, string>
     */
    public function all(): array
    {
        // Load preset on first access if needed
        if ($this->preset && !$this->presetLoaded) {
            $this->loadPreset();
        }

        return $this->paths;
    }

    /**
     * Check if path exists
     * 
     * @param string $name Path name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->paths[$name]);
    }

    /**
     * Set custom path with security validation
     * 
     * @param string $name Path name
     * @param string $path Path value
     * @return void
     * @throws RuntimeException If path contains dangerous patterns
     */
    public function set(string $name, string $path): void
    {
        // Sanitize path for security
        $sanitizedPath = $this->sanitizePath($path);
        $this->paths[$name] = $sanitizedPath;
    }

    /**
     * Get base path
     * 
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Create instance from current file location
     * Useful for modular systems
     *
     * @param string $currentFile Current file path (__DIR__ or __FILE__)
     * @param int $levelsUp Number of directory levels to go up
     * @return self
     */
    public static function fromHere(string $currentFile, int $levelsUp = 1): self
    {
        // Start from directory of current file
        $currentDir = is_file($currentFile) ? dirname($currentFile) : $currentFile;
        
        // Try to find project root by looking for common files
        $root = $currentDir;
        for ($i = 0; $i < $levelsUp + 5; $i++) { // Search up to 5 levels beyond requested
            if (file_exists($root . '/composer.json') || file_exists($root . '/.env')) {
                return new self($root);
            }
            $parent = dirname($root);
            if ($parent === $root) {
                break;
            }
            $root = $parent;
        }
        
        // Fallback: use directory levels
        $baseDir = $currentDir;
        for ($i = 0; $i < $levelsUp; $i++) {
            $baseDir = dirname($baseDir);
        }
        
        return new self($baseDir);
    }

    /**
     * Create instance with preset (factory method)
     *
     * @param string $preset Preset name
     * @param string $basePath Base path
     * @param array<string, string> $customPaths Custom paths
     * @return self
     */
    public static function withPreset(string $preset, string $basePath, array $customPaths = []): self
    {
        return new self($basePath, $customPaths, $preset);
    }

    /**
     * Create lightweight instance without preset
     * 
     * @param string $basePath Base path
     * @param array<string, string> $customPaths Custom paths
     * @return self
     */
    public static function create(string $basePath, array $customPaths = []): self
    {
        return new self($basePath, $customPaths);
    }

    /**
     * Build absolute path from relative path
     * 
     * @param string $relativePath Relative path
     * @return string Absolute path
     */
    public function buildPath(string $relativePath): string
    {
        return $this->basePath . '/' . ltrim($relativePath, '/\\');
    }

    /**
     * Initialize minimal default paths
     * 
     * @param array<string, string> $customPaths
     */
    private function initializePaths(array $customPaths): void
    {
        // Minimal default paths - memory efficient, use var/ by default
        $defaultPaths = [
            // Core directories
            'base' => $this->basePath,
            'config' => $this->basePath . '/config',
            'src' => $this->basePath . '/src',
            'public' => $this->basePath . '/public',
            'vendor' => $this->basePath . '/vendor',

            // Runtime directories - use var by default (best practice)
            'var' => $this->basePath . '/var',
            'data' => $this->basePath . '/var/data',
            'cache' => $this->basePath . '/var/cache',
            'logs' => $this->basePath . '/var/logs',
            'tmp' => $this->basePath . '/var/tmp',

            // Template directories
            'templates' => $this->basePath . '/templates',
        ];

        // Merge with custom paths (custom paths have priority)
        $this->paths = array_merge($defaultPaths, $customPaths);
    }

    /**
     * Lazy load preset
     */
    private function loadPreset(): void
    {
        if (!$this->preset || $this->presetLoaded) {
            return;
        }

        try {
            $presetPaths = PresetManager::loadPreset($this->preset, $this->basePath);

            // Merge preset paths with existing paths
            $this->paths = array_merge($this->paths, $presetPaths);

            $this->presetLoaded = true;
        } catch (\Exception $e) {
            // If preset loading fails, continue with default paths
            $this->presetLoaded = true;
        }
    }

    /**
     * Generate relative URL for internal routing
     * 
     * @param string $path URL path segment (without leading slash in concept, but handled safely)
     * @return string Valid internal URL
     */
    public function urlFor(string $path): string
    {
        $path = str_replace(['\\', '//'], '/', $path);

        // Define route mappings if needed, or simple pass-through
        // For basic usage as referenced in AuthController: 'home' -> '/'
        if ($path === 'home') {
            return '/';
        }

        return '/' . ltrim($path, '/');
    }

    /**
     * Sanitize path to prevent path traversal attacks
     * 
     * @param string $path Path to sanitize
     * @return string Sanitized path
     * @throws RuntimeException If path contains dangerous patterns
     */
    private function sanitizePath(string $path): string
    {
        // Check for path traversal patterns
        if (preg_match('/\.\.\/|\.\.\\\\/', $path)) {
            throw new RuntimeException("Path traversal detected in path: {$path}");
        }

        // Check for null bytes
        if (strpos($path, "\0") !== false) {
            throw new RuntimeException("Null byte detected in path: {$path}");
        }

        // Check for encoded path traversal
        $decoded = urldecode($path);
        if (preg_match('/\.\.\/|\.\.\\\\/', $decoded)) {
            throw new RuntimeException("Encoded path traversal detected in path: {$path}");
        }

        return $path;
    }
}
