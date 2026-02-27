<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Repository\MediaRepositoryInterface;
use Infrastructure\Storage\Providers\MediaProviderInterface;

/**
 * Media Manager - Handles media operations with provider abstraction
 */
class MediaManager
{
    public function __construct(
        private readonly MediaProviderInterface $provider,
        private readonly MediaRepositoryInterface $mediaRepository,
    ) {
    }

    /**
     * Upload file
     *
     * @param array $file $_FILES array
     * @param string|null $userId User ID who uploaded
     * @return array Media data with 'duplicate' flag if file already exists
     */
    public function upload(array $file, ?string $userId = null): array
    {
        // Calculate file hash BEFORE upload
        $hash = $this->calculateFileHash($file['tmp_name']);

        // Check if file with same hash already exists
        $existingMedia = $this->mediaRepository->findByHash($hash);

        if ($existingMedia !== null) {
            // File already exists - return existing media info
            return [
                'id' => $existingMedia->id(),
                'url' => $existingMedia->url(),
                'path' => $existingMedia->path(),
                'size' => $existingMedia->size(),
                'mime_type' => $existingMedia->mimeType(),
                'provider' => $existingMedia->provider(),
                'duplicate' => true,
                'message' => 'File already exists in media library',
            ];
        }

        // Upload to storage provider
        $uploadResult = $this->provider->upload($file);

        // Save to database with hash
        $media = $this->mediaRepository->create([
            'user_id' => $userId,
            'filename' => $uploadResult['path'],
            'original_name' => $file['name'] ?? basename($uploadResult['path']),
            'mime_type' => $uploadResult['mime_type'],
            'size' => $uploadResult['size'],
            'path' => $uploadResult['path'],
            'url' => $uploadResult['url'],
            'provider' => $this->provider->getName(),
            'hash' => $hash,
        ]);

        return [
            'id' => $media->id(),
            'url' => $uploadResult['url'],
            'path' => $uploadResult['path'],
            'size' => $uploadResult['size'],
            'mime_type' => $uploadResult['mime_type'],
            'provider' => $this->provider->getName(),
            'duplicate' => false,
        ];
    }

    /**
     * Calculate SHA256 hash of file
     */
    private function calculateFileHash(string $tmpPath): string
    {
        return hash_file('sha256', $tmpPath);
    }

    /**
     * Delete media
     */
    public function delete(string $mediaId): bool
    {
        $media = $this->mediaRepository->findById($mediaId);

        if ($media === null) {
            return false;
        }

        // Delete from storage provider
        $this->provider->delete($media->path());

        // Delete from database
        return $this->mediaRepository->delete($mediaId);
    }

    /**
     * Get media by ID
     */
    public function findById(string $id): ?\Domain\Model\Media
    {
        return $this->mediaRepository->findById($id);
    }

    /**
     * Get all media
     *
     * @return array<\Domain\Model\Media>
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        return $this->mediaRepository->findAll($limit, $offset);
    }

    /**
     * Get media by user
     *
     * @return array<\Domain\Model\Media>
     */
    public function findByUser(string $userId, int $limit = 20): array
    {
        return $this->mediaRepository->findByUserId($userId, $limit);
    }

    /**
     * Get storage provider name
     */
    public function getProviderName(): string
    {
        return $this->provider->getName();
    }
}
