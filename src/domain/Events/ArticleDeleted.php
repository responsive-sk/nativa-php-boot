<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Article Deleted Event
 */
class ArticleDeleted extends DomainEvent
{
    public function __construct(
        private readonly string $articleId,
        private readonly string $title,
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

    public function payload(): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
