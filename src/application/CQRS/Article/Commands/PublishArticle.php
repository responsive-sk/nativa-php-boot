<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Commands;

use Application\CQRS\CommandInterface;

/**
 * Publish Article Command
 */
class PublishArticle implements CommandInterface
{
    public function __construct(
        public readonly string $articleId,
    ) {
    }
}
