<?php

declare(strict_types=1);

namespace Application\CQRS\Article\Commands;

use Application\CQRS\CommandInterface;

/**
 * Create Article Command
 */
class CreateArticle implements CommandInterface
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly string $authorId,
        public readonly ?string $categoryId = null,
        public readonly ?string $excerpt = null,
        public readonly ?array $tags = null,
        public readonly ?string $image = null,
    ) {
    }
}
