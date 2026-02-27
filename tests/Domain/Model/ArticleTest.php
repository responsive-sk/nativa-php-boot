<?php

declare(strict_types=1);

namespace Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use Domain\Model\Article;
use Domain\ValueObjects\ArticleStatus;

class ArticleTest extends TestCase
{
    public function testCreateArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123',
            excerpt: 'Test excerpt'
        );

        $this->assertSame('Test Article', $article->title());
        $this->assertSame('test-article', $article->slug());
        $this->assertSame('Test content', $article->content());
        $this->assertSame('author-123', $article->authorId());
        $this->assertTrue($article->isDraft());
    }

    public function testPublishArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        $this->assertTrue($article->isDraft());
        
        $article->publish();
        
        $this->assertTrue($article->isPublished());
        $this->assertNotNull($article->publishedAt());
    }

    public function testUpdateArticleTitle(): void
    {
        $article = Article::create(
            title: 'Original Title',
            content: 'Test content',
            authorId: 'author-123'
        );

        $article->update(title: 'Updated Title');
        
        $this->assertSame('Updated Title', $article->title());
        $this->assertSame('updated-title', $article->slug());
    }

    public function testUpdateArticleContent(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Original content',
            authorId: 'author-123'
        );

        $article->update(content: 'Updated content');
        
        $this->assertSame('Updated content', $article->content());
    }

    public function testUnpublishArticle(): void
    {
        $article = Article::create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        $article->publish();
        $this->assertTrue($article->isPublished());
        
        $article->unpublish();
        $this->assertTrue($article->isDraft());
        $this->assertNull($article->publishedAt());
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
        
        $this->assertSame('Test Article', $array['title']);
        $this->assertSame('test-article', $array['slug']);
        $this->assertSame('draft', $array['status']);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('created_at', $array);
    }
}
