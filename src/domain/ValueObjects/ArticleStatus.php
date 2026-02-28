<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Article Status Value Object
 */
final class ArticleStatus
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const ARCHIVED = 'archived';

    private const VALID_STATUSES = [
        self::DRAFT,
        self::PUBLISHED,
        self::ARCHIVED,
    ];

    public function __construct(
        private readonly string $value
    ) {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException('Invalid article status: ' . $value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isDraft(): bool
    {
        return $this->value === self::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->value === self::PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->value === self::ARCHIVED;
    }

    public function canTransitionTo(self $newStatus): bool
    {
        // Cannot transition from archived to draft or published
        if ($this->isArchived() && !$newStatus->isArchived()) {
            return false;
        }

        return true;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function archived(): self
    {
        return new self(self::ARCHIVED);
    }
}
