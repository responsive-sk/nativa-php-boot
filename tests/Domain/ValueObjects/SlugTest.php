<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Slug;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Slug
 */
final class SlugTest extends TestCase
{
    public function testCreateSlugFromString(): void
    {
        $slug = Slug::fromString('Hello World');

        $this->assertEquals('hello-world', (string) $slug);
    }

    public function testCreateSlugWithSpecialCharacters(): void
    {
        $slug = Slug::fromString('Hello & World! @#$%');

        $this->assertEquals('hello-world', (string) $slug);
    }

    public function testCreateSlugWithAccentedCharacters(): void
    {
        $slug = Slug::fromString('Ceska Kava');

        $this->assertEquals('ceska-kava', (string) $slug);
    }

    public function testCreateSlugWithNumbers(): void
    {
        $slug = Slug::fromString('Article 123');

        $this->assertEquals('article-123', (string) $slug);
    }

    public function testCreateSlugWithMultipleSpaces(): void
    {
        $slug = Slug::fromString('Hello    World');

        $this->assertEquals('hello-world', (string) $slug);
    }

    public function testCreateSlugWithUppercase(): void
    {
        $slug = Slug::fromString('HELLO WORLD');

        $this->assertEquals('hello-world', (string) $slug);
    }

    public function testCreateSlugWithDashes(): void
    {
        $slug = Slug::fromString('Hello-World-Test');

        $this->assertEquals('hello-world-test', (string) $slug);
    }

    public function testCreateSlugWithUnderscores(): void
    {
        $slug = Slug::fromString('Hello_World_Test');

        $this->assertEquals('helloworldtest', (string) $slug);
    }

    public function testValueMethod(): void
    {
        $slug = Slug::fromString('Test Slug');

        $this->assertEquals('test-slug', $slug->value());
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
