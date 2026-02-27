<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use Domain\ValueObjects\Slug;

class SlugTest extends TestCase
{
    public function testValidSlug(): void
    {
        $slug = new Slug('my-valid-slug');
        $this->assertSame('my-valid-slug', $slug->value());
    }

    public function testInvalidSlug(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Slug('Invalid Slug With Spaces');
    }

    public function testFromStringWithSpaces(): void
    {
        $slug = Slug::fromString('My Article Title');
        $this->assertSame('my-article-title', $slug->value());
    }

    public function testFromStringWithSpecialChars(): void
    {
        $slug = Slug::fromString('Article with special chars!');
        $this->assertSame('article-with-special-chars', $slug->value());
    }

    public function testFromStringWithMultipleSpaces(): void
    {
        $slug = Slug::fromString('Multiple   Spaces   Here');
        $this->assertSame('multiple-spaces-here', $slug->value());
    }

    public function testFromStringWithLeadingTrailingSpaces(): void
    {
        $slug = Slug::fromString('  Trimmed Slug  ');
        $this->assertSame('trimmed-slug', $slug->value());
    }

    public function testToString(): void
    {
        $slug = new Slug('test-slug');
        $this->assertSame('test-slug', (string) $slug);
    }
}
