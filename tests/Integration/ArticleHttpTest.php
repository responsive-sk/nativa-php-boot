<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\ArticleRepository;
use Application\Services\ArticleAppService;

class ArticleHttpTest extends TestCase
{
    private DatabaseConnection $db;
    private ArticleAppService $service;

    protected function setUp(): void
    {
        $this->db = new DatabaseConnection(':memory:');
        $uow = new UnitOfWork($this->db);
        
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

        $repo = new ArticleRepository($uow);
        $this->service = new ArticleAppService($repo);
    }

    public function testCreateAndPublishArticleViaService(): void
    {
        $article = $this->service->create(
            title: 'Integration Test Article',
            content: 'Test content for integration',
            authorId: 'admin'
        );

        $this->assertNotNull($article->id());
        $this->assertSame('integration-test-article', $article->slug());
        $this->assertTrue($article->isDraft());

        // Publish
        $published = $this->service->publish($article->id());
        $this->assertTrue($published->isPublished());
    }

    public function testArticleUpdateChangesSlug(): void
    {
        $article = $this->service->create(
            title: 'Original Title',
            content: 'Content',
            authorId: 'admin'
        );

        $this->assertSame('original-title', $article->slug());

        $updated = $this->service->update(
            articleId: $article->id(),
            title: 'New Title'
        );

        $this->assertSame('new-title', $updated->slug());
    }

    public function testArticleSearch(): void
    {
        $article1 = $this->service->create(
            title: 'PHP Tutorial',
            content: 'Learn PHP programming',
            authorId: 'admin'
        );
        $this->service->publish($article1->id());

        $article2 = $this->service->create(
            title: 'Python Basics',
            content: 'Learn Python programming',
            authorId: 'admin'
        );
        $this->service->publish($article2->id());

        $results = $this->service->search('programming');
        $this->assertCount(2, $results);

        $results = $this->service->search('PHP');
        $this->assertCount(1, $results);
        $this->assertSame('PHP Tutorial', $results[0]->title());
    }
}
