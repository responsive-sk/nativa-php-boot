<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Queries;

use Application\CQRS\QueryInterface;

/**
 * List Published Articles Query
 */
class ListArticles implements QueryInterface
{
    public function __construct(
        public readonly int $limit = 20,
        public readonly int $offset = 0,
    ) {
    }
}
