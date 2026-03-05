<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use Domain\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\ValueObjects\Email
 */
final class EmailTest extends TestCase
{
    public function testCreateValidEmail(): void
    {
        $email = Email::fromString('test@example.com');

        $this->assertEquals('test@example.com', (string) $email);
        $this->assertEquals('test@example.com', $email->value());
    }

    public function testEmailWithPlusAddressing(): void
    {
        $email = Email::fromString('test+tag@example.com');

        $this->assertEquals('test+tag@example.com', (string) $email);
    }

    public function testEmailWithSubdomain(): void
    {
        $email = Email::fromString('user@mail.example.com');

        $this->assertEquals('user@mail.example.com', (string) $email);
    }

    public function testInvalidEmailWithoutAt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('invalidemail.com');
    }

    public function testInvalidEmailWithMultipleAt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('test@@example.com');
    }

    public function testInvalidEmailWithEmptyLocalPart(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('@example.com');
    }

    public function testInvalidEmailWithEmptyDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('test@');
    }

    public function testInvalidEmailWithSpaces(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('test @example.com');
    }

    public function testEmailNormalization(): void
    {
        $email = Email::fromString('TEST@EXAMPLE.COM');

        $this->assertEquals('TEST@EXAMPLE.COM', (string) $email);
    }

    public function testEmailWithValidTld(): void
    {
        $email = Email::fromString('user@example.io');

        $this->assertEquals('user@example.io', (string) $email);
    }

    public function testEmailWithNumericDomain(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString('admin@127.0.0.1');
    }
}
