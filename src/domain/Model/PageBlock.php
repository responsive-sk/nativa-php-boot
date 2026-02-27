<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Page Block Entity (Reusable content sections)
 */
class PageBlock
{
    private string $id;
    private string $pageId;
    private string $type; // hero, features, cta, text_image, testimonials
    private ?string $title;
    private ?string $content;
    private array $data; // JSON data for block-specific settings
    private int $sortOrder;
    private bool $isActive;
    private string $createdAt;

    private function __construct()
    {
    }

    public static function create(
        string $pageId,
        string $type,
        ?string $title = null,
        ?string $content = null,
        array $data = [],
        int $sortOrder = 0,
    ): self {
        $block = new self();
        $block->id = self::generateId();
        $block->pageId = $pageId;
        $block->type = $type;
        $block->title = $title;
        $block->content = $content;
        $block->data = $data;
        $block->sortOrder = $sortOrder;
        $block->isActive = true;
        $block->createdAt = self::now();

        return $block;
    }

    public static function fromArray(array $data): self
    {
        $block = new self();
        $block->id = $data['id'];
        $block->pageId = $data['page_id'];
        $block->type = $data['type'];
        $block->title = $data['title'] ?? null;
        $block->content = $data['content'] ?? null;
        $block->data = json_decode($data['data'] ?? '[]', true) ?? [];
        $block->sortOrder = (int) ($data['sort_order'] ?? 0);
        $block->isActive = (bool) ($data['is_active'] ?? true);
        $block->createdAt = $data['created_at'];

        return $block;
    }

    public function update(
        ?string $title = null,
        ?string $content = null,
        ?array $data = null,
        ?int $sortOrder = null,
        ?bool $isActive = null,
    ): void {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($content !== null) {
            $this->content = $content;
        }
        if ($data !== null) {
            $this->data = $data;
        }
        if ($sortOrder !== null) {
            $this->sortOrder = $sortOrder;
        }
        if ($isActive !== null) {
            $this->isActive = $isActive;
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

    public function type(): string
    {
        return $this->type;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function isActive(): bool
    {
        return $this->isActive;
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
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'data' => json_encode($this->data),
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
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
