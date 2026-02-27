<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Page;
use Domain\Repository\PageRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Page Repository Implementation
 */
class PageRepository implements PageRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(Page $page): void
    {
        $data = $page->toArray();

        $sql = <<<SQL
            INSERT INTO pages (
                id, title, slug, content, template, meta_title,
                meta_description, is_published, created_at, updated_at
            ) VALUES (
                :id, :title, :slug, :content, :template, :meta_title,
                :meta_description, :is_published, :created_at, :updated_at
            )
            ON CONFLICT(id) DO UPDATE SET
                title = excluded.title,
                slug = excluded.slug,
                content = excluded.content,
                template = excluded.template,
                meta_title = excluded.meta_title,
                meta_description = excluded.meta_description,
                is_published = excluded.is_published,
                updated_at = excluded.updated_at
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM pages WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findById(string $id): ?Page
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? Page::fromArray($data) : null;
    }

    public function findBySlug(string $slug): ?Page
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM pages WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? Page::fromArray($data) : null;
    }

    public function findPublished(): array
    {
        $stmt = $this->uow->getConnection()->query(
            "SELECT * FROM pages WHERE is_published = 1 ORDER BY title"
        );

        return array_map(fn($row) => Page::fromArray($row), $stmt->fetchAll());
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM pages ORDER BY created_at DESC');
        return array_map(fn($row) => Page::fromArray($row), $stmt->fetchAll());
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM pages');
        return (int) $stmt->fetchColumn();
    }
}
