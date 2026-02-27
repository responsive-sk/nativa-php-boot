<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Slug Value Object
 */
class Slug
{
    public function __construct(
        private readonly string $value
    ) {
        if (!$this->isValid($value)) {
            throw new \InvalidArgumentException('Invalid slug format');
        }
    }

    private function isValid(string $value): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $string): self
    {
        // Convert to slug format
        $slug = strtolower(trim($string));
        $slug = str_replace(' ', '-', $slug); // Replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug); // Remove special chars
        $slug = preg_replace('/-+/', '-', $slug); // Replace multiple hyphens with single
        $slug = trim($slug, '-');

        return new self($slug);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
