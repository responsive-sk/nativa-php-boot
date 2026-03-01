<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Security;

/**
 * Security Configuration for Path Operations
 * 
 * Centralized configuration for path security settings
 */
class SecurityConfig
{
    private bool $enablePathTraversalProtection = true;
    private bool $enableEncodingProtection = true;
    private bool $enableExtensionValidation = true;
    private bool $enableLengthValidation = true;
    private bool $enableHiddenFileProtection = true;
    private bool $strictMode = false;
    
    /** @var array<string> */
    private array $trustedPaths = [];
    
    /** @var array<string> */
    private array $allowedExtensions = [];
    
    /** @var array<string> */
    private array $blockedExtensions = [];
    
    private int $maxPathLength = 4096;
    private int $maxFilenameLength = 255;
    
    /** @var array<string> */
    private array $customDangerousPatterns = [];

    /**
     * Enable/disable path traversal protection
     */
    public function setPathTraversalProtection(bool $enabled): self
    {
        $this->enablePathTraversalProtection = $enabled;
        return $this;
    }

    /**
     * Enable/disable encoding attack protection
     */
    public function setEncodingProtection(bool $enabled): self
    {
        $this->enableEncodingProtection = $enabled;
        return $this;
    }

    /**
     * Enable/disable file extension validation
     */
    public function setExtensionValidation(bool $enabled): self
    {
        $this->enableExtensionValidation = $enabled;
        return $this;
    }

    /**
     * Enable/disable path length validation
     */
    public function setLengthValidation(bool $enabled): self
    {
        $this->enableLengthValidation = $enabled;
        return $this;
    }

    /**
     * Enable/disable hidden file protection
     */
    public function setHiddenFileProtection(bool $enabled): self
    {
        $this->enableHiddenFileProtection = $enabled;
        return $this;
    }

    /**
     * Enable/disable strict mode
     */
    public function setStrictMode(bool $enabled): self
    {
        $this->strictMode = $enabled;
        return $this;
    }

    /**
     * Add trusted paths that bypass some security checks
     * 
     * @param array<string> $paths
     */
    public function addTrustedPaths(array $paths): self
    {
        $this->trustedPaths = array_merge($this->trustedPaths, $paths);
        return $this;
    }

    /**
     * Set allowed file extensions
     * 
     * @param array<string> $extensions
     */
    public function setAllowedExtensions(array $extensions): self
    {
        $this->allowedExtensions = array_map('strtolower', $extensions);
        return $this;
    }

    /**
     * Set blocked file extensions
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
     * Add custom dangerous patterns
     * 
     * @param array<string> $patterns
     */
    public function addCustomDangerousPatterns(array $patterns): self
    {
        $this->customDangerousPatterns = array_merge($this->customDangerousPatterns, $patterns);
        return $this;
    }

    // Getters

    public function isPathTraversalProtectionEnabled(): bool
    {
        return $this->enablePathTraversalProtection;
    }

    public function isEncodingProtectionEnabled(): bool
    {
        return $this->enableEncodingProtection;
    }

    public function isExtensionValidationEnabled(): bool
    {
        return $this->enableExtensionValidation;
    }

    public function isLengthValidationEnabled(): bool
    {
        return $this->enableLengthValidation;
    }

    public function isHiddenFileProtectionEnabled(): bool
    {
        return $this->enableHiddenFileProtection;
    }

    public function isStrictModeEnabled(): bool
    {
        return $this->strictMode;
    }

    /**
     * @return array<string>
     */
    public function getTrustedPaths(): array
    {
        return $this->trustedPaths;
    }

    /**
     * @return array<string>
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @return array<string>
     */
    public function getBlockedExtensions(): array
    {
        return $this->blockedExtensions;
    }

    public function getMaxPathLength(): int
    {
        return $this->maxPathLength;
    }

    public function getMaxFilenameLength(): int
    {
        return $this->maxFilenameLength;
    }

    /**
     * @return array<string>
     */
    public function getCustomDangerousPatterns(): array
    {
        return $this->customDangerousPatterns;
    }

    /**
     * Check if path is trusted
     */
    public function isPathTrusted(string $path): bool
    {
        foreach ($this->trustedPaths as $trustedPath) {
            if (str_starts_with($path, $trustedPath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create configuration for development environment
     */
    public static function forDevelopment(): self
    {
        return (new self())
            ->setStrictMode(false)
            ->setHiddenFileProtection(false)
            ->setMaxPathLength(8192)
            ->setMaxFilenameLength(500);
    }

    /**
     * Create configuration for production environment
     */
    public static function forProduction(): self
    {
        return (new self())
            ->setStrictMode(true)
            ->setHiddenFileProtection(true)
            ->setMaxPathLength(2048)
            ->setMaxFilenameLength(200);
    }

    /**
     * Create configuration for uploads
     */
    public static function forUploads(): self
    {
        return (new self())
            ->setStrictMode(true)
            ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'doc', 'docx'])
            ->setMaxPathLength(1024)
            ->setMaxFilenameLength(100);
    }

    /**
     * Create configuration for templates
     */
    public static function forTemplates(): self
    {
        return (new self())
            ->setStrictMode(false)
            ->setHiddenFileProtection(false)
            ->setAllowedExtensions(['phtml', 'twig', 'html', 'htm', 'xml'])
            ->setMaxPathLength(2048)
            ->setMaxFilenameLength(200);
    }
}
