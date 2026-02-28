<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Page;
use Domain\Model\PageBlock;
use Domain\Model\PageMedia;
use Domain\Model\PageForm;
use Domain\Repository\PageRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;
use PDO;

/**
 * Page Repository Implementation
 */
 final class PageRepository implements PageRepositoryInterface
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

    public function findById(string $id): ?Page
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Page::fromArray($data);
    }

    public function findBySlug(string $slug): ?Page
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM pages WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Page::fromArray($data);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM pages
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Page::fromArray($row), $stmt->fetchAll());
    }

    public function findPublished(int $limit = 50): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM pages
            WHERE is_published = TRUE
            ORDER BY created_at DESC
            LIMIT :limit
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Page::fromArray($row), $stmt->fetchAll());
    }

    public function delete(string $id): bool
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM pages WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM pages');
        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<\Domain\Model\PageBlock>
     */
    public function getBlocks(string $pageId): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM page_blocks
            WHERE page_id = :page_id AND is_active = TRUE
            ORDER BY sort_order ASC
        SQL);
        $stmt->execute([':page_id' => $pageId]);
        return array_map(fn($row) => PageBlock::fromArray($row), $stmt->fetchAll());
    }

    /**
     * @return array<\Domain\Model\PageMedia>
     */
    public function getMedia(string $pageId): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT pm.*, m.url, m.mime_type, m.size
            FROM page_media pm
            INNER JOIN media m ON pm.media_id = m.id
            WHERE pm.page_id = :page_id
            ORDER BY pm.sort_order ASC
        SQL);
        $stmt->execute([':page_id' => $pageId]);
        return array_map(fn($row) => PageMedia::fromArray($row), $stmt->fetchAll());
    }

    /**
     * @return array<\Domain\Model\PageForm>
     */
    public function getForms(string $pageId): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT pf.*, f.name as form_name, f.slug as form_slug
            FROM page_forms pf
            INNER JOIN forms f ON pf.form_id = f.id
            WHERE pf.page_id = :page_id
            ORDER BY pf.sort_order ASC
        SQL);
        $stmt->execute([':page_id' => $pageId]);
        return array_map(fn($row) => PageForm::fromArray($row), $stmt->fetchAll());
    }
}
