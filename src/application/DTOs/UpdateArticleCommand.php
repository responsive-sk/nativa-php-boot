<?php

declare(strict_types=1);

namespace Application\DTOs;

use Application\Validation\Validator;

/**
 * Update Article Command DTO
 */
class UpdateArticleCommand
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
     * Validate the command data
     *
     * @throws \Application\Exceptions\ValidationException
     */
    private function validate(): void
    {
        Validator::validate([
            'articleId' => $this->articleId,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
        ], [
            'articleId' => ['required', 'uuid'],
            'title' => ['min:3', 'max:255'],
            'content' => ['min:10'],
            'excerpt' => ['max:500'],
            'image' => ['max:500', 'url'],
        ]);

        // Validate tags if provided
        if ($this->tags !== null) {
            foreach ($this->tags as $index => $tag) {
                if (!is_string($tag) || strlen($tag) < 2 || strlen($tag) > 50) {
                    throw new \Application\Exceptions\ValidationException(
                        ['tags' => ["Tag at index {$index} must be between 2 and 50 characters"]],
                        'Validation failed'
                    );
                }
            }
        }
    }

    /**
     * Convert to array for Article::update()
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'categoryId' => $this->categoryId,
            'image' => $this->image,
            'tags' => $this->tags,
        ];
    }
}
