<?php

declare(strict_types = 1);

namespace Infrastructure\View;

/**
 * AssetHelper - Resolves asset paths from Vite manifest for production builds.
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

    private static array $manifestPaths = [
        'frontend' => 'public/assets/frontend/manifest.json',
        'svelte' => 'public/assets/svelte/svelte-manifest.json',
        'admin' => 'public/assets/admin/admin-manifest.json',
    ];

    private static string $assetBaseUrl = '/assets/';

    /**
     * Get the hashed filename for a JavaScript asset.
     *
     * @param string $asset Asset name (e.g., 'app.js', 'init.js')
     *
     * @return string Full asset URL with hash
     */
    public static function js(string $asset): string
    {
        $manifest = self::loadManifest();

        // Map legacy/core names to manifest keys
        $nameMap = [
            'core-init' => '../vanilla/frontend/src/init.js',
            'core-app'  => '../vanilla/frontend/src/app.ts',
            'core-css'  => '../vanilla/frontend/src/css.ts',
            // Page-specific JS
            'home'      => '../vanilla/frontend/src/pages/home.ts',
            'blog'      => '../vanilla/frontend/src/pages/blog.ts',
            'portfolio' => '../vanilla/frontend/src/pages/portfolio.ts',
            'contact'   => '../vanilla/frontend/src/pages/contact.ts',
            'docs'      => '../vanilla/frontend/src/pages/docs.ts',
            'about'     => '../vanilla/frontend/src/pages/about.ts',
            'services'  => '../vanilla/frontend/src/pages/services.ts',
            'pricing'   => '../vanilla/frontend/src/pages/pricing.ts',
            'articles'  => '../vanilla/frontend/src/pages/articles.ts',
            // Svelte components
            'navigation-enhance' => '../svelte/frontend/src/navigation-enhance.js',
        ];

        // Try mapped name first
        if (isset($nameMap[$asset])) {
            $manifestKey = $nameMap[$asset];
            if (isset($manifest[$manifestKey])) {
                $file = $manifest[$manifestKey]['file'];

                return self::$assetBaseUrl . $file;
            }
        }

        // Normalize asset name (remove .js extension for manifest lookup)
        $assetKey = preg_replace('/\.js$/', '', $asset) ?? $asset;

        // Try different possible keys
        $possibleKeys = [
            $asset,
            $assetKey,
            $assetKey . '.ts',
            $assetKey . '.js',
            basename($asset, '.js') . '.ts',
            'frontend/use-cases/' . basename($asset, '.js') . '/' . basename($asset, '.js') . '.ts',
            'frontend/pages/' . basename($asset, '.js') . '.ts',
        ];

        foreach ($possibleKeys as $key) {
            if (isset($manifest[$key]) && \is_array($manifest[$key]) && isset($manifest[$key]['file'])) {
                /** @var string $file */
                $file = $manifest[$key]['file'];

                return self::$assetBaseUrl . $file;
            }
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        // Add .js extension if not present
        if (!str_ends_with($url, '.js')) {
            $url .= '.js';
        }

        return $url;
    }

    /**
     * Get the hashed filename for a CSS asset.
     *
     * @param string $asset Asset name (e.g., 'css.css', 'home.css', 'auth.css')
     *
     * @return string Full asset URL with hash
     */
    public static function css(string $asset): string
    {
        $manifest = self::loadManifest();

        // First check nameMap
        $nameMap = [
            'core-init' => '../vanilla/frontend/src/init.js',
            'core-app'  => '../vanilla/frontend/src/app.ts',
            'core-css'  => '../vanilla/frontend/src/css.ts',
            // Page-specific CSS
            'home'      => '../vanilla/frontend/src/pages/home.ts',
            'blog'      => '../vanilla/frontend/src/pages/blog.ts',
            'portfolio' => '../vanilla/frontend/src/pages/portfolio.ts',
            'contact'   => '../vanilla/frontend/src/pages/contact.ts',
            'docs'      => '../vanilla/frontend/src/pages/docs.ts',
            'about'     => '../vanilla/frontend/src/pages/about.ts',
            'services'  => '../vanilla/frontend/src/pages/services.ts',
            'pricing'   => '../vanilla/frontend/src/pages/pricing.ts',
            'articles'  => '../vanilla/frontend/src/pages/articles.ts',
        ];

        // Try mapped name first
        if (isset($nameMap[$asset])) {
            $key = $nameMap[$asset];
            if (isset($manifest[$key])) {
                $entry = $manifest[$key];
                
                // Check if entry has CSS files
                if (isset($entry['css']) && \is_array($entry['css']) && !empty($entry['css'])) {
                    return self::$assetBaseUrl . $entry['css'][0];
                }
                
                // Fallback to JS file if no CSS
                return self::$assetBaseUrl . $entry['file'];
            }
        }

        // Try different possible keys
        $possibleKeys = [
            $asset,
            $asset . '.ts',
            $asset . '.css',
            basename($asset, '.css') . '.ts',
        ];

        foreach ($possibleKeys as $key) {
            if (isset($manifest[$key]) && isset($manifest[$key]['css'])) {
                $entry = $manifest[$key];
                if (\is_array($entry['css']) && !empty($entry['css'])) {
                    return self::$assetBaseUrl . $entry['css'][0];
                }
            }
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        if (!str_ends_with($url, '.css')) {
            $url .= '.css';
        }

        return $url;
    }

    /**
     * Get the hashed filename for any asset type.
     *
     * @param string $asset Asset name with extension
     *
     * @return string Full asset URL with hash
     */
    public static function asset(string $asset): string
    {
        $manifest = self::loadManifest();

        // Try exact match first
        if (isset($manifest[$asset])) {
            $file = $manifest[$asset]['file'];

            return self::$assetBaseUrl . $file;
        }

        // Fallback to original asset name
        return self::$assetBaseUrl . $asset;
    }

    /**
     * Get page-specific CSS from manifest.
     *
     * @param string $page Page identifier (home, blog, portfolio, etc.)
     *
     * @return string|null Full CSS URL or null if not found
     */
    public static function pageCss(string $page): ?string
    {
        $manifest = self::loadManifest();

        // Map page names to Vite entry points (matches vite.config.ts input keys)
        $pageMap = [
            'home'      => '../vanilla/frontend/src/pages/home.ts',
            'blog'      => '../vanilla/frontend/src/pages/blog.ts',
            'portfolio' => '../vanilla/frontend/src/pages/portfolio.ts',
            'contact'   => '../vanilla/frontend/src/pages/contact.ts',
            'docs'      => '../vanilla/frontend/src/pages/docs.ts',
            'services'  => '../vanilla/frontend/src/pages/services.ts',
            'pricing'   => '../vanilla/frontend/src/pages/pricing.ts',
            'about'     => '../vanilla/frontend/src/pages/about.ts',
            'articles'  => '../vanilla/frontend/src/pages/articles.ts',
            'not-found' => '../vanilla/frontend/src/pages/not-found.css',
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
        if (!isset($entry['css']) || !\is_array($entry['css']) || empty($entry['css'])) {
            return null;
        }

        $cssFile = $entry['css'][0];

        return self::$assetBaseUrl . $cssFile;
    }

    /**
     * Clear the cached manifest (useful for development).
     */
    public static function clearCache(): void
    {
        self::$manifest = null;
    }

    /**
     * Check if manifest is loaded and has entries.
     */
    public static function hasManifest(): bool
    {
        $manifest = self::loadManifest();

        return !empty($manifest);
    }

    /**
     * Get all manifest entries (useful for debugging).
     *
     * @return array<string, array{file: string, name: string, src: string, isEntry?: bool, css?: array<string>, imports?: array<string>}>
     */
    public static function getManifest(): array
    {
        return self::loadManifest();
    }

    /**
     * Load and cache the Vite manifest.
     *
     * @return array<string, array{file: string, name: string, src: string, isEntry?: bool, css?: array<string>, imports?: array<string>}>
     */
    private static function loadManifest(): array
    {
        if (null !== self::$manifest) {
            return self::$manifest;
        }

        // Hardcoded project root - adjust if needed
        $projectRoot = '/home/evan/dev/nativa-php-boot';

        // Try each manifest path (frontend, svelte, admin)
        foreach (self::$manifestPaths as $type => $relPath) {
            $manifestPath = $projectRoot . '/' . $relPath;
            
            if (file_exists($manifestPath)) {
                $content = file_get_contents($manifestPath);
                if (false !== $content) {
                    $manifest = json_decode($content, true);
                    if (\is_array($manifest) && !empty($manifest)) {
                        self::$manifest = $manifest;

                        return self::$manifest;
                    }
                }
            }
        }

        // No manifest found
        self::$manifest = [];

        return self::$manifest;
    }
}
