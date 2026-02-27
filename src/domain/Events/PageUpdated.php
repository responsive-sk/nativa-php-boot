<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Page Updated Event
 */
class PageUpdated extends DomainEvent
{
    public function __construct(
        private readonly string $pageId,
        private readonly string $title,
    ) {
        parent::__construct();
    }

    public function pageId(): string
    {
        return $this->pageId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function payload(): array
    {
        return [
            'page_id' => $this->pageId,
            'title' => $this->title,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
