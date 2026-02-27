<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Page Form Entity (Embedded forms on pages)
 */
class PageForm
{
    private string $id;
    private string $pageId;
    private string $formId;
    private ?string $title;
    private string $position; // sidebar, content, bottom
    private int $sortOrder;
    private string $createdAt;

    private function __construct()
    {
    }

    public static function create(
        string $pageId,
        string $formId,
        ?string $title = null,
        string $position = 'sidebar',
        int $sortOrder = 0,
    ): self {
        $pageForm = new self();
        $pageForm->id = self::generateId();
        $pageForm->pageId = $pageId;
        $pageForm->formId = $formId;
        $pageForm->title = $title;
        $pageForm->position = $position;
        $pageForm->sortOrder = $sortOrder;
        $pageForm->createdAt = self::now();

        return $pageForm;
    }

    public static function fromArray(array $data): self
    {
        $pageForm = new self();
        $pageForm->id = $data['id'];
        $pageForm->pageId = $data['page_id'];
        $pageForm->formId = $data['form_id'];
        $pageForm->title = $data['title'] ?? null;
        $pageForm->position = $data['position'] ?? 'sidebar';
        $pageForm->sortOrder = (int) ($data['sort_order'] ?? 0);
        $pageForm->createdAt = $data['created_at'];

        return $pageForm;
    }

    public function update(
        ?string $title = null,
        ?string $position = null,
        ?int $sortOrder = null,
    ): void {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($position !== null) {
            $this->position = $position;
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

    public function formId(): string
    {
        return $this->formId;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function position(): string
    {
        return $this->position;
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
            'form_id' => $this->formId,
            'title' => $this->title,
            'position' => $this->position,
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
