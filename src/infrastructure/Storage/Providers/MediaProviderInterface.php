<?php

declare(strict_types=1);

namespace Infrastructure\Storage\Providers;

/**
 * Media Provider Interface - Strategy Pattern
 */
interface MediaProviderInterface
{
    /**
     * Upload file
     *
     * @param array $file $_FILES array
     * @return array{path: string, url: string, size: int, mime_type: string}
     */
    public function upload(array $file): array;

    /**
     * Delete file
     */
    public function delete(string $path): bool;

    /**
     * Get file URL
     */
    public function getUrl(string $path): string;

    /**
     * Get file size
     */
    public function getSize(string $path): int;

    /**
     * Check if file exists
     */
    public function exists(string $path): bool;

    /**
     * Get provider name
     */
    public function getName(): string;
}
