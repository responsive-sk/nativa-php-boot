<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Queries;

use Application\CQRS\QueryInterface;

/**
 * Get Article By Slug Query
 */
class GetArticleBySlug implements QueryInterface
{
    public function __construct(
        public readonly string $slug,
    ) {
    }
}
