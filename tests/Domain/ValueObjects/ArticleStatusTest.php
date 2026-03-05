<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\ArticleStatus;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\ArticleStatus
 */
final class ArticleStatusTest extends TestCase
{
    public function testCreateDraftStatus(): void
    {
        $status = ArticleStatus::draft();

        $this->assertTrue($status->isDraft());
        $this->assertFalse($status->isPublished());
        $this->assertFalse($status->isArchived());
    }

    public function testCreatePublishedStatus(): void
    {
        $status = ArticleStatus::published();

        $this->assertFalse($status->isDraft());
        $this->assertTrue($status->isPublished());
        $this->assertFalse($status->isArchived());
    }

    public function testCreateArchivedStatus(): void
    {
        $status = ArticleStatus::archived();

        $this->assertFalse($status->isDraft());
        $this->assertFalse($status->isPublished());
        $this->assertTrue($status->isArchived());
    }

    public function testCanTransitionFromDraft(): void
    {
        $draft = ArticleStatus::draft();

        $this->assertTrue($draft->canTransitionTo(ArticleStatus::published()));
        $this->assertTrue($draft->canTransitionTo(ArticleStatus::archived()));
        $this->assertFalse($draft->canTransitionTo(ArticleStatus::draft()));
    }

    public function testCanTransitionFromPublished(): void
    {
        $published = ArticleStatus::published();

        $this->assertTrue($published->canTransitionTo(ArticleStatus::archived()));
        $this->assertFalse($published->canTransitionTo(ArticleStatus::draft()));
        $this->assertFalse($published->canTransitionTo(ArticleStatus::published()));
    }

    public function testCanTransitionFromArchived(): void
    {
        $archived = ArticleStatus::archived();

        $this->assertFalse($archived->canTransitionTo(ArticleStatus::draft()));
        $this->assertFalse($archived->canTransitionTo(ArticleStatus::published()));
        $this->assertFalse($archived->canTransitionTo(ArticleStatus::archived()));
    }
}
