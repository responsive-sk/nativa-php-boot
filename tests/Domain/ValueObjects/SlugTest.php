<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Slug;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Slug
 *
 * @internal
 */
final class SlugTest extends TestCase
{
    public function testCreateSlugFromString(): void
    {
        $slug = Slug::fromString('Hello World');

        self::assertSame('hello-world', (string) $slug);
    }

    public function testCreateSlugWithSpecialCharacters(): void
    {
        $slug = Slug::fromString('Hello & World! @#$%');

        self::assertSame('hello-world', (string) $slug);
    }

    public function testCreateSlugWithAccentedCharacters(): void
    {
        $slug = Slug::fromString('Ceska Kava');

        self::assertSame('ceska-kava', (string) $slug);
    }

    public function testCreateSlugWithNumbers(): void
    {
        $slug = Slug::fromString('Article 123');

        self::assertSame('article-123', (string) $slug);
    }

    public function testCreateSlugWithMultipleSpaces(): void
    {
        $slug = Slug::fromString('Hello    World');

        self::assertSame('hello-world', (string) $slug);
    }

    public function testCreateSlugWithUppercase(): void
    {
        $slug = Slug::fromString('HELLO WORLD');

        self::assertSame('hello-world', (string) $slug);
    }

    public function testCreateSlugWithDashes(): void
    {
        $slug = Slug::fromString('Hello-World-Test');

        self::assertSame('hello-world-test', (string) $slug);
    }

    public function testCreateSlugWithUnderscores(): void
    {
        $slug = Slug::fromString('Hello_World_Test');

        self::assertSame('helloworldtest', (string) $slug);
    }

    public function testValueMethod(): void
    {
        $slug = Slug::fromString('Test Slug');

        self::assertSame('test-slug', $slug->value());
    }

    public function testEmptyStringCreatesEmptySlug(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create slug from empty content');

        Slug::fromString('');
    }

    public function testSlugWithOnlySpecialCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create slug from empty content');

        Slug::fromString('!@#$%^&*()');
    }
}
