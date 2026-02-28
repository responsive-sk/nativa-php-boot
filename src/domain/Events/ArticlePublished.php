<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Article Published Event
 */
final class ArticlePublished extends DomainEvent
{
    public function __construct(
        private readonly string $articleId,
        private readonly string $title,
        private readonly string $publishedAt,
    ) {
        parent::__construct();
    }

    public function articleId(): string
    {
        return $this->articleId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function publishedAt(): string
    {
        return $this->publishedAt;
    }

    public function payload(): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
            'published_at' => $this->publishedAt,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
