<?php

declare(strict_types=1);

namespace Tests\Domain\Model;

use Domain\Model\Article;
use Domain\ValueObjects\ArticleStatus;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Slug;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\Model\Article
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

        $this->assertEquals('Test Article', $article->title());
        $this->assertEquals('test-article', (string) $article->slug());
        $this->assertEquals('Test content', $article->content());
        $this->assertEquals('author-123', $article->authorId());
        $this->assertTrue($article->status()->isDraft());
    }

    public function testPublishArticle(): void
    {
        $article = Article::create(
            title: 'Draft Article',
            content: 'Content',
            authorId: 'author-123',
        );

        $article->publish();

        $this->assertTrue($article->status()->isPublished());
        $this->assertNotNull($article->publishedAt());
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

        $this->assertTrue($article->status()->isDraft());
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

        $this->assertTrue($article->status()->isArchived());
    }
}
