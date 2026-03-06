<?php

declare(strict_types = 1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Password;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Password
 *
 * @internal
 */
final class PasswordTest extends TestCase
{
    public function testCreateFromPlainPassword(): void
    {
        $password = Password::fromPlain('ValidPassword123!');

        self::assertNotEmpty($password->hash());
        self::assertIsString($password->hash());
    }

    public function testCreateFromHash(): void
    {
        $hash = password_hash('TestPassword123!', PASSWORD_DEFAULT);
        $password = Password::fromHash($hash);

        self::assertSame($hash, $password->hash());
    }

    public function testVerifyCorrectPassword(): void
    {
        $password = Password::fromPlain('CorrectPassword123!');

        self::assertTrue($password->verify('CorrectPassword123!'));
    }

    public function testVerifyIncorrectPassword(): void
    {
        $password = Password::fromPlain('CorrectPassword123!');

        self::assertFalse($password->verify('WrongPassword456!'));
    }

    public function testNeedsRehash(): void
    {
        $password = Password::fromPlain('TestPassword123!');

        // Fresh hash should not need rehash
        self::assertFalse($password->needsRehash());
    }

    public function testPasswordWithMinimumLength(): void
    {
        $password = Password::fromPlain('Abc12345!');

        self::assertNotEmpty($password->hash());
    }

    public function testPasswordTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 8 characters long');

        Password::fromPlain('Short1!');
    }

    public function testPasswordWithoutUppercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must contain at least one uppercase letter');

        Password::fromPlain('lowercase123!');
    }

    public function testPasswordWithoutLowercase(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must contain at least one lowercase letter');

        Password::fromPlain('UPPERCASE123!');
    }

    public function testPasswordWithoutNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must contain at least one number');

        Password::fromPlain('NoNumbersHere!');
    }

    public function testPasswordWithoutSpecialCharacter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must contain at least one special character');

        Password::fromPlain('NoSpecialChar123');
    }

    public function testPasswordWithMultipleErrors(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Password::fromPlain('short');
    }

    public function testPasswordWithAllValidRequirements(): void
    {
        $password = Password::fromPlain('MyStr0ngP@ss!');

        self::assertTrue($password->verify('MyStr0ngP@ss!'));
        self::assertFalse($password->verify('WrongPass123!'));
    }

    public function testHashIsDifferentFromPlain(): void
    {
        $password = Password::fromPlain('TestPassword123!');

        self::assertNotSame('TestPassword123!', $password->hash());
    }

    public function testSamePasswordProducesDifferentHashes(): void
    {
        $password1 = Password::fromPlain('SamePassword123!');
        $password2 = Password::fromPlain('SamePassword123!');

        // Hashes should be different due to salt
        self::assertNotSame($password1->hash(), $password2->hash());

        // But both should verify correctly
        self::assertTrue($password1->verify('SamePassword123!'));
        self::assertTrue($password2->verify('SamePassword123!'));
    }
}
