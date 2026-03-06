<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\ArticleStatus;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\ArticleStatus
 *
 * @internal
 */
final class ArticleStatusTest extends TestCase
{
    public function testCreateDraftStatus(): void
    {
        $status = ArticleStatus::draft();

        self::assertTrue($status->isDraft());
        self::assertFalse($status->isPublished());
        self::assertFalse($status->isArchived());
    }

    public function testCreatePublishedStatus(): void
    {
        $status = ArticleStatus::published();

        self::assertFalse($status->isDraft());
        self::assertTrue($status->isPublished());
        self::assertFalse($status->isArchived());
    }

    public function testCreateArchivedStatus(): void
    {
        $status = ArticleStatus::archived();

        self::assertFalse($status->isDraft());
        self::assertFalse($status->isPublished());
        self::assertTrue($status->isArchived());
    }

    public function testCanTransitionFromDraft(): void
    {
        $draft = ArticleStatus::draft();

        self::assertTrue($draft->canTransitionTo(ArticleStatus::published()));
        self::assertTrue($draft->canTransitionTo(ArticleStatus::archived()));
        self::assertFalse($draft->canTransitionTo(ArticleStatus::draft()));
    }

    public function testCanTransitionFromPublished(): void
    {
        $published = ArticleStatus::published();

        self::assertTrue($published->canTransitionTo(ArticleStatus::archived()));
        self::assertFalse($published->canTransitionTo(ArticleStatus::draft()));
        self::assertFalse($published->canTransitionTo(ArticleStatus::published()));
    }

    public function testCanTransitionFromArchived(): void
    {
        $archived = ArticleStatus::archived();

        self::assertFalse($archived->canTransitionTo(ArticleStatus::draft()));
        self::assertFalse($archived->canTransitionTo(ArticleStatus::published()));
        self::assertFalse($archived->canTransitionTo(ArticleStatus::archived()));
    }
}
