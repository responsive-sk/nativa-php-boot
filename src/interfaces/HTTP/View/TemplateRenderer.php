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
     */
    public function render(string $template, array $data = [], ?string $layout = null): string
    {
        $this->currentData = array_merge($this->sharedData, $data);
        $this->currentLayout = $layout;
        $this->isAdminTemplate = str_starts_with($template, 'admin/');

        if ($this->currentLayout) {
            // Render content first, then layout
            $this->currentContent = $this->renderTemplate($template);
            return $this->renderLayout($this->currentLayout);
        }

        return $this->renderTemplate($template);
    }

    /**
     * Render a partial template
     */
    public function partial(string $partial, array $data = []): string
    {
        $partialData = array_merge($this->currentData, $data);
        return $this->renderTemplate('partials/' . $partial, $partialData);
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
     */
    private function renderTemplate(string $template, array $data = []): string
    {
        // Determine template type (admin or frontend)
        $templatePath = $this->templatesPath;
        
        if (str_starts_with($template, 'admin/')) {
            $templatePath .= '/admin';
            $template = substr($template, 6); // Remove 'admin/' prefix
        } else {
            $templatePath .= '/frontend';
        }
        
        $templatePath .= '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        // Check in-memory cache first
        $cacheKey = $template . $this->cacheVersion;
        if (isset($this->templateCache[$cacheKey])) {
            $templatePath = $this->templateCache[$cacheKey];
        } elseif ($this->cachePath && !$this->debug) {
            // Use compiled cache in production
            $cacheFile = $this->getCacheFile($template);

            if (!file_exists($cacheFile) || filemtime($templatePath) > filemtime($cacheFile)) {
                $this->compileTemplate($templatePath, $cacheFile);
            }

            $templatePath = $cacheFile;
            $this->templateCache[$cacheKey] = $templatePath;
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
     */
    private function renderLayout(string $layout): string
    {
        // Determine layout path based on template type
        $layoutPath = $this->templatesPath;
        
        // Layout already includes path (e.g., 'admin/layouts/base' or 'layouts/base')
        if (str_starts_with($layout, 'admin/')) {
            $layoutPath .= '/' . $layout . '.php';
        } else {
            $layoutPath .= '/frontend/' . $layout . '.php';
        }

        if (!file_exists($layoutPath)) {
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
            return trim(file_get_contents($versionFile));
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
