<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Media;
use Domain\Repository\MediaRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;
use PDO;

/**
 * Media Repository Implementation
 */
class MediaRepository implements MediaRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function create(array $data): Media
    {
        $media = Media::create($data);

        $sql = <<<SQL
            INSERT INTO media (
                id, user_id, filename, original_name, mime_type, size,
                path, url, provider, hash
            ) VALUES (
                :id, :user_id, :filename, :original_name, :mime_type, :size,
                :path, :url, :provider, :hash
            )
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute([
            ':id' => $media->id(),
            ':user_id' => $media->userId(),
            ':filename' => $media->filename(),
            ':original_name' => $media->originalName(),
            ':mime_type' => $media->mimeType(),
            ':size' => $media->size(),
            ':path' => $media->path(),
            ':url' => $media->url(),
            ':provider' => $media->provider(),
            ':hash' => $media->hash(),
        ]);

        return $media;
    }

    public function findById(string $id): ?Media
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM media WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Media::fromArray($data);
    }

    public function findAll(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM media
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Media::fromArray($row), $stmt->fetchAll());
    }

    public function findByUserId(string $userId, int $limit = 20): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM media
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        SQL);

        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Media::fromArray($row), $stmt->fetchAll());
    }

    public function findByHash(string $hash): ?Media
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM media WHERE hash = ? LIMIT 1');
        $stmt->execute([$hash]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Media::fromArray($data);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM media WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM media');
        return (int) $stmt->fetchColumn();
    }
}
