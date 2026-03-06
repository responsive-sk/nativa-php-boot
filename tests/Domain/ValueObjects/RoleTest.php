<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Role;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Role
 *
 * @internal
 */
final class RoleTest extends TestCase
{
    public function testCreateAdminRole(): void
    {
        $role = Role::admin();

        self::assertSame('admin', $role->name());
        self::assertTrue($role->isAdmin());
        self::assertFalse($role->isEditor());
        self::assertFalse($role->isViewer());
        self::assertFalse($role->isUser());
    }

    public function testCreateEditorRole(): void
    {
        $role = Role::editor();

        self::assertSame('editor', $role->name());
        self::assertFalse($role->isAdmin());
        self::assertTrue($role->isEditor());
        self::assertFalse($role->isViewer());
        self::assertFalse($role->isUser());
    }

    public function testCreateViewerRole(): void
    {
        $role = Role::viewer();

        self::assertSame('viewer', $role->name());
        self::assertFalse($role->isAdmin());
        self::assertFalse($role->isEditor());
        self::assertTrue($role->isViewer());
        self::assertFalse($role->isUser());
    }

    public function testCreateUserRole(): void
    {
        $role = Role::user();

        self::assertSame('user', $role->name());
        self::assertFalse($role->isAdmin());
        self::assertFalse($role->isEditor());
        self::assertFalse($role->isViewer());
        self::assertTrue($role->isUser());
    }

    public function testRoleFromAdminString(): void
    {
        $role = Role::fromString('admin');

        self::assertTrue($role->isAdmin());
        self::assertSame(100, $role->getLevel());
    }

    public function testRoleFromEditorString(): void
    {
        $role = Role::fromString('editor');

        self::assertTrue($role->isEditor());
        self::assertSame(50, $role->getLevel());
    }

    public function testRoleFromViewerString(): void
    {
        $role = Role::fromString('viewer');

        self::assertTrue($role->isViewer());
        self::assertSame(20, $role->getLevel());
    }

    public function testRoleFromStringWithUppercase(): void
    {
        $role = Role::fromString('ADMIN');

        self::assertTrue($role->isAdmin());
    }

    public function testRoleFromStringWithMixedCase(): void
    {
        $role = Role::fromString('EdItOr');

        self::assertTrue($role->isEditor());
    }

    public function testRoleFromStringWithWhitespace(): void
    {
        $role = Role::fromString('  user  ');

        self::assertTrue($role->isUser());
    }

    public function testInvalidRoleFromString(): void
    {
        $this->expectException(\ValueError::class);

        Role::fromString('invalid');
    }

    public function testAdminHasHighestLevel(): void
    {
        $admin = Role::admin();

        self::assertSame(100, $admin->getLevel());
        self::assertTrue($admin->hasHigherOrEqualLevelThan(Role::editor()));
        self::assertTrue($admin->hasHigherOrEqualLevelThan(Role::viewer()));
        self::assertTrue($admin->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testEditorHasHigherLevelThanViewerAndUser(): void
    {
        $editor = Role::editor();

        self::assertSame(50, $editor->getLevel());
        self::assertFalse($editor->hasHigherOrEqualLevelThan(Role::admin()));
        self::assertTrue($editor->hasHigherOrEqualLevelThan(Role::viewer()));
        self::assertTrue($editor->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testViewerHasHigherLevelThanUser(): void
    {
        $viewer = Role::viewer();

        self::assertSame(20, $viewer->getLevel());
        self::assertFalse($viewer->hasHigherOrEqualLevelThan(Role::admin()));
        self::assertFalse($viewer->hasHigherOrEqualLevelThan(Role::editor()));
        self::assertTrue($viewer->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testRoleHasEqualLevelToItself(): void
    {
        $admin = Role::admin();
        $editor = Role::editor();

        self::assertTrue($admin->hasHigherOrEqualLevelThan($admin));
        self::assertTrue($editor->hasHigherOrEqualLevelThan($editor));
    }

    public function testUserRoleHasLowestLevel(): void
    {
        $user = Role::user();

        self::assertSame(10, $user->getLevel());
        self::assertFalse($user->hasHigherOrEqualLevelThan(Role::admin()));
        self::assertFalse($user->hasHigherOrEqualLevelThan(Role::editor()));
        self::assertFalse($user->hasHigherOrEqualLevelThan(Role::viewer()));
    }

    public function testRoleValue(): void
    {
        self::assertSame('admin', Role::ADMIN->value);
        self::assertSame('editor', Role::EDITOR->value);
        self::assertSame('viewer', Role::VIEWER->value);
        self::assertSame('user', Role::USER->value);
    }
}
