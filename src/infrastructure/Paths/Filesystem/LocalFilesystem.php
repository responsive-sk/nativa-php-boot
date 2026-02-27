<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Filesystem;

/**
 * Local filesystem implementation using native PHP functions
 * 
 * Provides secure file operations with path validation
 */
class LocalFilesystem implements FilesystemInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        
        // Ensure base path exists
        if (!is_dir($this->basePath)) {
            if (!mkdir($this->basePath, 0755, true) && !is_dir($this->basePath)) {
                throw FilesystemException::cannotCreateDirectory($this->basePath);
            }
        }
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function read(string $path): string
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            throw FilesystemException::cannotReadFile($path, 'File does not exist');
        }

        $contents = file_get_contents($fullPath);
        if ($contents === false) {
            throw FilesystemException::cannotReadFile($path, 'Failed to read file contents');
        }

        return $contents;
    }

    public function write(string $path, string $contents): void
    {
        $fullPath = $this->getFullPath($path);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw FilesystemException::cannotCreateDirectory($directory);
            }
        }

        if (file_put_contents($fullPath, $contents) === false) {
            throw FilesystemException::cannotWriteFile($path, 'Failed to write file contents');
        }
    }

    public function delete(string $path): void
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            return; // Already deleted
        }

        if (!unlink($fullPath)) {
            throw FilesystemException::cannotDeleteFile($path, 'Failed to delete file');
        }
    }

    public function createDirectory(string $path, int $permissions = 0755): void
    {
        $fullPath = $this->getFullPath($path);
        
        if (is_dir($fullPath)) {
            return; // Already exists
        }

        if (!mkdir($fullPath, $permissions, true) && !is_dir($fullPath)) {
            throw FilesystemException::cannotCreateDirectory($path);
        }
    }

    public function isDirectory(string $path): bool
    {
        return is_dir($this->getFullPath($path));
    }

    public function isFile(string $path): bool
    {
        return is_file($this->getFullPath($path));
    }

    public function getSize(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            throw FilesystemException::cannotGetFileInfo($path, 'size', 'File does not exist');
        }

        $size = filesize($fullPath);
        if ($size === false) {
            throw FilesystemException::cannotGetFileInfo($path, 'size', 'Failed to get file size');
        }

        return $size;
    }

    public function getModifiedTime(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            throw FilesystemException::cannotGetFileInfo($path, 'modification time', 'File does not exist');
        }

        $time = filemtime($fullPath);
        if ($time === false) {
            throw FilesystemException::cannotGetFileInfo($path, 'modification time', 'Failed to get modification time');
        }

        return $time;
    }

    public function listContents(string $path): array
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->isDirectory($path)) {
            throw FilesystemException::cannotListDirectory($path, 'Path is not a directory');
        }

        $contents = scandir($fullPath);
        if ($contents === false) {
            throw FilesystemException::cannotListDirectory($path, 'Failed to scan directory');
        }

        // Remove . and .. entries
        return array_values(array_filter($contents, fn($item) => $item !== '.' && $item !== '..'));
    }

    public function copy(string $source, string $destination): void
    {
        $sourceFullPath = $this->getFullPath($source);
        $destinationFullPath = $this->getFullPath($destination);
        
        if (!$this->exists($source)) {
            throw FilesystemException::cannotCopyFile($source, $destination, 'Source file does not exist');
        }

        // Ensure destination directory exists
        $destinationDir = dirname($destinationFullPath);
        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
                throw FilesystemException::cannotCreateDirectory($destinationDir);
            }
        }

        if (!copy($sourceFullPath, $destinationFullPath)) {
            throw FilesystemException::cannotCopyFile($source, $destination, 'Copy operation failed');
        }
    }

    public function move(string $source, string $destination): void
    {
        $sourceFullPath = $this->getFullPath($source);
        $destinationFullPath = $this->getFullPath($destination);
        
        if (!$this->exists($source)) {
            throw FilesystemException::cannotMoveFile($source, $destination, 'Source file does not exist');
        }

        // Ensure destination directory exists
        $destinationDir = dirname($destinationFullPath);
        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
                throw FilesystemException::cannotCreateDirectory($destinationDir);
            }
        }

        if (!rename($sourceFullPath, $destinationFullPath)) {
            throw FilesystemException::cannotMoveFile($source, $destination, 'Move operation failed');
        }
    }

    public function getPermissions(string $path): int
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            throw FilesystemException::cannotGetFileInfo($path, 'permissions', 'File does not exist');
        }

        $permissions = fileperms($fullPath);
        if ($permissions === false) {
            throw FilesystemException::cannotGetFileInfo($path, 'permissions', 'Failed to get file permissions');
        }

        return $permissions & 0777; // Return only permission bits
    }

    public function setPermissions(string $path, int $permissions): void
    {
        $fullPath = $this->getFullPath($path);
        
        if (!$this->exists($path)) {
            throw FilesystemException::cannotSetPermissions($path, 'File does not exist');
        }

        if (!chmod($fullPath, $permissions)) {
            throw FilesystemException::cannotSetPermissions($path, 'Failed to set permissions');
        }
    }

    /**
     * Get base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get full path with security validation
     */
    private function getFullPath(string $path): string
    {
        // Basic path traversal protection
        if (str_contains($path, '..')) {
            throw new \InvalidArgumentException("Path traversal detected: {$path}");
        }

        // Normalize path separators
        $normalizedPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        
        // Remove leading separators
        $normalizedPath = ltrim($normalizedPath, DIRECTORY_SEPARATOR);
        
        return $this->basePath . DIRECTORY_SEPARATOR . $normalizedPath;
    }
}
