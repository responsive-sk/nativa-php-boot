<?php

declare(strict_types = 1);

namespace Tests\Domain\Model;

use Domain\Model\User;
use Domain\ValueObjects\Email;
use Domain\ValueObjects\Password;
use Domain\ValueObjects\Role as RoleVO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\Model\User
 *
 * @internal
 */
final class UserTest extends TestCase
{
    public function testCreateUser(): void
    {
        $user = User::create(
            name: 'John Doe',
            email: Email::fromString('john@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::user(),
        );

        self::assertSame('John Doe', $user->name());
        self::assertSame('john@example.com', (string) $user->email());
        self::assertFalse($user->isAdmin());
        self::assertTrue($user->isActive());
    }

    public function testCreateAdminUser(): void
    {
        $user = User::create(
            name: 'Admin User',
            email: Email::fromString('admin@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::admin(),
        );

        self::assertSame('Admin User', $user->name());
        self::assertTrue($user->isAdmin());
    }

    public function testDeactivateUser(): void
    {
        $user = User::create(
            name: 'Active User',
            email: Email::fromString('active@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::user(),
        );

        $user->deactivate();

        self::assertFalse($user->isActive());
    }

    public function testActivateUser(): void
    {
        $user = User::create(
            name: 'Inactive User',
            email: Email::fromString('inactive@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::user(),
        );
        $user->deactivate();

        $user->activate();

        self::assertTrue($user->isActive());
    }

    public function testChangePassword(): void
    {
        $user = User::create(
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: Password::fromPlain('OldPassword123!'),
            role: RoleVO::user(),
        );

        $user->changePassword(Password::fromPlain('NewPassword456!'));

        self::assertNotNull($user->password());
    }

    public function testUserIsNotAdminByDefault(): void
    {
        $user = User::create(
            name: 'Regular User',
            email: Email::fromString('user@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::user(),
        );

        self::assertFalse($user->isAdmin());
    }

    public function testUserHasId(): void
    {
        $user = User::create(
            name: 'Test User',
            email: Email::fromString('test@example.com'),
            password: Password::fromPlain('Password123!'),
            role: RoleVO::user(),
        );

        self::assertNotEmpty($user->id());
        self::assertIsString($user->id());
    }
}
