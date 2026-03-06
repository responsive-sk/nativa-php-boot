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

    private static string $manifestPath = '/assets/manifest.json';

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
            'core-init' => 'init.js',
            'core-app'  => 'app.ts',
            'core-css'  => 'css.ts',
            'init.js'   => 'init.js',
            'app.js'    => 'app.ts',
            // Svelte components
            'article-list' => '../svelte/components/ArticleList.svelte',
            'contact-form' => '../svelte/components/ContactForm.svelte',
            'theme-toggle' => '../svelte/components/ThemeToggle.svelte',
            'navigation' => '../svelte/components/Navigation.svelte',
            'toast' => '../svelte/components/Toast.svelte',
            // CSS styles
            'tokens' => '../styles/tokens.css',
            'components' => '../styles/components.css',
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
        $assetKey = preg_replace('/\.ts$/', '.ts', $assetKey) ?? $assetKey;

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

        // Normalize asset name
        $assetKey = $asset;

        // Special mapping for core-* entries
        $coreMap = [
            'core-css'  => 'css.ts',
            'core-app'  => 'app.ts',
            'core-init' => 'init.js',
        ];

        // Try different possible keys including use-cases paths
        $possibleKeys = [
            $asset,
            $assetKey,
            $assetKey . '.ts',
            $assetKey . '.css',
            $assetKey . '.js',
            basename($asset, '.css') . '.ts',
            basename($asset, '.css') . '.css',
            'frontend/use-cases/' . basename($asset, '.css') . '/' . basename($asset, '.css') . '.ts',
            'frontend/pages/' . basename($asset, '.css') . '.ts',
        ];

        // Add core mapping if applicable
        if (isset($coreMap[$assetKey])) {
            array_unshift($possibleKeys, $coreMap[$assetKey]);
        }

        foreach ($possibleKeys as $key) {
            if (isset($manifest[$key])) {
                $entry = $manifest[$key];

                // Check if this entry has CSS files
                if (isset($entry['css']) && \is_array($entry['css']) && \count($entry['css']) > 0) {
                    $cssFile = $entry['css'][0];

                    return self::$assetBaseUrl . $cssFile;
                }

                // If the entry itself is a CSS file
                if (isset($entry['file']) && str_ends_with($entry['file'], '.css')) {
                    return self::$assetBaseUrl . $entry['file'];
                }
            }
        }

        // Fallback to original asset name
        $url = self::$assetBaseUrl . $asset;
        // Add .css extension if not present
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
            'home'      => 'frontend/pages/home.ts',
            'blog'      => 'frontend/pages/blog.ts',
            'portfolio' => 'frontend/pages/portfolio.ts',
            'contact'   => 'frontend/pages/contact.ts',
            'docs'      => 'frontend/pages/docs.ts',
            'services'  => 'frontend/pages/services.ts',
            'pricing'   => 'frontend/pages/pricing.ts',
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

        $manifestPath = $_SERVER['DOCUMENT_ROOT'] . self::$manifestPath;

        // Try alternative path if DOCUMENT_ROOT is not set or wrong
        if (!file_exists($manifestPath)) {
            $manifestPath = \dirname(__DIR__, 3) . '/public' . self::$manifestPath;
        }

        if (!file_exists($manifestPath)) {
            error_log("WARN: AssetHelper manifest.json not found at {$manifestPath}, using fallback mode");
            self::$manifest = [];

            return self::$manifest;
        }

        $content = file_get_contents($manifestPath);
        if (false === $content) {
            error_log("WARN: AssetHelper could not read manifest.json from {$manifestPath}");
            self::$manifest = [];

            return self::$manifest;
        }

        $manifest = json_decode($content, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            error_log('WARN: AssetHelper invalid JSON in manifest.json: ' . json_last_error_msg());
            self::$manifest = [];

            return self::$manifest;
        }

        self::$manifest = $manifest;

        return self::$manifest;
    }
}
