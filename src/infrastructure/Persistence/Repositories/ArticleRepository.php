<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Article;
use Domain\Repository\ArticleRepositoryInterface;
use Infrastructure\Persistence\StatementExecutor;
use Infrastructure\Persistence\UnitOfWork;
use PDO;

/**
 * Article Repository Implementation
 */
 final class ArticleRepository implements ArticleRepositoryInterface
{
    use StatementExecutor;

    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    protected function getConnection(): PDO
    {
        return $this->uow->getConnection();
    }

    #[\Override]
    public function save(Article $article): void
    {
        $data = $article->toArray();
        unset($data['tags']); // Handle tags separately

        $sql = <<<SQL
            INSERT INTO articles (
                id, author_id, category_id, title, slug, excerpt, content,
                image, status, views, published_at, created_at, updated_at
            ) VALUES (
                :id, :author_id, :category_id, :title, :slug, :excerpt,
                :content, :image, :status, :views, :published_at,
                :created_at, :updated_at
            )
            ON CONFLICT(id) DO UPDATE SET
                author_id = excluded.author_id,
                category_id = excluded.category_id,
                title = excluded.title,
                slug = excluded.slug,
                excerpt = excluded.excerpt,
                content = excluded.content,
                image = excluded.image,
                status = excluded.status,
                views = excluded.views,
                published_at = excluded.published_at,
                updated_at = excluded.updated_at
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        assert($stmt !== false, 'Failed to prepare SQL statement');
        $stmt->execute($data);

        // Handle tags
        $this->saveTags($article->id(), $article->tags());
    }

    #[\Override]
    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM articles WHERE id = ?');
        assert($stmt !== false, 'Failed to prepare SQL statement');
        $stmt->execute([$id]);
    }

    #[\Override]
    public function findById(string $id): ?Article
    {
        $data = $this->fetchOne('SELECT * FROM articles WHERE id = ?', [$id]);
        
        if ($data === null) {
            return null;
        }

        /** @var array<string, mixed> $data */
        $data['tags'] = $this->getTags($id);
        return Article::fromArray($data);
    }

    #[\Override]
    public function findBySlug(string $slug): ?Article
    {
        $data = $this->fetchOne('SELECT * FROM articles WHERE slug = ?', [$slug]);
        
        if ($data === null) {
            return null;
        }

        /** @var array<string, mixed> $data */
        $data['tags'] = $this->getTags($data['id']);
        return Article::fromArray($data);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function findByAuthorId(string $authorId): array
    {
        $rows = $this->fetchAll(
            'SELECT * FROM articles WHERE author_id = ? ORDER BY created_at DESC',
            [$authorId]
        );

        return array_map(function (array $row) {
            /** @var array<string, mixed> $row */
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $rows);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function findByCategoryId(string $categoryId): array
    {
        $rows = $this->fetchAll(
            'SELECT * FROM articles WHERE category_id = ? ORDER BY created_at DESC',
            [$categoryId]
        );

        return array_map(function (array $row) {
            /** @var array<string, mixed> $row */
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $rows);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function findByTag(string $tag): array
    {
        $sql = <<<SQL
            SELECT a.* FROM articles a
            INNER JOIN article_tag at ON a.id = at.article_id
            INNER JOIN tags t ON at.tag_id = t.id
            WHERE t.slug = ?
            ORDER BY a.created_at DESC
        SQL;

        $rows = $this->fetchAll($sql, [$tag]);

        return array_map(function (array $row) {
            /** @var array<string, mixed> $row */
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $rows);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function findPublished(int $limit = 10, int $offset = 0): array
    {
        // SQLite doesn't support bound parameters for LIMIT/OFFSET
        // Sanitize inputs to prevent SQL injection
        $limit = max(1, min(100, (int) $limit));
        $offset = max(0, (int) $offset);

        $sql = <<<SQL
            SELECT * FROM articles
            WHERE status = 'published'
            ORDER BY published_at DESC
            LIMIT $limit OFFSET $offset
        SQL;

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return array_map(function (array $row) {
            /** @var array<string, mixed> $row */
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $rows);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function findLatest(int $limit = 5): array
    {
        return array_slice($this->findPublished($limit), 0, $limit);
    }

    /**
     * @return array<int, Article>
     */
    #[\Override]
    public function search(string $query): array
    {
        $searchTerm = "%{$query}%";
        $sql = <<<SQL
            SELECT * FROM articles
            WHERE status = 'published'
            AND (title LIKE :query OR content LIKE :query OR excerpt LIKE :query)
            ORDER BY published_at DESC
        SQL;

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':query', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        return array_map(function (array $row) {
            /** @var array<string, mixed> $row */
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $rows);
    }

    #[\Override]
    public function count(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM articles');
    }

    #[\Override]
    public function countPublished(): int
    {
        return (int) $this->fetchColumn(
            "SELECT COUNT(*) FROM articles WHERE status = 'published'"
        );
    }

    private function saveTags(string $articleId, array $tags): void
    {
        // Delete existing tags
        $this->executeQuery(
            'DELETE FROM article_tag WHERE article_id = ?',
            [$articleId]
        );

        if (empty($tags)) {
            return;
        }

        // Insert new tags
        $sql = <<<SQL
            INSERT INTO article_tag (article_id, tag_id)
            VALUES (:article_id, (SELECT id FROM tags WHERE slug = :tag_slug))
        SQL;

        foreach ($tags as $tagSlug) {
            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute([
                ':article_id' => $articleId,
                ':tag_slug' => $tagSlug,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private function getTags(string $articleId): array
    {
        $sql = <<<SQL
            SELECT t.name FROM tags t
            INNER JOIN article_tag at ON t.id = at.tag_id
            WHERE at.article_id = ?
        SQL;

        $rows = $this->fetchAll($sql, [$articleId]);
        
        /** @var array<int, array{name: string}> $rows */
        return array_column($rows, 'name');
    }
}
