<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Role;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Role
 */
final class RoleTest extends TestCase
{
    public function testCreateAdminRole(): void
    {
        $role = Role::admin();

        $this->assertEquals('admin', $role->name());
        $this->assertTrue($role->isAdmin());
        $this->assertFalse($role->isEditor());
        $this->assertFalse($role->isViewer());
        $this->assertFalse($role->isUser());
    }

    public function testCreateEditorRole(): void
    {
        $role = Role::editor();

        $this->assertEquals('editor', $role->name());
        $this->assertFalse($role->isAdmin());
        $this->assertTrue($role->isEditor());
        $this->assertFalse($role->isViewer());
        $this->assertFalse($role->isUser());
    }

    public function testCreateViewerRole(): void
    {
        $role = Role::viewer();

        $this->assertEquals('viewer', $role->name());
        $this->assertFalse($role->isAdmin());
        $this->assertFalse($role->isEditor());
        $this->assertTrue($role->isViewer());
        $this->assertFalse($role->isUser());
    }

    public function testCreateUserRole(): void
    {
        $role = Role::user();

        $this->assertEquals('user', $role->name());
        $this->assertFalse($role->isAdmin());
        $this->assertFalse($role->isEditor());
        $this->assertFalse($role->isViewer());
        $this->assertTrue($role->isUser());
    }

    public function testRoleFromAdminString(): void
    {
        $role = Role::fromString('admin');

        $this->assertTrue($role->isAdmin());
        $this->assertEquals(100, $role->getLevel());
    }

    public function testRoleFromEditorString(): void
    {
        $role = Role::fromString('editor');

        $this->assertTrue($role->isEditor());
        $this->assertEquals(50, $role->getLevel());
    }

    public function testRoleFromViewerString(): void
    {
        $role = Role::fromString('viewer');

        $this->assertTrue($role->isViewer());
        $this->assertEquals(20, $role->getLevel());
    }

    public function testRoleFromStringWithUppercase(): void
    {
        $role = Role::fromString('ADMIN');

        $this->assertTrue($role->isAdmin());
    }

    public function testRoleFromStringWithMixedCase(): void
    {
        $role = Role::fromString('EdItOr');

        $this->assertTrue($role->isEditor());
    }

    public function testRoleFromStringWithWhitespace(): void
    {
        $role = Role::fromString('  user  ');

        $this->assertTrue($role->isUser());
    }

    public function testInvalidRoleFromString(): void
    {
        $this->expectException(\ValueError::class);

        Role::fromString('invalid');
    }

    public function testAdminHasHighestLevel(): void
    {
        $admin = Role::admin();

        $this->assertEquals(100, $admin->getLevel());
        $this->assertTrue($admin->hasHigherOrEqualLevelThan(Role::editor()));
        $this->assertTrue($admin->hasHigherOrEqualLevelThan(Role::viewer()));
        $this->assertTrue($admin->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testEditorHasHigherLevelThanViewerAndUser(): void
    {
        $editor = Role::editor();

        $this->assertEquals(50, $editor->getLevel());
        $this->assertFalse($editor->hasHigherOrEqualLevelThan(Role::admin()));
        $this->assertTrue($editor->hasHigherOrEqualLevelThan(Role::viewer()));
        $this->assertTrue($editor->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testViewerHasHigherLevelThanUser(): void
    {
        $viewer = Role::viewer();

        $this->assertEquals(20, $viewer->getLevel());
        $this->assertFalse($viewer->hasHigherOrEqualLevelThan(Role::admin()));
        $this->assertFalse($viewer->hasHigherOrEqualLevelThan(Role::editor()));
        $this->assertTrue($viewer->hasHigherOrEqualLevelThan(Role::user()));
    }

    public function testRoleHasEqualLevelToItself(): void
    {
        $admin = Role::admin();
        $editor = Role::editor();

        $this->assertTrue($admin->hasHigherOrEqualLevelThan($admin));
        $this->assertTrue($editor->hasHigherOrEqualLevelThan($editor));
    }

    public function testUserRoleHasLowestLevel(): void
    {
        $user = Role::user();

        $this->assertEquals(10, $user->getLevel());
        $this->assertFalse($user->hasHigherOrEqualLevelThan(Role::admin()));
        $this->assertFalse($user->hasHigherOrEqualLevelThan(Role::editor()));
        $this->assertFalse($user->hasHigherOrEqualLevelThan(Role::viewer()));
    }

    public function testRoleValue(): void
    {
        $this->assertEquals('admin', Role::ADMIN->value);
        $this->assertEquals('editor', Role::EDITOR->value);
        $this->assertEquals('viewer', Role::VIEWER->value);
        $this->assertEquals('user', Role::USER->value);
    }
}
