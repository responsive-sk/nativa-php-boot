<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Article Created Event
 */
class ArticleCreated extends DomainEvent
{
    public function __construct(
        private readonly string $articleId,
        private readonly string $title,
        private readonly string $authorId,
        private readonly ?string $categoryId = null,
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

    public function authorId(): string
    {
        return $this->authorId;
    }

    public function categoryId(): ?string
    {
        return $this->categoryId;
    }

    public function payload(): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
            'author_id' => $this->authorId,
            'category_id' => $this->categoryId,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
