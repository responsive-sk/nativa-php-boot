<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Page Media Entity (Page attachments/gallery)
 */
final class PageMedia
{
    private string $id;
    private string $pageId;
    private string $mediaId;
    private ?string $caption;
    private int $sortOrder;
    private string $createdAt;

    private function __construct()
    {
    }

    public static function create(
        string $pageId,
        string $mediaId,
        ?string $caption = null,
        int $sortOrder = 0,
    ): self {
        $pageMedia = new self();
        $pageMedia->id = self::generateId();
        $pageMedia->pageId = $pageId;
        $pageMedia->mediaId = $mediaId;
        $pageMedia->caption = $caption;
        $pageMedia->sortOrder = $sortOrder;
        $pageMedia->createdAt = self::now();

        return $pageMedia;
    }

    public static function fromArray(array $data): self
    {
        $pageMedia = new self();
        $pageMedia->id = $data['id'];
        $pageMedia->pageId = $data['page_id'];
        $pageMedia->mediaId = $data['media_id'];
        $pageMedia->caption = $data['caption'] ?? null;
        $pageMedia->sortOrder = (int) ($data['sort_order'] ?? 0);
        $pageMedia->createdAt = $data['created_at'];

        return $pageMedia;
    }

    public function update(
        ?string $caption = null,
        ?int $sortOrder = null,
    ): void {
        if ($caption !== null) {
            $this->caption = $caption;
        }
        if ($sortOrder !== null) {
            $this->sortOrder = $sortOrder;
        }
    }

    // Getters
    public function id(): string
    {
        return $this->id;
    }

    public function pageId(): string
    {
        return $this->pageId;
    }

    public function mediaId(): string
    {
        return $this->mediaId;
    }

    public function caption(): ?string
    {
        return $this->caption;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'page_id' => $this->pageId,
            'media_id' => $this->mediaId,
            'caption' => $this->caption,
            'sort_order' => $this->sortOrder,
            'created_at' => $this->createdAt,
        ];
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
