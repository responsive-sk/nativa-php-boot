<?php

declare(strict_types=1);

namespace Infrastructure\View;

/**
 * AssetHelper - Resolves asset paths from Vite manifest for production builds
 * 
 * Reads the Vite manifest.json to get hashed filenames in production,
 * falls back to original filenames in development.
 * 
 * Usage:
 *   AssetHelper::js('app.js')     // Returns: /assets/app.a1b2c3d4.js
 *   AssetHelper::css('css.css')   // Returns: /assets/css.e5f6g7h8.css
 * 
 * Logging:
 *   - DEBUG: Log manifest.json load path and contents
 *   - DEBUG: Log asset resolution (input → output path)
 *   - WARN: Log if manifest.json is missing or unreadable
 */
final class AssetHelper
{
    private static ?array $manifest = null;
    private static string $manifestPath = '/assets/manifest.json';
    private static string $assetBaseUrl = '/assets/';

    /**
     * Load and cache the Vite manifest
     * 
     * @return array<string, array{file: string, name: string, src: string, isEntry?: bool, css?: array<string>, imports?: array<string>}>
     */
    private static function loadManifest(): array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $manifestPath = $_SERVER['DOCUMENT_ROOT'] . self::$manifestPath;
        
        // Try alternative path if DOCUMENT_ROOT is not set or wrong
        if (!file_exists($manifestPath)) {
            $manifestPath = dirname(__DIR__, 3) . '/public' . self::$manifestPath;
        }


        if (!file_exists($manifestPath)) {
            error_log("WARN: AssetHelper manifest.json not found at {$manifestPath}, using fallback mode");
            self::$manifest = [];
            return self::$manifest;
        }

        $content = file_get_contents($manifestPath);
        if ($content === false) {
            error_log("WARN: AssetHelper could not read manifest.json from {$manifestPath}");
            self::$manifest = [];
            return self::$manifest;
        }

        $manifest = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("WARN: AssetHelper invalid JSON in manifest.json: " . json_last_error_msg());
            self::$manifest = [];
            return self::$manifest;
        }


        self::$manifest = $manifest;
        return self::$manifest;
    }

    /**
     * Get the hashed filename for a JavaScript asset
     *
     * @param string $asset Asset name (e.g., 'app.js', 'init.js')
     * @return string Full asset URL with hash
     */
    public static function js(string $asset): string
    {
        $manifest = self::loadManifest();

        // Map legacy names to new manifest keys
        $legacyMap = [
            'init.js' => 'core-init',
            'init' => 'core-init',
            'app.js' => 'core-app',
            'app' => 'core-app',
            'css.js' => 'core-css',
            'css' => 'core-css',
        ];

        // Check legacy map first
        if (isset($legacyMap[$asset])) {
            $key = $legacyMap[$asset];
            if (isset($manifest[$key])) {
                $file = $manifest[$key]['file'];
                $url = self::$assetBaseUrl . $file;
                return $url;
            }
        }

        // Normalize asset name (remove .js extension for manifest lookup)
        $assetKey = preg_replace('/\.js$/', '', $asset);
        $assetKey = preg_replace('/\.ts$/', '.ts', $assetKey);

        // Try different possible keys
        $possibleKeys = [
            $asset,
            $assetKey . '.ts',
            $assetKey . '.js',
            basename($asset, '.js') . '.ts',
        ];

        foreach ($possibleKeys as $key) {
            if (isset($manifest[$key])) {
                $file = $manifest[$key]['file'];
                $url = self::$assetBaseUrl . $file;
                return $url;
            }
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        return $url;
    }

    /**
     * Get the hashed filename for a CSS asset
     * 
     * @param string $asset Asset name (e.g., 'css.css', 'home.css')
     * @return string Full asset URL with hash
     */
    public static function css(string $asset): string
    {
        $manifest = self::loadManifest();
        
        // Normalize asset name
        $assetKey = $asset;
        
        // Try different possible keys
        $possibleKeys = [
            $asset,
            basename($asset, '.css') . '.ts',
            'use-cases/' . basename($asset, '.css') . '.ts',
        ];

        foreach ($possibleKeys as $key) {
            if (isset($manifest[$key])) {
                $entry = $manifest[$key];
                
                // Check if this entry has CSS files
                if (isset($entry['css']) && is_array($entry['css']) && count($entry['css']) > 0) {
                    $cssFile = $entry['css'][0];
                    $url = self::$assetBaseUrl . $cssFile;
                    return $url;
                }
                
                // If the entry itself is a CSS file
                if (isset($entry['file']) && str_ends_with($entry['file'], '.css')) {
                    $url = self::$assetBaseUrl . $entry['file'];
                    return $url;
                }
            }
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        return $url;
    }

    /**
     * Get the hashed filename for any asset type
     * 
     * @param string $asset Asset name with extension
     * @return string Full asset URL with hash
     */
    public static function asset(string $asset): string
    {
        $manifest = self::loadManifest();
        
        // Try exact match first
        if (isset($manifest[$asset])) {
            $file = $manifest[$asset]['file'];
            $url = self::$assetBaseUrl . $file;
            return $url;
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        return $url;
    }

    /**
     * Get page-specific CSS from manifest
     *
     * @param string $page Page identifier (home, blog, portfolio, etc.)
     * @return string|null Full CSS URL or null if not found
     */
    public static function pageCss(string $page): ?string
    {
        $manifest = self::loadManifest();
        
        // Map page names to Vite entry points (matches vite.config.ts input keys)
        $pageMap = [
            'home' => 'frontend/pages/home.ts',
            'blog' => 'frontend/pages/blog.ts',
            'portfolio' => 'frontend/pages/portfolio.ts',
            'contact' => 'frontend/pages/contact.ts',
            'docs' => 'frontend/pages/docs.ts',
            'services' => 'frontend/pages/services.ts',
            'pricing' => 'frontend/pages/pricing.ts',
            'not-found' => 'frontend/use-cases/not-found/not-found.css',
        ];
        
        if (!isset($pageMap[$page])) {
            return null;
        }
        
        $entryKey = $pageMap[$page];
        
        if (!isset($manifest[$entryKey])) {
            error_log("WARN: AssetHelper pageCss - manifest entry not found: {$entryKey}");
            return null;
        }
        
        $entry = $manifest[$entryKey];
        
        // Check if entry has CSS files
        if (!isset($entry['css']) || !is_array($entry['css']) || empty($entry['css'])) {
            return null;
        }
        
        $cssFile = $entry['css'][0];
        $url = self::$assetBaseUrl . $cssFile;
        
        return $url;
    }

    /**
     * Clear the cached manifest (useful for development)
     */
    public static function clearCache(): void
    {
        self::$manifest = null;
    }

    /**
     * Check if manifest is loaded and has entries
     */
    public static function hasManifest(): bool
    {
        $manifest = self::loadManifest();
        return !empty($manifest);
    }

    /**
     * Get all manifest entries (useful for debugging)
     * 
     * @return array<string, array{file: string, name: string, src: string, isEntry?: bool, css?: array<string>, imports?: array<string>}>
     */
    public static function getManifest(): array
    {
        return self::loadManifest();
    }
}
