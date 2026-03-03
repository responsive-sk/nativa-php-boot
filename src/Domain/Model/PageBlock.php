<?php

declare(strict_types = 1);

namespace Domain\Model;

/**
 * Page Block Entity (Reusable content sections).
 */
final class PageBlock
{
    private string $id;

    private string $pageId;

    private string $type; // hero, features, cta, text_image, testimonials

    private ?string $title;

    private ?string $content;

    /** @var array<string, mixed> */
    private array $data; // JSON data for block-specific settings

    private int $sortOrder;

    private bool $isActive;

    private string $createdAt;

    private function __construct() {}

    /**
     * @param array<string, mixed> $data
     */
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

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $block = new self();
        $block->id = (string) $data['id'];
        $block->pageId = (string) $data['page_id'];
        $block->type = (string) $data['type'];
        $block->title = isset($data['title']) ? (string) $data['title'] : null;
        $block->content = isset($data['content']) ? (string) $data['content'] : null;
        $block->data = (array) (json_decode($data['data'] ?? '[]', true) ?? []);
        $block->sortOrder = (int) ($data['sort_order'] ?? 0);
        $block->isActive = (bool) ($data['is_active'] ?? true);
        $block->createdAt = (string) $data['created_at'];

        return $block;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(
        ?string $title = null,
        ?string $content = null,
        ?array $data = null,
        ?int $sortOrder = null,
        ?bool $isActive = null,
    ): void {
        if (null !== $title) {
            $this->title = $title;
        }
        if (null !== $content) {
            $this->content = $content;
        }
        if (null !== $data) {
            $this->data = $data;
        }
        if (null !== $sortOrder) {
            $this->sortOrder = $sortOrder;
        }
        if (null !== $isActive) {
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'page_id'    => $this->pageId,
            'type'       => $this->type,
            'title'      => $this->title,
            'content'    => $this->content,
            'data'       => json_encode($this->data),
            'sort_order' => $this->sortOrder,
            'is_active'  => $this->isActive,
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
