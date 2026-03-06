<?php

declare(strict_types = 1);

namespace Tests\Domain\Model;

use Domain\Model\Article;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\Model\Article
 *
 * @internal
 */
final class ArticleTest extends TestCase
{
    public function testCreateArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123',
        );

        self::assertSame('Test Article', $article->title());
        self::assertSame('test-article', (string) $article->slug());
        self::assertSame('Test content', $article->content());
        self::assertSame('author-123', $article->authorId());
        self::assertTrue($article->status()->isDraft());
    }

    public function testPublishArticle(): void
    {
        $article = Article::create(
            title: 'Draft Article',
            content: 'Content',
            authorId: 'author-123',
        );

        $article->publish();

        self::assertTrue($article->status()->isPublished());
        self::assertNotNull($article->publishedAt());
    }

    public function testUnpublishArticle(): void
    {
        $article = Article::create(
            title: 'Published Article',
            content: 'Content',
            authorId: 'author-123',
        );
        $article->publish();

        $article->unpublish();

        self::assertTrue($article->status()->isDraft());
    }

    public function testArchiveArticle(): void
    {
        $article = Article::create(
            title: 'Article to Archive',
            content: 'Content',
            authorId: 'author-123',
        );
        $article->publish();

        $article->archive();

        self::assertTrue($article->status()->isArchived());
    }
}
