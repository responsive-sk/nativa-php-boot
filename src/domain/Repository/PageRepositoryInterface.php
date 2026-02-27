<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Page;

/**
 * Page Repository Interface
 */
interface PageRepositoryInterface
{
    public function save(Page $page): void;

    public function delete(string $id): void;

    public function findById(string $id): ?Page;

    public function findBySlug(string $slug): ?Page;

    public function findPublished(): array;

    public function findAll(): array;

    public function count(): int;
}
