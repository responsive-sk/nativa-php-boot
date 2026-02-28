<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Media Entity
 */
final class Media
{
    private string $id;
    private ?string $userId;
    private string $filename;
    private string $originalName;
    private string $mimeType;
    private int $size;
    private string $path;
    private string $url;
    private string $provider;
    private ?string $hash;
    private string $createdAt;

    private function __construct()
    {
    }

    public static function create(array $data): self
    {
        $media = new self();
        $media->id = self::generateId();
        $media->userId = $data['user_id'] ?? null;
        $media->filename = $data['filename'];
        $media->originalName = $data['original_name'] ?? '';
        $media->mimeType = $data['mime_type'];
        $media->size = $data['size'];
        $media->path = $data['path'];
        $media->url = $data['url'];
        $media->provider = $data['provider'] ?? 'local';
        $media->hash = $data['hash'] ?? null;
        $media->createdAt = self::now();

        return $media;
    }

    public static function fromArray(array $data): self
    {
        $media = new self();
        $media->id = $data['id'];
        $media->userId = $data['user_id'] ?? null;
        $media->filename = $data['filename'];
        $media->originalName = $data['original_name'] ?? '';
        $media->mimeType = $data['mime_type'];
        $media->size = (int) $data['size'];
        $media->path = $data['path'];
        $media->url = $data['url'];
        $media->provider = $data['provider'] ?? 'local';
        $media->hash = $data['hash'] ?? null;
        $media->createdAt = $data['created_at'];

        return $media;
    }

    // Getters
    public function id(): string
    {
        return $this->id;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function originalName(): string
    {
        return $this->originalName;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function hash(): ?string
    {
        return $this->hash;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'filename' => $this->filename,
            'original_name' => $this->originalName,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'path' => $this->path,
            'url' => $this->url,
            'provider' => $this->provider,
            'hash' => $this->hash,
            'created_at' => $this->createdAt,
        ];
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mimeType, 'video/');
    }

    public function isPdf(): bool
    {
        return $this->mimeType === 'application/pdf';
    }

    public function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
