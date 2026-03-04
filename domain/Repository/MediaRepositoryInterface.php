<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Media;

/**
 * Media Repository Interface
 */
interface MediaRepositoryInterface
{
    /**
     * Create media record
     */
    public function create(array $data): Media;

    /**
     * Find media by ID
     */
    public function findById(string $id): ?Media;

    /**
     * Find all media
     *
     * @return array<Media>
     */
    public function findAll(int $limit = 20, int $offset = 0): array;

    /**
     * Find media by user ID
     *
     * @return array<Media>
     */
    public function findByUserId(string $userId, int $limit = 20): array;

    /**
     * Find media by hash
     */
    public function findByHash(string $hash): ?Media;

    /**
     * Delete media
     */
    public function delete(string $id): bool;

    /**
     * Count all media
     */
    public function count(): int;
}
