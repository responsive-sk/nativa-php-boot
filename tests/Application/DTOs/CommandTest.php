<?php

declare(strict_types=1);

namespace Tests\Application\DTOs;

use Application\DTOs\CreateArticleCommand;
use Application\DTOs\SubmitContactCommand;
use Application\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\DTOs\CreateArticleCommand
 * @covers \Application\DTOs\SubmitContactCommand
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

        $this->assertEquals('Test Article', $command->title);
        $this->assertStringContainsString('content', $command->content);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $command->authorId);
        $this->assertNull($command->categoryId);
        $this->assertNull($command->excerpt);
        $this->assertNull($command->image);
        $this->assertNull($command->tags);
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

        $this->assertEquals('category-123', $command->categoryId);
        $this->assertEquals('Short excerpt', $command->excerpt);
        $this->assertEquals('https://example.com/image.jpg', $command->image);
        $this->assertEquals(['php', 'cms', 'testing'], $command->tags);
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

        $this->assertIsArray($array);
        $this->assertEquals('Test Article', $array['title']);
        $this->assertEquals('Valid content with more than 10 characters', $array['content']);
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $array['authorId']);
        $this->assertEquals('cat-456', $array['categoryId']);
        $this->assertEquals('Excerpt', $array['excerpt']);
        $this->assertEquals('https://example.com/img.jpg', $array['image']);
        $this->assertEquals(['tag1', 'tag2'], $array['tags']);
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

        $this->assertEquals('John Doe', $command->name);
        $this->assertEquals('john@example.com', $command->email);
        $this->assertStringContainsString('test message', $command->message);
        $this->assertNull($command->subject);
    }

    public function testSubmitContactCommandWithSubject(): void
    {
        $command = new SubmitContactCommand(
            name: 'Jane Doe',
            email: 'jane@example.com',
            message: 'Test message content',
            subject: 'Inquiry about services'
        );

        $this->assertEquals('Inquiry about services', $command->subject);
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
