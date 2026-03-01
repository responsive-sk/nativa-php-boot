<?php

declare(strict_types=1);

namespace Interfaces\HTTP\View;

/**
 * TemplateRenderer - Native PHP template renderer with layout support
 */
class TemplateRenderer
{
    private string $templatesPath;
    private string $cachePath;
    private bool $debug;
    private string $cacheVersion;

    /** @var array<string, mixed> */
    private array $sharedData = [];

    /** @var array<string, mixed> */
    private array $currentData = [];

    /** @var array<string, string> */
    private array $templateCache = [];

    private ?string $currentLayout = null;
    private string $currentContent = '';
    private bool $isAdminTemplate = false;

    public function __construct(
        string $templatesPath,
        ?string $cachePath = null,
        bool $debug = false,
        ?string $version = null
    ) {
        $this->templatesPath = rtrim($templatesPath, '/');
        $this->cachePath = $cachePath ? rtrim($cachePath, '/') : null;
        $this->debug = $debug;
        $this->cacheVersion = $version ?? $this->loadCacheVersion();
    }

    /**
     * Set shared data available to all templates
     */
    public function share(string $key, mixed $value): void
    {
        $this->sharedData[$key] = $value;
    }

    /**
     * Render a template with optional layout
     *
     * @param array<string, mixed> $data
     *
     * @return string
     */
    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $this->currentData = array_merge($this->sharedData, $data);
        $this->currentLayout = $layout;
        $this->isAdminTemplate = str_starts_with($template, 'admin/');

        if ($this->currentLayout) {
            // Render content first, then layout
            $this->currentContent = $this->renderTemplate($template);
            $layoutResult = $this->renderLayout($this->currentLayout);
            return $layoutResult !== false ? $layoutResult : '';
        }

        $result = $this->renderTemplate($template);
        return $result !== false ? $result : '';
    }

    /**
     * Render a partial template
     *
     * @param array<string, mixed> $data
     *
     * @return string
     */
    public function partial(string $partial, array $data = []): string
    {
        $partialData = array_merge($this->currentData, $data);
        $result = $this->renderTemplate('partials/' . $partial, $partialData);
        return $result !== false ? $result : '';
    }

    /**
     * Get content from yielded section (used in layouts)
     */
    public function yieldContent(): string
    {
        return $this->currentContent;
    }

    /**
     * Escape HTML output
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format date
     */
    public function date(?string $date, string $format = 'M d, Y'): string
    {
        if ($date === null) {
            return '';
        }
        return date($format, strtotime($date));
    }

    /**
     * Convert newlines to <br>
     */
    public function nl2br(string $text): string
    {
        return nl2br($text);
    }

    /**
     * Generate URL
     */
    public function url(string $path): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? '';
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Check if value is empty
     */
    public function isEmpty(mixed $value): bool
    {
        return empty($value);
    }

    /**
     * Get current data for use in templates
     *
     * @return array<string, mixed>
     */
    public function getCurrentData(): array
    {
        return $this->currentData;
    }

    /**
     * Render a template file
     *
     * @param array<string, mixed> $data
     *
     * @return false|string
     */
    private function renderTemplate(string $template, array $data = []): string|false
    {
        // Determine template path based on template type
        $templatePath = $this->templatesPath;

        if (str_starts_with($template, 'admin/')) {
            // Admin templates
            $templatePath .= '/pages/admin';
            $template = substr($template, 6); // Remove 'admin/' prefix
        } elseif (str_starts_with($template, 'frontend/')) {
            // Frontend templates (Templates/pages/frontend)
            $templatePath .= '/pages/frontend';
            $template = substr($template, 9); // Remove 'frontend/' prefix
        } else {
            // Legacy frontend templates
            $templatePath .= '/pages';
        }

        $templatePath .= '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            error_log("WARN: TemplateRenderer template not found: {$templatePath}");
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        
        // Invalidate OPcache for this template file (development mode only)
        // Skip entirely in production to avoid warnings on shared hosting
        if ($this->debug && function_exists('opcache_invalidate')) {
            // Check if OPcache functions are not disabled (shared hosting)
            $disabled = explode(',', ini_get('disable_functions'));
            if (!in_array('opcache_invalidate', $disabled, true)) {
                opcache_invalidate($templatePath, true);
            }
        }
        // else: production mode - skip OPcache invalidation

        // Check in-memory cache first
        $cacheKey = $template . $this->cacheVersion;
        if (isset($this->templateCache[$cacheKey])) {
            $templatePath = $this->templateCache[$cacheKey];
        } elseif ($this->cachePath && !$this->debug) {
            // Use compiled cache in production
            $cacheFile = $this->getCacheFile($template);

            $templateMtime = filemtime($templatePath);
            $cacheMtime = file_exists($cacheFile) ? filemtime($cacheFile) : false;

            // Compile if cache doesn't exist or template is newer
            if ($cacheMtime === false || $templateMtime > $cacheMtime) {
                $this->compileTemplate($templatePath, $cacheFile);
            }

            // Use cache file if it exists, otherwise use original template
            if (file_exists($cacheFile)) {
                $templatePath = $cacheFile;
                $this->templateCache[$cacheKey] = $templatePath;
            }
            // else: use original $templatePath (fallback for production)
        }

        // Extract data for template
        extract($data ?: $this->currentData, EXTR_SKIP);

        // Capture output
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Render layout with yielded content
     *
     * @return false|string
     */
    private function renderLayout(string $layout): string|false
    {
        // Determine layout path based on template type
        $layoutPath = $this->templatesPath;

        // Layout path (Templates/layouts/)
        if (str_starts_with($layout, 'admin/')) {
            $layoutPath .= '/layouts/admin.php';
        } elseif (str_starts_with($layout, 'frontend/')) {
            $layoutPath .= '/layouts/frontend.php';
        } else {
            // Legacy layouts
            $layoutPath .= '/layouts/' . $layout . '.php';
        }

        if (!file_exists($layoutPath)) {
            error_log("WARN: TemplateRenderer layout not found: {$layoutPath}");
            throw new \RuntimeException("Layout not found: {$layoutPath}");
        }


        // Extract data for layout including content
        $data = array_merge($this->currentData, ['content' => $this->currentContent]);
        extract($data, EXTR_SKIP);

        // Capture output
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }

    /**
     * Compile template to cache
     */
    private function compileTemplate(string $source, string $target): void
    {
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = file_get_contents($source);
        
        // Add comment with source path for debugging
        if ($this->debug) {
            $content = "<?php /* Template: {$source} */ ?>" . "\n" . $content;
        }
        
        file_put_contents($target, $content);
        
        // Update cache timestamp
        $this->updateCacheTimestamp();
    }

    /**
     * Clear all template caches
     */
    public function clearCache(): void
    {
        if ($this->cachePath && is_dir($this->cachePath)) {
            $this->deleteDirectory($this->cachePath);
            mkdir($this->cachePath, 0755, true);
        }
        $this->templateCache = [];
    }

    /**
     * Warm up cache for templates
     *
     * @param array<string> $templates
     */
    public function warmupCache(array $templates): void
    {
        foreach ($templates as $template) {
            $templatePath = $this->templatesPath . '/' . $template . '.php';
            if (file_exists($templatePath)) {
                $cacheFile = $this->getCacheFile($template);
                $this->compileTemplate($templatePath, $cacheFile);
            }
        }
    }

    /**
     * Get cache file path
     */
    private function getCacheFile(string $template): string
    {
        return $this->cachePath . '/' . md5($template . $this->cacheVersion) . '.php';
    }

    /**
     * Load cache version from file
     */
    private function loadCacheVersion(): string
    {
        $versionFile = $this->cachePath . '/.cache-version';
        if (file_exists($versionFile)) {
            $content = file_get_contents($versionFile);
            return $content !== false ? trim($content) : date('YmdHis');
        }
        return date('YmdHis');
    }

    /**
     * Update cache timestamp file
     */
    private function updateCacheTimestamp(): void
    {
        if ($this->cachePath) {
            $versionFile = $this->cachePath . '/.cache-version';
            file_put_contents($versionFile, $this->cacheVersion);
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
