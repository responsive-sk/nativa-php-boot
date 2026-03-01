<?php

declare(strict_types=1);

namespace Infrastructure\Paths;

/**
 * Lazy loading preset manager
 * 
 * Loads presets only when needed to save memory.
 * Presets are optional and loaded on-demand.
 */
class PresetManager
{
    /** @var array<string, string> */
    private static array $presetClasses = [
        'mezzio' => 'ResponsiveSk\\Slim4Paths\\Presets\\MezzioPresetLite',
        'laravel' => 'ResponsiveSk\\Slim4Paths\\Presets\\LaravelPreset',
        'slim4' => 'ResponsiveSk\\Slim4Paths\\Presets\\Slim4Preset',
        'laminas' => 'ResponsiveSk\\Slim4Paths\\Presets\\LaminasPreset',
    ];
    
    /** @var array<string, PresetInterface> */
    private static array $loadedPresets = [];
    
    /**
     * Get available preset names
     * 
     * @return array<string>
     */
    public static function getAvailablePresets(): array
    {
        return array_keys(self::$presetClasses);
    }
    
    /**
     * Check if preset exists
     * 
     * @param string $preset Preset name
     * @return bool
     */
    public static function exists(string $preset): bool
    {
        return isset(self::$presetClasses[$preset]);
    }
    
    /**
     * Load preset (lazy loading)
     * 
     * @param string $preset Preset name
     * @param string $basePath Base path
     * @return array<string, string> Preset paths
     * @throws \InvalidArgumentException If preset doesn't exist
     */
    public static function loadPreset(string $preset, string $basePath): array
    {
        if (!self::exists($preset)) {
            throw new \InvalidArgumentException("Preset '{$preset}' not found. Available: " . implode(', ', self::getAvailablePresets()));
        }
        
        $cacheKey = $preset . ':' . $basePath;
        
        // Return cached instance if available
        if (isset(self::$loadedPresets[$cacheKey])) {
            return self::$loadedPresets[$cacheKey]->getPaths();
        }
        
        // Lazy load preset class
        $className = self::$presetClasses[$preset];
        
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Preset class '{$className}' not found");
        }
        
        $presetInstance = new $className($basePath);
        assert($presetInstance instanceof PresetInterface);
        self::$loadedPresets[$cacheKey] = $presetInstance;
        
        return $presetInstance->getPaths();
    }
    
    /**
     * Create Paths instance with preset
     *
     * @param string $preset Preset name
     * @param string $basePath Base path
     * @return Paths
     */
    public static function createWithPreset(string $preset, string $basePath): Paths
    {
        $presetPaths = self::loadPreset($preset, $basePath);
        return new Paths($basePath, $presetPaths);
    }
    
    /**
     * Clear loaded presets cache
     * 
     * @return void
     */
    public static function clearCache(): void
    {
        self::$loadedPresets = [];
    }
    
    /**
     * Get memory usage of loaded presets
     * 
     * @return array<string, mixed>
     */
    public static function getMemoryUsage(): array
    {
        return [
            'loaded_presets' => count(self::$loadedPresets),
            'preset_keys' => array_keys(self::$loadedPresets),
            'memory_estimate' => count(self::$loadedPresets) * 1024, // Rough estimate
        ];
    }
}
