<?php

declare(strict_types = 1);

namespace Tests\Domain\Model;

use Domain\Model\Article;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ArticleTest extends TestCase
{
    public function testCreateArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123',
            excerpt: 'Test excerpt'
        );

        self::assertSame('Test Article', $article->title());
        self::assertSame('test-article', $article->slug());
        self::assertSame('Test content', $article->content());
        self::assertSame('author-123', $article->authorId());
        self::assertTrue($article->isDraft());
    }

    public function testPublishArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        self::assertTrue($article->isDraft());

        $article->publish();

        self::assertTrue($article->isPublished());
        self::assertNotNull($article->publishedAt());
    }

    public function testUpdateArticleTitle(): void
    {
        $article = Article::create(
            title: 'Original Title',
            content: 'Test content',
            authorId: 'author-123'
        );

        $article->update(title: 'Updated Title');

        self::assertSame('Updated Title', $article->title());
        self::assertSame('updated-title', $article->slug());
    }

    public function testUpdateArticleContent(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Original content',
            authorId: 'author-123'
        );

        $article->update(content: 'Updated content');

        self::assertSame('Updated content', $article->content());
    }

    public function testUnpublishArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        $article->publish();
        self::assertTrue($article->isPublished());

        $article->unpublish();
        self::assertTrue($article->isDraft());
        self::assertNull($article->publishedAt());
    }

    public function testArticleToArray(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123',
            excerpt: 'Test excerpt'
        );

        $array = $article->toArray();

        self::assertSame('Test Article', $array['title']);
        self::assertSame('test-article', $array['slug']);
        self::assertSame('draft', $array['status']);
        self::assertArrayHasKey('id', $array);
        self::assertArrayHasKey('created_at', $array);
    }
}
