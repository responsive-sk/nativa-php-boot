<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\PermissionName;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\PermissionName
 */
final class PermissionNameTest extends TestCase
{
    public function testCreatePermissionName(): void
    {
        $permission = PermissionName::fromString('articles.create');

        $this->assertEquals('articles.create', $permission->name());
        $this->assertEquals('articles.create', (string) $permission);
    }

    public function testCreatePermissionNameWithMultipleLevels(): void
    {
        $permission = PermissionName::fromString('admin.articles.create');

        $this->assertEquals('admin.articles.create', $permission->name());
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

        $this->assertEquals($permission1->name(), $permission2->name());
    }

    public function testPermissionNameNotEquals(): void
    {
        $permission1 = PermissionName::fromString('articles.create');
        $permission2 = PermissionName::fromString('articles.delete');

        $this->assertNotEquals($permission1->name(), $permission2->name());
    }
}
