<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Security;

/**
 * Advanced Path Sanitization and Security Validation
 * 
 * Provides multi-layer security for path operations:
 * - Path traversal protection
 * - Encoding attack prevention
 * - File extension validation
 * - Path length limits
 * - Whitelist/blacklist support
 */
class PathSanitizer
{
    /** @var array<string> */
    private array $allowedExtensions = [];
    
    /** @var array<string> */
    private array $blockedExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar',
        'exe', 'bat', 'cmd', 'com', 'scr', 'vbs', 'js',
        'jar', 'sh', 'py', 'pl', 'rb', 'asp', 'aspx',
        'jsp', 'cgi', 'htaccess', 'htpasswd'
    ];
    
    /** @var array<string> */
    private array $dangerousPatterns = [
        '../', '..\\', './', '.\\',
        '~/', '~\\',
        'file://', 'http://', 'https://', 'ftp://',
        'php://', 'data://', 'expect://', 'zip://',
        '\x00', '\0', '%00', '%2e%2e', '%2f', '%5c',
        '<script', '</script>', '<?php', '<?=',
        'eval(', 'exec(', 'system(', 'shell_exec(',
        'passthru(', 'file_get_contents(', 'include(',
        'require(', 'include_once(', 'require_once('
    ];
    
    private int $maxPathLength = 4096;
    private int $maxFilenameLength = 255;
    private bool $strictMode = true;

    /**
     * Set allowed file extensions (whitelist)
     * 
     * @param array<string> $extensions
     */
    public function setAllowedExtensions(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set blocked file extensions (blacklist)
     * 
     * @param array<string> $extensions
     */
    public function setBlockedExtensions(array $extensions): self
    {
        $this->blockedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set maximum path length
     */
    public function setMaxPathLength(int $length): self
    {
        $this->maxPathLength = max(1, $length);
        return $this;
    }

    /**
     * Set maximum filename length
     */
    public function setMaxFilenameLength(int $length): self
    {
        $this->maxFilenameLength = max(1, $length);
        return $this;
    }

    /**
     * Enable/disable strict mode
     */
    public function setStrictMode(bool $strict): self
    {
        $this->strictMode = $strict;
        return $this;
    }

    /**
     * Sanitize and validate path
     * 
     * @throws \InvalidArgumentException If path is invalid or dangerous
     */
    public function sanitize(string $path): string
    {
        // Basic validation
        $this->validateBasicPath($path);
        
        // Normalize path
        $normalized = $this->normalizePath($path);
        
        // Security validation
        $this->validateSecurity($normalized);
        
        // Extension validation
        $this->validateExtension($normalized);
        
        return $normalized;
    }

    /**
     * Validate basic path properties
     */
    private function validateBasicPath(string $path): void
    {
        if (empty($path)) {
            throw new \InvalidArgumentException('Path cannot be empty');
        }

        if (strlen($path) > $this->maxPathLength) {
            throw new \InvalidArgumentException(
                "Path too long: {$this->maxPathLength} characters maximum"
            );
        }

        // Check for null bytes
        if (str_contains($path, "\0")) {
            throw new \InvalidArgumentException('Path contains null byte');
        }

        // Check filename length
        $filename = basename($path);
        if (strlen($filename) > $this->maxFilenameLength) {
            throw new \InvalidArgumentException(
                "Filename too long: {$this->maxFilenameLength} characters maximum"
            );
        }
    }

    /**
     * Normalize path separators and encoding
     */
    private function normalizePath(string $path): string
    {
        // Decode URL encoding
        $decoded = urldecode($path);
        
        // Decode HTML entities
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Normalize directory separators
        $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $decoded);
        
        // Remove multiple consecutive separators
        $normalized = preg_replace('#' . preg_quote(DIRECTORY_SEPARATOR) . '+#', DIRECTORY_SEPARATOR, $normalized) ?? $normalized;

        // Trim separators from start and end
        return trim($normalized, DIRECTORY_SEPARATOR);
    }

    /**
     * Validate against security threats
     */
    private function validateSecurity(string $path): void
    {
        $lowerPath = strtolower($path);
        
        foreach ($this->dangerousPatterns as $pattern) {
            if (str_contains($lowerPath, strtolower($pattern))) {
                throw new \InvalidArgumentException(
                    "Dangerous pattern detected in path: {$pattern}"
                );
            }
        }

        // Check for path traversal attempts
        if (str_contains($path, '..')) {
            throw new \InvalidArgumentException('Path traversal detected');
        }

        // Check for absolute paths in strict mode
        if ($this->strictMode && $this->isAbsolutePath($path)) {
            throw new \InvalidArgumentException('Absolute paths not allowed in strict mode');
        }

        // Check for hidden files/directories
        if ($this->strictMode && $this->containsHiddenElements($path)) {
            throw new \InvalidArgumentException('Hidden files/directories not allowed in strict mode');
        }
    }

    /**
     * Validate file extension
     */
    private function validateExtension(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if (empty($extension)) {
            return; // No extension is allowed
        }

        // Check whitelist first (if set)
        if (!empty($this->allowedExtensions)) {
            if (!in_array($extension, $this->allowedExtensions, true)) {
                throw new \InvalidArgumentException(
                    "File extension '{$extension}' not in allowed list"
                );
            }
            return;
        }

        // Check blacklist
        if (in_array($extension, $this->blockedExtensions, true)) {
            throw new \InvalidArgumentException(
                "File extension '{$extension}' is blocked for security reasons"
            );
        }
    }

    /**
     * Check if path is absolute
     */
    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/') || 
               (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Za-z]:/', $path));
    }

    /**
     * Check if path contains hidden elements
     */
    private function containsHiddenElements(string $path): bool
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        
        foreach ($parts as $part) {
            if (str_starts_with($part, '.') && $part !== '.' && $part !== '..') {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Create sanitizer with safe defaults for uploads
     */
    public static function forUploads(): self
    {
        return (new self())
            ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'doc', 'docx'])
            ->setMaxPathLength(1024)
            ->setMaxFilenameLength(100)
            ->setStrictMode(true);
    }

    /**
     * Create sanitizer with safe defaults for templates
     */
    public static function forTemplates(): self
    {
        return (new self())
            ->setAllowedExtensions(['phtml', 'twig', 'html', 'htm', 'xml'])
            ->setMaxPathLength(2048)
            ->setMaxFilenameLength(200)
            ->setStrictMode(false);
    }

    /**
     * Create sanitizer with safe defaults for content
     */
    public static function forContent(): self
    {
        return (new self())
            ->setAllowedExtensions(['md', 'txt', 'json', 'yaml', 'yml'])
            ->setMaxPathLength(2048)
            ->setMaxFilenameLength(200)
            ->setStrictMode(false);
    }
}
