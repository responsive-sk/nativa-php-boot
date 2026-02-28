<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Article Updated Event
 */
final class ArticleUpdated extends DomainEvent
{
    public function __construct(
        private readonly string $articleId,
        private readonly string $title,
        private readonly array $changes,
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

    public function changes(): array
    {
        return $this->changes;
    }

    public function payload(): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
            'changes' => $this->changes,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
