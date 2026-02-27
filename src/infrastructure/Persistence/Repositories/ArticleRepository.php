<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Article;
use Domain\Repository\ArticleRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;
use PDO;

/**
 * Article Repository Implementation
 */
class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

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
        $stmt->execute($data);

        // Handle tags
        $this->saveTags($article->id(), $article->tags());
    }

    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM articles WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findById(string $id): ?Article
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM articles WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $data['tags'] = $this->getTags($id);
        return Article::fromArray($data);
    }

    public function findBySlug(string $slug): ?Article
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM articles WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $data['tags'] = $this->getTags($data['id']);
        return Article::fromArray($data);
    }

    public function findByAuthorId(string $authorId): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM articles WHERE author_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$authorId]);

        return array_map(function ($row) {
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findByCategoryId(string $categoryId): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM articles WHERE category_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$categoryId]);

        return array_map(function ($row) {
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findByTag(string $tag): array
    {
        $sql = <<<SQL
            SELECT a.* FROM articles a
            INNER JOIN article_tag at ON a.id = at.article_id
            INNER JOIN tags t ON at.tag_id = t.id
            WHERE t.slug = ?
            ORDER BY a.created_at DESC
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute([$tag]);

        return array_map(function ($row) {
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findPublished(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM articles
            WHERE status = 'published'
            ORDER BY published_at DESC
            LIMIT :limit OFFSET :offset
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(function ($row) {
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findLatest(int $limit = 5): array
    {
        return array_slice($this->findPublished($limit), 0, $limit);
    }

    public function search(string $query): array
    {
        $searchTerm = "%{$query}%";
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM articles
            WHERE status = 'published'
            AND (title LIKE :query OR content LIKE :query OR excerpt LIKE :query)
            ORDER BY published_at DESC
        SQL);

        $stmt->execute([':query' => $searchTerm]);

        return array_map(function ($row) {
            $row['tags'] = $this->getTags($row['id']);
            return Article::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM articles');
        return (int) $stmt->fetchColumn();
    }

    public function countPublished(): int
    {
        $stmt = $this->uow->getConnection()->query(
            "SELECT COUNT(*) FROM articles WHERE status = 'published'"
        );
        return (int) $stmt->fetchColumn();
    }

    private function saveTags(string $articleId, array $tags): void
    {
        // Delete existing tags
        $stmt = $this->uow->getConnection()->prepare(
            'DELETE FROM article_tag WHERE article_id = ?'
        );
        $stmt->execute([$articleId]);

        if (empty($tags)) {
            return;
        }

        // Insert new tags
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            INSERT INTO article_tag (article_id, tag_id)
            VALUES (:article_id, (SELECT id FROM tags WHERE slug = :tag_slug))
        SQL);

        foreach ($tags as $tagSlug) {
            $stmt->execute([
                ':article_id' => $articleId,
                ':tag_slug' => $tagSlug,
            ]);
        }
    }

    private function getTags(string $articleId): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT t.name FROM tags t
            INNER JOIN article_tag at ON t.id = at.tag_id
            WHERE at.article_id = ?
        SQL);

        $stmt->execute([$articleId]);
        return array_column($stmt->fetchAll(), 'name');
    }
}
