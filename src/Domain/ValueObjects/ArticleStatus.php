<?php

declare(strict_types = 1);

namespace Domain\ValueObjects;

/**
 * Article Status Enum.
 *
 * Represents the publication status of an article.
 */
enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    /**
     * Check if status can transition to new status.
     */
    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::DRAFT     => \in_array($newStatus, [self::PUBLISHED, self::ARCHIVED], true),
            self::PUBLISHED => self::ARCHIVED === $newStatus,
            self::ARCHIVED  => false,
        };
    }

    /**
     * Check if status is draft.
     */
    public function isDraft(): bool
    {
        return self::DRAFT === $this;
    }

    /**
     * Check if status is published.
     */
    public function isPublished(): bool
    {
        return self::PUBLISHED === $this;
    }

    /**
     * Check if status is archived.
     */
    public function isArchived(): bool
    {
        return self::ARCHIVED === $this;
    }

    /**
     * Create draft status.
     */
    public static function draft(): self
    {
        return self::DRAFT;
    }

    /**
     * Create published status.
     */
    public static function published(): self
    {
        return self::PUBLISHED;
    }

    /**
     * Create archived status.
     */
    public static function archived(): self
    {
        return self::ARCHIVED;
    }
}
