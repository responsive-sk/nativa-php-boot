<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Slug;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class SlugTest extends TestCase
{
    public function testValidSlug(): void
    {
        $slug = new Slug('my-valid-slug');
        self::assertSame('my-valid-slug', $slug->value());
    }

    public function testInvalidSlug(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Slug('Invalid Slug With Spaces');
    }

    public function testFromStringWithSpaces(): void
    {
        $slug = Slug::fromString('My Article Title');
        self::assertSame('my-article-title', $slug->value());
    }

    public function testFromStringWithSpecialChars(): void
    {
        $slug = Slug::fromString('Article with special chars!');
        self::assertSame('article-with-special-chars', $slug->value());
    }

    public function testFromStringWithMultipleSpaces(): void
    {
        $slug = Slug::fromString('Multiple   Spaces   Here');
        self::assertSame('multiple-spaces-here', $slug->value());
    }

    public function testFromStringWithLeadingTrailingSpaces(): void
    {
        $slug = Slug::fromString('  Trimmed Slug  ');
        self::assertSame('trimmed-slug', $slug->value());
    }

    public function testToString(): void
    {
        $slug = new Slug('test-slug');
        self::assertSame('test-slug', (string) $slug);
    }
}
