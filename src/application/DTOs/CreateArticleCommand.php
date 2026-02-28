<?php

declare(strict_types=1);

namespace Application\DTOs;

use Application\Validation\Validator;

/**
 * Create Article Command DTO
 *
 * @phpstan-consistent-constructor
 */
class CreateArticleCommand
{
    /**
     * @param array<string>|null $tags
     */
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly string $authorId,
        public readonly ?string $categoryId = null,
        public readonly ?string $excerpt = null,
        public readonly ?string $image = null,
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
            'title' => $this->title,
            'content' => $this->content,
            'authorId' => $this->authorId,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
        ], [
            'title' => ['required', 'min:3', 'max:255'],
            'content' => ['required', 'min:10'],
            'authorId' => ['required', 'uuid'],
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
     * Convert to array for Article::create()
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'authorId' => $this->authorId,
            'categoryId' => $this->categoryId,
            'excerpt' => $this->excerpt,
            'image' => $this->image,
            'tags' => $this->tags,
        ];
    }
}
