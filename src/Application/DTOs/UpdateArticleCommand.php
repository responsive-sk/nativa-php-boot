<?php

declare(strict_types = 1);

namespace Application\DTOs;

use Application\Exceptions\ValidationException;
use Application\Validation\Validator;

/**
 * Update Article Command DTO.
 */
final class UpdateArticleCommand
{
    public function __construct(
        public readonly string $articleId,
        public readonly ?string $title = null,
        public readonly ?string $content = null,
        public readonly ?string $excerpt = null,
        public readonly ?string $categoryId = null,
        public readonly ?string $image = null,
        /** @var array<string>|null */
        public readonly ?array $tags = null,
    ) {
        $this->validate();
    }

    /**
     * Convert to array for Article::update().
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title'      => $this->title,
            'content'    => $this->content,
            'excerpt'    => $this->excerpt,
            'categoryId' => $this->categoryId,
            'image'      => $this->image,
            'tags'       => $this->tags,
        ];
    }

    /**
     * Validate the command data.
     *
     * @throws ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'articleId' => $this->articleId,
            'title'     => $this->title,
            'content'   => $this->content,
            'excerpt'   => $this->excerpt,
            'image'     => $this->image,
        ], [
            'articleId' => ['required', 'uuid'],
            'title'     => ['min:3', 'max:255'],
            'content'   => ['min:10'],
            'excerpt'   => ['max:500'],
            'image'     => ['max:500', 'url'],
        ]);

        // Validate tags if provided
        if (null !== $this->tags) {
            foreach ($this->tags as $index => $tag) {
                /** @var string $tag */
                if (\strlen($tag) < 2 || \strlen($tag) > 50) {
                    throw new ValidationException(
                        ['tags' => ["Tag at index {$index} must be between 2 and 50 characters"]],
                        'Validation failed'
                    );
                }
            }
        }
    }
}
