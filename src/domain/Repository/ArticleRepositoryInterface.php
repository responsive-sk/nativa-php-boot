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

    /**
     * @return array<int, Article>
     */
    public function findByAuthorId(string $authorId): array;

    /**
     * @return array<int, Article>
     */
    public function findByCategoryId(string $categoryId): array;

    /**
     * @return array<int, Article>
     */
    public function findByTag(string $tag, int $limit = 10, int $offset = 0): array;

    /**
     * @return array<int, Article>
     */
    public function findPublished(int $limit = 10, int $offset = 0): array;

    /**
     * @return array<int, Article>
     */
    public function findLatest(int $limit = 5): array;

    /**
     * @return array<int, Article>
     */
    public function search(string $query): array;

    public function count(): int;

    public function countPublished(): int;

    /**
     * Find published articles by category ID
     *
     * @param string $categoryId Category ID
     * @param int $limit Maximum results
     * @return array<int, Article>
     */
    public function findByCategory(string $categoryId, int $limit = 10): array;

    /**
     * Find published articles by any of the given tag slugs
     *
     * @param array<int, string> $tagSlugs Tag slugs to search for
     * @param int $limit Maximum results
     * @return array<int, Article>
     */
    public function findByAnyTag(array $tagSlugs, int $limit = 10): array;

    /**
     * Increment view count for an article
     */
    public function incrementViewCount(string $articleId): void;
}
