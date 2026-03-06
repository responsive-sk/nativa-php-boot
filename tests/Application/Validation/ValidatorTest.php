<?php

declare(strict_types = 1);

namespace Tests\Application\Validation;

use Application\Exceptions\ValidationException;
use Application\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\Validation\Validator
 *
 * @internal
 */
final class ValidatorTest extends TestCase
{
    public function testValidatePassesWithValidData(): void
    {
        // Should not throw exception
        Validator::validate([
            'name'  => 'John',
            'email' => 'john@example.com',
            'age'   => '25',
        ], [
            'name'  => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email'],
            'age'   => ['numeric'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateRequiredField(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'name' => '',
        ], [
            'name' => ['required'],
        ]);
    }

    public function testValidateMinLength(): void
    {
        try {
            Validator::validate([
                'name' => 'Jo',
            ], [
                'name' => ['min:3'],
            ]);
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('at least 3 characters', $e->getFirstError('name'));
        }
    }

    public function testValidateMaxLength(): void
    {
        try {
            Validator::validate([
                'name' => 'This is a very long name that exceeds the maximum length',
            ], [
                'name' => ['max:10'],
            ]);
            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            self::assertStringContainsString('not exceed 10 characters', $e->getFirstError('name'));
        }
    }

    public function testValidateEmail(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'email' => 'invalid-email',
        ], [
            'email' => ['email'],
        ]);
    }

    public function testValidateValidEmail(): void
    {
        Validator::validate([
            'email' => 'valid@example.com',
        ], [
            'email' => ['email'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateUuid(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'user_id' => 'not-a-uuid',
        ], [
            'user_id' => ['uuid'],
        ]);
    }

    public function testValidateValidUuid(): void
    {
        Validator::validate([
            'user_id' => '550e8400-e29b-41d4-a716-446655440000',
        ], [
            'user_id' => ['uuid'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateAlpha(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'name' => 'John123',
        ], [
            'name' => ['alpha'],
        ]);
    }

    public function testValidateValidAlpha(): void
    {
        Validator::validate([
            'name' => 'JohnDoe',
        ], [
            'name' => ['alpha'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateNumeric(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'age' => 'not-a-number',
        ], [
            'age' => ['numeric'],
        ]);
    }

    public function testValidateValidNumeric(): void
    {
        Validator::validate([
            'age' => '25',
        ], [
            'age' => ['numeric'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateUrl(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'website' => 'not-a-url',
        ], [
            'website' => ['url'],
        ]);
    }

    public function testValidateValidUrl(): void
    {
        Validator::validate([
            'website' => 'https://example.com',
        ], [
            'website' => ['url'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateMultipleErrors(): void
    {
        try {
            Validator::validate([
                'name'  => '',
                'email' => 'invalid',
            ], [
                'name'  => ['required', 'min:3'],
                'email' => ['email'],
            ]);

            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            self::assertArrayHasKey('name', $errors);
            self::assertArrayHasKey('email', $errors);
        }
    }

    public function testValidateOptionalField(): void
    {
        Validator::validate([
            'name'  => 'John',
            'email' => null,
        ], [
            'name'  => ['required', 'min:2'],
            'email' => ['email'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateWithMultipleRules(): void
    {
        Validator::validate([
            'username' => 'johndoe',
        ], [
            'username' => ['required', 'min:3', 'max:50', 'alpha'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateFieldFailsFirstRule(): void
    {
        try {
            Validator::validate([
                'username' => '',
            ], [
                'username' => ['required', 'min:3', 'alpha'],
            ]);

            self::fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            // Should fail on 'required' first
            self::assertContains('Username is required', $errors['username']);
        }
    }

    public function testValidateUnknownRule(): void
    {
        // Unknown rules should be silently ignored
        Validator::validate([
            'field' => 'value',
        ], [
            'field' => ['unknown_rule'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateMinWithZero(): void
    {
        Validator::validate([
            'count' => '0',
        ], [
            'count' => ['min:0'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateMaxBoundary(): void
    {
        Validator::validate([
            'name' => '1234567890',
        ], [
            'name' => ['max:10'],
        ]);

        $this->expectNotToPerformAssertions();
    }

    public function testValidateMaxBoundaryExceeded(): void
    {
        $this->expectException(ValidationException::class);

        Validator::validate([
            'name' => '12345678901',
        ], [
            'name' => ['max:10'],
        ]);
    }
}
