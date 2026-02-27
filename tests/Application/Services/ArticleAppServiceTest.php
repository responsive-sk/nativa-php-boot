<?php

declare(strict_types=1);

namespace Tests\Application\Services;

use PHPUnit\Framework\TestCase;
use Application\Services\ArticleAppService;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\ArticleRepository;

class ArticleAppServiceTest extends TestCase
{
    private ArticleAppService $service;
    private DatabaseConnection $db;
    private UnitOfWork $uow;

    protected function setUp(): void
    {
        $this->db = new DatabaseConnection(':memory:');
        $this->uow = new UnitOfWork($this->db);
        
        // Create tables
        $conn = $this->db->getConnection();
        $conn->exec(<<<SQL
            CREATE TABLE articles (
                id VARCHAR(36) PRIMARY KEY,
                author_id VARCHAR(36),
                category_id VARCHAR(36),
                title VARCHAR(255),
                slug VARCHAR(255) UNIQUE,
                excerpt TEXT,
                content TEXT,
                image VARCHAR(255),
                status VARCHAR(50) DEFAULT 'draft',
                views INTEGER DEFAULT 0,
                published_at DATETIME,
                created_at DATETIME,
                updated_at DATETIME
            )
        SQL);
        
        $conn->exec(<<<SQL
            CREATE TABLE tags (
                id VARCHAR(36) PRIMARY KEY,
                name VARCHAR(255),
                slug VARCHAR(255) UNIQUE
            )
        SQL);
        
        $conn->exec(<<<SQL
            CREATE TABLE article_tag (
                article_id VARCHAR(36),
                tag_id VARCHAR(36),
                PRIMARY KEY (article_id, tag_id)
            )
        SQL);

        $repo = new ArticleRepository($this->uow);
        $this->service = new ArticleAppService($repo);
    }

    public function testCreateArticle(): void
    {
        $article = $this->service->create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123',
            excerpt: 'Test excerpt'
        );

        $this->assertSame('Test Article', $article->title());
        $this->assertSame('test-article', $article->slug());
        $this->assertTrue($article->isDraft());
    }

    public function testPublishArticle(): void
    {
        $article = $this->service->create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        $published = $this->service->publish($article->id());
        
        $this->assertTrue($published->isPublished());
    }

    public function testUpdateArticle(): void
    {
        $article = $this->service->create(
            title: 'Original Title',
            content: 'Original content',
            authorId: 'author-123'
        );

        $updated = $this->service->update(
            articleId: $article->id(),
            title: 'Updated Title',
            content: 'Updated content'
        );
        
        $this->assertSame('Updated Title', $updated->title());
        $this->assertSame('Updated content', $updated->content());
    }

    public function testFindArticleBySlug(): void
    {
        $article = $this->service->create(
            title: 'Test Article',
            content: 'Test content',
            authorId: 'author-123'
        );

        $found = $this->service->findBySlug('test-article');
        
        $this->assertNotNull($found);
        $this->assertSame('test-article', $found->slug());
    }

    public function testListPublishedArticles(): void
    {
        // Create draft article
        $this->service->create(
            title: 'Draft Article',
            content: 'Draft content',
            authorId: 'author-123'
        );

        // Create published article
        $article = $this->service->create(
            title: 'Published Article',
            content: 'Published content',
            authorId: 'author-123'
        );
        $this->service->publish($article->id());

        $published = $this->service->listPublished();
        
        $this->assertCount(1, $published);
        $this->assertSame('Published Article', $published[0]->title());
    }

    public function testDeleteArticle(): void
    {
        $article = $this->service->create(
            title: 'To Delete',
            content: 'Content',
            authorId: 'author-123'
        );

        $this->service->delete($article->id());
        
        $found = $this->service->findById($article->id());
        $this->assertNull($found);
    }
}
