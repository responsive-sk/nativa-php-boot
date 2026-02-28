<?php

declare(strict_types=1);

namespace Infrastructure\Storage\Providers;

use Infrastructure\Paths\AppPaths;
use RuntimeException;

/**
 * Local Storage Provider
 * Stores files on local filesystem
 */
final class LocalStorageProvider implements MediaProviderInterface
{
    private string $basePath;
    private string $baseUrl;

    public function __construct(
        ?string $basePath = null,
        ?string $baseUrl = null
    ) {
        $appPaths = AppPaths::instance();
        $this->basePath = $basePath ?? $appPaths->storage('uploads');
        $this->baseUrl = $baseUrl ?? '/storage/uploads';

        // Ensure base directory exists
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0755, true);
        }
    }

    public function upload(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Invalid file upload');
        }

        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        
        // Create date-based directory structure
        $datePath = date('Y/m/d');
        $targetDir = $this->basePath . '/' . $datePath;
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        $relativePath = $datePath . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Failed to move uploaded file');
        }

        return [
            'path' => $relativePath,
            'url' => $this->getUrl($relativePath),
            'size' => (int) $file['size'],
            'mime_type' => $file['type'] ?: mime_content_type($file['tmp_name']),
            'original_name' => $file['name'],
        ];
    }

    public function delete(string $path): bool
    {
        $fullPath = $this->basePath . '/' . ltrim($path, '/');
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    public function getUrl(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }

    public function getSize(string $path): int
    {
        $fullPath = $this->basePath . '/' . ltrim($path, '/');
        return file_exists($fullPath) ? (int) filesize($fullPath) : 0;
    }

    public function exists(string $path): bool
    {
        $fullPath = $this->basePath . '/' . ltrim($path, '/');
        return file_exists($fullPath);
    }

    public function getName(): string
    {
        return 'local';
    }

    /**
     * Validate uploaded file
     *
     * @throws RuntimeException
     */
    private function validateFile(array $file): void
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error: ' . $this->getUploadErrorMessage($file['error']));
        }

        // Check file size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            throw new RuntimeException('File size exceeds maximum allowed size (10MB)');
        }

        // Validate MIME type using native PHP
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
            'video/mp4',
            'video/x-msvideo',
            'video/x-matroska',
        ];

        $mimeType = $file['type'] ?: mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes, true)) {
            throw new RuntimeException('File type not allowed: ' . $mimeType);
        }
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload',
            default => 'Unknown upload error',
        };
    }
}
