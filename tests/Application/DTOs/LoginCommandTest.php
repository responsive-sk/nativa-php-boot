<?php

declare(strict_types = 1);

namespace Tests\Application\DTOs;

use Application\DTOs\LoginCommand;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\DTOs\LoginCommand
 *
 * @internal
 */
final class LoginCommandTest extends TestCase
{
    public function testCreateLoginCommandWithValidData(): void
    {
        $command = LoginCommand::fromArray([
            'email'       => 'test@example.com',
            'password'    => 'Password123!',
            'remember_me' => false,
        ]);

        self::assertSame('test@example.com', $command->email);
        self::assertSame('Password123!', $command->password);
        self::assertFalse($command->rememberMe);
    }

    public function testCreateLoginCommandWithRememberMe(): void
    {
        $command = LoginCommand::fromArray([
            'email'       => 'test@example.com',
            'password'    => 'Password123!',
            'remember_me' => true,
        ]);

        self::assertTrue($command->rememberMe);
    }

    public function testCreateLoginCommandWithEmptyEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        LoginCommand::fromArray([
            'email'       => '',
            'password'    => 'Password123!',
            'remember_me' => false,
        ]);
    }

    public function testCreateLoginCommandWithInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        LoginCommand::fromArray([
            'email'       => 'invalid-email',
            'password'    => 'Password123!',
            'remember_me' => false,
        ]);
    }

    public function testCreateLoginCommandWithEmptyPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        LoginCommand::fromArray([
            'email'       => 'test@example.com',
            'password'    => '',
            'remember_me' => false,
        ]);
    }

    public function testCreateLoginCommandWithMissingFields(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        LoginCommand::fromArray([
            'email' => 'test@example.com',
        ]);
    }

    public function testCreateLoginCommandWithExtraFields(): void
    {
        $command = LoginCommand::fromArray([
            'email'       => 'test@example.com',
            'password'    => 'Password123!',
            'remember_me' => false,
            'extra_field' => 'ignored',
        ]);

        self::assertSame('test@example.com', $command->email);
    }
}
