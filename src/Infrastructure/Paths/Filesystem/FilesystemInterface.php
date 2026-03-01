<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Filesystem;

/**
 * Lightweight filesystem interface for basic file operations
 * 
 * Provides essential file operations without external dependencies
 */
interface FilesystemInterface
{
    /**
     * Check if file exists
     */
    public function exists(string $path): bool;

    /**
     * Read file contents
     * 
     * @throws FilesystemException If file cannot be read
     */
    public function read(string $path): string;

    /**
     * Write file contents
     * 
     * @throws FilesystemException If file cannot be written
     */
    public function write(string $path, string $contents): void;

    /**
     * Delete file
     * 
     * @throws FilesystemException If file cannot be deleted
     */
    public function delete(string $path): void;

    /**
     * Create directory
     * 
     * @throws FilesystemException If directory cannot be created
     */
    public function createDirectory(string $path, int $permissions = 0755): void;

    /**
     * Check if path is directory
     */
    public function isDirectory(string $path): bool;

    /**
     * Check if path is file
     */
    public function isFile(string $path): bool;

    /**
     * Get file size in bytes
     * 
     * @throws FilesystemException If file size cannot be determined
     */
    public function getSize(string $path): int;

    /**
     * Get file modification time
     * 
     * @throws FilesystemException If modification time cannot be determined
     */
    public function getModifiedTime(string $path): int;

    /**
     * List directory contents
     * 
     * @return array<string> Array of file/directory names
     * @throws FilesystemException If directory cannot be read
     */
    public function listContents(string $path): array;

    /**
     * Copy file
     * 
     * @throws FilesystemException If file cannot be copied
     */
    public function copy(string $source, string $destination): void;

    /**
     * Move/rename file
     * 
     * @throws FilesystemException If file cannot be moved
     */
    public function move(string $source, string $destination): void;

    /**
     * Get file permissions
     * 
     * @throws FilesystemException If permissions cannot be determined
     */
    public function getPermissions(string $path): int;

    /**
     * Set file permissions
     * 
     * @throws FilesystemException If permissions cannot be set
     */
    public function setPermissions(string $path, int $permissions): void;
}
