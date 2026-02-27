<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Page;

/**
 * Page Repository Interface
 */
interface PageRepositoryInterface
{
    /**
     * Save page
     */
    public function save(Page $page): void;

    /**
     * Find page by ID
     */
    public function findById(string $id): ?Page;

    /**
     * Find page by slug
     */
    public function findBySlug(string $slug): ?Page;

    /**
     * Find all pages
     *
     * @return array<Page>
     */
    public function findAll(int $limit = 50, int $offset = 0): array;

    /**
     * Find published pages
     *
     * @return array<Page>
     */
    public function findPublished(int $limit = 50): array;

    /**
     * Delete page
     */
    public function delete(string $id): bool;

    /**
     * Count all pages
     */
    public function count(): int;

    /**
     * Get page blocks
     *
     * @return array<PageBlock>
     */
    public function getBlocks(string $pageId): array;

    /**
     * Get page media
     *
     * @return array<PageMedia>
     */
    public function getMedia(string $pageId): array;

    /**
     * Get page forms
     *
     * @return array<PageForm>
     */
    public function getForms(string $pageId): array;
}
