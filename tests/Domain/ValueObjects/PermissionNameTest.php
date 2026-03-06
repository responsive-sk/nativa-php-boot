<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\PermissionName;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\PermissionName
 *
 * @internal
 */
final class PermissionNameTest extends TestCase
{
    public function testCreatePermissionName(): void
    {
        $permission = PermissionName::fromString('articles.create');

        self::assertSame('articles.create', $permission->name());
        self::assertSame('articles.create', (string) $permission);
    }

    public function testCreatePermissionNameWithMultipleLevels(): void
    {
        $permission = PermissionName::fromString('admin.articles.create');

        self::assertSame('admin.articles.create', $permission->name());
    }

    public function testCreatePermissionNameWithInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PermissionName::fromString('invalid name with spaces');
    }

    public function testCreatePermissionNameWithEmptyString(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PermissionName::fromString('');
    }

    public function testPermissionNameEquals(): void
    {
        $permission1 = PermissionName::fromString('articles.create');
        $permission2 = PermissionName::fromString('articles.create');

        self::assertSame($permission1->name(), $permission2->name());
    }

    public function testPermissionNameNotEquals(): void
    {
        $permission1 = PermissionName::fromString('articles.create');
        $permission2 = PermissionName::fromString('articles.delete');

        self::assertNotSame($permission1->name(), $permission2->name());
    }
}
