<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Factory for creating framework presets
 */
class PresetFactory
{
    /**
     * Available presets
     * 
     * @var array<string, class-string<PresetInterface>>
     */
    private static array $presets = [
        'laravel' => LaravelPreset::class,
        'slim4' => Slim4Preset::class,
        'mezzio' => MezzioPreset::class,
        'laminas' => MezzioPreset::class, // Alias for Mezzio
    ];

    /**
     * Create preset by name
     * 
     * @param string $name Preset name (laravel, slim4, mezzio, laminas)
     * @param string $basePath Base application path
     * @return PresetInterface
     * @throws \InvalidArgumentException If preset doesn't exist
     */
    public static function create(string $name, string $basePath): PresetInterface
    {
        $name = strtolower($name);
        
        if (!isset(self::$presets[$name])) {
            throw new \InvalidArgumentException(
                "Unknown preset '{$name}'. Available presets: " . implode(', ', array_keys(self::$presets))
            );
        }

        $presetClass = self::$presets[$name];
        return new $presetClass($basePath);
    }

    /**
     * Get available preset names
     * 
     * @return array<string>
     */
    public static function getAvailablePresets(): array
    {
        return array_keys(self::$presets);
    }

    /**
     * Register custom preset
     * 
     * @param string $name Preset name
     * @param class-string<PresetInterface> $presetClass Preset class
     */
    public static function register(string $name, string $presetClass): void
    {
        if (!is_subclass_of($presetClass, PresetInterface::class)) {
            throw new \InvalidArgumentException(
                "Preset class must implement PresetInterface"
            );
        }

        self::$presets[strtolower($name)] = $presetClass;
    }

    /**
     * Check if preset exists
     */
    public static function has(string $name): bool
    {
        return isset(self::$presets[strtolower($name)]);
    }

    /**
     * Get preset information
     * 
     * @return array<string, array{name: string, description: string, class: string}>
     */
    public static function getPresetInfo(): array
    {
        $info = [];
        
        foreach (self::$presets as $key => $class) {
            // Create temporary instance to get info
            $preset = new $class('/tmp');
            $info[$key] = [
                'name' => $preset->getName(),
                'description' => $preset->getDescription(),
                'class' => $class,
            ];
        }
        
        return $info;
    }
}
