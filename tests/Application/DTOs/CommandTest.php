<?php

declare(strict_types = 1);

namespace Tests\Application\DTOs;

use Application\DTOs\CreateArticleCommand;
use Application\DTOs\SubmitContactCommand;
use Application\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\DTOs\CreateArticleCommand
 * @covers \Application\DTOs\SubmitContactCommand
 *
 * @internal
 */
final class CommandTest extends TestCase
{
    public function testCreateArticleCommandWithValidData(): void
    {
        $command = new CreateArticleCommand(
            title: 'Test Article',
            content: 'This is the content of the article with more than 10 characters',
            authorId: '550e8400-e29b-41d4-a716-446655440000'
        );

        self::assertSame('Test Article', $command->title);
        self::assertStringContainsString('content', $command->content);
        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $command->authorId);
        self::assertNull($command->categoryId);
        self::assertNull($command->excerpt);
        self::assertNull($command->image);
        self::assertNull($command->tags);
    }

    public function testCreateArticleCommandWithOptionalFields(): void
    {
        $command = new CreateArticleCommand(
            title: 'Test Article',
            content: 'Content here',
            authorId: '550e8400-e29b-41d4-a716-446655440000',
            categoryId: 'category-123',
            excerpt: 'Short excerpt',
            image: 'https://example.com/image.jpg',
            tags: ['php', 'cms', 'testing']
        );

        self::assertSame('category-123', $command->categoryId);
        self::assertSame('Short excerpt', $command->excerpt);
        self::assertSame('https://example.com/image.jpg', $command->image);
        self::assertSame(['php', 'cms', 'testing'], $command->tags);
    }

    public function testCreateArticleCommandToArray(): void
    {
        $command = new CreateArticleCommand(
            title: 'Test Article',
            content: 'Valid content with more than 10 characters',
            authorId: '550e8400-e29b-41d4-a716-446655440000',
            categoryId: 'cat-456',
            excerpt: 'Excerpt',
            image: 'https://example.com/img.jpg',
            tags: ['tag1', 'tag2']
        );

        $array = $command->toArray();

        self::assertIsArray($array);
        self::assertSame('Test Article', $array['title']);
        self::assertSame('Valid content with more than 10 characters', $array['content']);
        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $array['authorId']);
        self::assertSame('cat-456', $array['categoryId']);
        self::assertSame('Excerpt', $array['excerpt']);
        self::assertSame('https://example.com/img.jpg', $array['image']);
        self::assertSame(['tag1', 'tag2'], $array['tags']);
    }

    public function testCreateArticleCommandWithTitleTooShort(): void
    {
        $this->expectException(ValidationException::class);

        new CreateArticleCommand(
            title: 'AB',
            content: 'Valid content here',
            authorId: '550e8400-e29b-41d4-a716-446655440000'
        );
    }

    public function testCreateArticleCommandWithContentTooShort(): void
    {
        $this->expectException(ValidationException::class);

        new CreateArticleCommand(
            title: 'Valid Title',
            content: 'Short',
            authorId: '550e8400-e29b-41d4-a716-446655440000'
        );
    }

    public function testCreateArticleCommandWithInvalidAuthorId(): void
    {
        $this->expectException(ValidationException::class);

        new CreateArticleCommand(
            title: 'Valid Title',
            content: 'Valid content here',
            authorId: 'invalid-uuid'
        );
    }

    public function testCreateArticleCommandWithInvalidTags(): void
    {
        $this->expectException(ValidationException::class);

        new CreateArticleCommand(
            title: 'Valid Title',
            content: 'Valid content',
            authorId: '550e8400-e29b-41d4-a716-446655440000',
            tags: ['a'] // Tag too short
        );
    }

    public function testSubmitContactCommandWithValidData(): void
    {
        $command = new SubmitContactCommand(
            name: 'John Doe',
            email: 'john@example.com',
            message: 'This is a test message with more than 10 characters'
        );

        self::assertSame('John Doe', $command->name);
        self::assertSame('john@example.com', $command->email);
        self::assertStringContainsString('test message', $command->message);
        self::assertNull($command->subject);
    }

    public function testSubmitContactCommandWithSubject(): void
    {
        $command = new SubmitContactCommand(
            name: 'Jane Doe',
            email: 'jane@example.com',
            message: 'Test message content',
            subject: 'Inquiry about services'
        );

        self::assertSame('Inquiry about services', $command->subject);
    }

    public function testSubmitContactCommandWithNameTooShort(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: 'J',
            email: 'test@example.com',
            message: 'Valid message content here'
        );
    }

    public function testSubmitContactCommandWithInvalidEmail(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: 'John Doe',
            email: 'invalid-email',
            message: 'Valid message content here'
        );
    }

    public function testSubmitContactCommandWithMessageTooShort(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: 'John Doe',
            email: 'test@example.com',
            message: 'Short'
        );
    }

    public function testSubmitContactCommandWithEmptyName(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: '',
            email: 'test@example.com',
            message: 'Valid message content here'
        );
    }

    public function testSubmitContactCommandWithEmptyEmail(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: 'John Doe',
            email: '',
            message: 'Valid message content here'
        );
    }

    public function testSubmitContactCommandWithEmptyMessage(): void
    {
        $this->expectException(ValidationException::class);

        new SubmitContactCommand(
            name: 'John Doe',
            email: 'test@example.com',
            message: ''
        );
    }
}
