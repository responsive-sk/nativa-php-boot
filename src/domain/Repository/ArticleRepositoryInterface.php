<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Article;

/**
 * Article Repository Interface
 */
interface ArticleRepositoryInterface
{
    public function save(Article $article): void;

    public function delete(string $id): void;

    public function findById(string $id): ?Article;

    public function findBySlug(string $slug): ?Article;

    public function findByAuthorId(string $authorId): array;

    public function findByCategoryId(string $categoryId): array;

    public function findByTag(string $tag): array;

    public function findPublished(int $limit = 10, int $offset = 0): array;

    public function findLatest(int $limit = 5): array;

    public function search(string $query): array;

    public function count(): int;

    public function countPublished(): int;
}
