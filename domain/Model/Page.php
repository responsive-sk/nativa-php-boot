<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\ValueObjects\Slug;

/**
 * Page Entity (Static pages like About, Contact, etc.)
 */
final class Page
{
    private string $id;
    private string $title;
    private Slug $slug;
    private string $content;
    private string $template;
    private ?string $metaTitle;
    private ?string $metaDescription;
    private bool $isPublished;
    private string $createdAt;
    private string $updatedAt;

    private function __construct()
    {
    }

    public static function create(
        string $title,
        string $content,
        string $template = 'default',
        ?string $metaTitle = null,
        ?string $metaDescription = null,
    ): self {
        $page = new self();
        $page->id = self::generateId();
        $page->title = $title;
        $page->slug = Slug::fromString($title);
        $page->content = $content;
        $page->template = $template;
        $page->metaTitle = $metaTitle;
        $page->metaDescription = $metaDescription;
        $page->isPublished = false;
        $page->createdAt = self::now();
        $page->updatedAt = self::now();

        return $page;
    }

    public static function fromArray(array $data): self
    {
        $page = new self();
        $page->id = $data['id'];
        $page->title = $data['title'];
        $page->slug = new Slug($data['slug']);
        $page->content = $data['content'];
        $page->template = $data['template'] ?? 'default';
        $page->metaTitle = $data['meta_title'] ?? null;
        $page->metaDescription = $data['meta_description'] ?? null;
        $page->isPublished = (bool) ($data['is_published'] ?? false);
        $page->createdAt = $data['created_at'];
        $page->updatedAt = $data['updated_at'];

        return $page;
    }

    public function publish(): void
    {
        $this->isPublished = true;
        $this->updatedAt = self::now();
    }

    public function unpublish(): void
    {
        $this->isPublished = false;
        $this->updatedAt = self::now();
    }

    public function update(
        ?string $title = null,
        ?string $content = null,
        ?string $template = null,
        ?string $metaTitle = null,
        ?string $metaDescription = null,
    ): void {
        if ($title !== null) {
            $this->title = $title;
            $this->slug = Slug::fromString($title);
        }

        if ($content !== null) {
            $this->content = $content;
        }

        if ($template !== null) {
            $this->template = $template;
        }

        if ($metaTitle !== null) {
            $this->metaTitle = $metaTitle;
        }

        if ($metaDescription !== null) {
            $this->metaDescription = $metaDescription;
        }

        $this->updatedAt = self::now();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): string
    {
        return $this->slug->value();
    }

    public function content(): string
    {
        return $this->content;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function metaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function metaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug->value(),
            'content' => $this->content,
            'template' => $this->template,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'is_published' => $this->isPublished,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
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
