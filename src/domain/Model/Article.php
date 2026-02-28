<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\Events\ArticleCreated;
use Domain\Events\ArticleDeleted;
use Domain\Events\ArticlePublished;
use Domain\Events\ArticleUpdated;
use Domain\Events\DomainEventInterface;
use Domain\ValueObjects\ArticleStatus;
use Domain\ValueObjects\Slug;

/**
 * Article Entity
 */
final class Article
{
    private string $id;
    private string $authorId;
    private ?string $categoryId;
    private string $title;
    private Slug $slug;
    private string $excerpt;
    private string $content;
    private ?string $image;
    private ArticleStatus $status;
    private int $views;
    private ?string $publishedAt;
    private string $createdAt;
    private string $updatedAt;

    /** @var array<string> */
    private array $tags = [];

    /** @var array<DomainEventInterface> */
    private array $events = [];

    private function __construct()
    {
    }

    public static function create(
        string $title,
        string $content,
        string $authorId,
        ?string $categoryId = null,
        ?string $excerpt = null,
        ?string $image = null,
    ): self {
        $article = new self();
        $article->id = self::generateId();
        $article->authorId = $authorId;
        $article->categoryId = $categoryId;
        $article->title = $title;
        $article->slug = Slug::fromString($title);
        $article->content = $content;
        $article->excerpt = $excerpt ?? '';
        $article->image = $image;
        $article->status = ArticleStatus::draft();
        $article->views = 0;
        $article->publishedAt = null;
        $article->createdAt = self::now();
        $article->updatedAt = self::now();

        $article->recordEvent(new ArticleCreated(
            $article->id,
            $title,
            $authorId,
            $categoryId
        ));

        return $article;
    }

    public static function fromArray(array $data): self
    {
        $article = new self();
        $article->id = $data['id'];
        $article->authorId = $data['author_id'];
        $article->categoryId = $data['category_id'] ?? null;
        $article->title = $data['title'];
        $article->slug = new Slug($data['slug']);
        $article->content = $data['content'];
        $article->excerpt = $data['excerpt'] ?? '';
        $article->image = $data['image'] ?? null;
        $article->status = new ArticleStatus($data['status']);
        $article->views = (int) ($data['views'] ?? 0);
        $article->publishedAt = $data['published_at'] ?? null;
        $article->createdAt = $data['created_at'];
        $article->updatedAt = $data['updated_at'];
        $article->tags = $data['tags'] ?? [];

        return $article;
    }

    public function publish(): void
    {
        if (!$this->status->canTransitionTo(ArticleStatus::published())) {
            throw new \RuntimeException('Cannot publish this article');
        }

        $this->status = ArticleStatus::published();
        $this->publishedAt = self::now();
        $this->updatedAt = self::now();

        $this->recordEvent(new ArticlePublished($this->id, $this->title, $this->publishedAt));
    }

    public function unpublish(): void
    {
        $this->status = ArticleStatus::draft();
        $this->publishedAt = null;
        $this->updatedAt = self::now();
    }

    public function archive(): void
    {
        $this->status = ArticleStatus::archived();
        $this->updatedAt = self::now();
    }

    public function incrementViews(): void
    {
        $this->views++;
    }

    public function update(
        ?string $title = null,
        ?string $content = null,
        ?string $excerpt = null,
        ?string $categoryId = null,
        ?string $image = null,
    ): void {
        $changes = [];

        if ($title !== null) {
            $this->title = $title;
            $this->slug = Slug::fromString($title);
            $changes['title'] = $title;
            $changes['slug'] = $this->slug->value();
        }

        if ($content !== null) {
            $this->content = $content;
            $changes['content'] = $content;
        }

        if ($excerpt !== null) {
            $this->excerpt = $excerpt;
            $changes['excerpt'] = $excerpt;
        }

        if ($categoryId !== null) {
            $this->categoryId = $categoryId;
            $changes['category_id'] = $categoryId;
        }

        if ($image !== null) {
            $this->image = $image;
            $changes['image'] = $image;
        }

        $this->updatedAt = self::now();

        if (!empty($changes)) {
            $this->recordEvent(new ArticleUpdated($this->id, $this->title, $changes));
        }
    }

    public function addTags(array $tags): void
    {
        $this->tags = array_unique(array_merge($this->tags, $tags));
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    // Getters
    public function id(): string
    {
        return $this->id;
    }

    public function authorId(): string
    {
        return $this->authorId;
    }

    public function categoryId(): ?string
    {
        return $this->categoryId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function slug(): string
    {
        return $this->slug->value();
    }

    public function excerpt(): string
    {
        return $this->excerpt;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function image(): ?string
    {
        return $this->image;
    }

    public function status(): ArticleStatus
    {
        return $this->status;
    }

    public function views(): int
    {
        return $this->views;
    }

    public function publishedAt(): ?string
    {
        return $this->publishedAt;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }

    /**
     * Record a domain event
     */
    protected function recordEvent(DomainEventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * Get all recorded events and clear them
     *
     * @return array<DomainEventInterface>
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    /**
     * Get all pending events
     *
     * @return array<DomainEventInterface>
     */
    public function getPendingEvents(): array
    {
        return $this->events;
    }

    /**
     * Clear all pending events
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }

    /**
     * Record delete event (called before deletion)
     */
    public function recordDeleteEvent(): void
    {
        $this->recordEvent(new ArticleDeleted($this->id, $this->title));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author_id' => $this->authorId,
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'slug' => $this->slug->value(),
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->image,
            'status' => $this->status->value(),
            'views' => $this->views,
            'published_at' => $this->publishedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'tags' => $this->tags,
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
