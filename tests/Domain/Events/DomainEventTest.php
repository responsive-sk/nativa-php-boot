<?php

declare(strict_types = 1);

namespace Tests\Domain\Events;

use Domain\Events\ArticleCreated;
use Domain\Events\ArticleDeleted;
use Domain\Events\ArticlePublished;
use Domain\Events\ArticleUpdated;
use Domain\Events\ContactSubmitted;
use Domain\Events\FormSubmitted;
use Domain\Events\PageCreated;
use Domain\Events\PageUpdated;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\Events\ArticleCreated
 * @covers \Domain\Events\ArticleDeleted
 * @covers \Domain\Events\ArticlePublished
 * @covers \Domain\Events\ArticleUpdated
 * @covers \Domain\Events\ContactSubmitted
 * @covers \Domain\Events\FormSubmitted
 * @covers \Domain\Events\PageCreated
 * @covers \Domain\Events\PageUpdated
 *
 * @internal
 */
final class DomainEventTest extends TestCase
{
    public function testArticleCreatedEvent(): void
    {
        $event = new ArticleCreated(
            articleId: 'article-123',
            title: 'Test Article',
            authorId: 'author-456',
            categoryId: 'category-789'
        );

        self::assertSame('article-123', $event->articleId());
        self::assertSame('Test Article', $event->title());
        self::assertSame('author-456', $event->authorId());
        self::assertSame('category-789', $event->categoryId());
        self::assertNotNull($event->occurredAt());
    }

    public function testArticleCreatedEventWithoutCategory(): void
    {
        $event = new ArticleCreated(
            articleId: 'article-123',
            title: 'Test Article',
            authorId: 'author-456'
        );

        self::assertNull($event->categoryId());
    }

    public function testArticleCreatedPayload(): void
    {
        $event = new ArticleCreated(
            articleId: 'article-123',
            title: 'Test Article',
            authorId: 'author-456',
            categoryId: 'category-789'
        );

        $payload = $event->payload();

        self::assertIsArray($payload);
        self::assertSame('article-123', $payload['article_id']);
        self::assertSame('Test Article', $payload['title']);
        self::assertSame('author-456', $payload['author_id']);
        self::assertSame('category-789', $payload['category_id']);
        self::assertArrayHasKey('occurred_at', $payload);
    }

    public function testArticlePublishedEvent(): void
    {
        $event = new ArticlePublished(
            articleId: 'article-123',
            title: 'Published Article',
            publishedAt: '2026-03-05 12:00:00'
        );

        self::assertSame('article-123', $event->articleId());
        self::assertSame('Published Article', $event->title());
        self::assertSame('2026-03-05 12:00:00', $event->publishedAt());
    }

    public function testArticlePublishedPayload(): void
    {
        $event = new ArticlePublished(
            articleId: 'article-123',
            title: 'Published Article',
            publishedAt: '2026-03-05 12:00:00'
        );

        $payload = $event->payload();

        self::assertSame('article-123', $payload['article_id']);
        self::assertSame('Published Article', $payload['title']);
        self::assertSame('2026-03-05 12:00:00', $payload['published_at']);
    }

    public function testArticleUpdatedEvent(): void
    {
        $event = new ArticleUpdated(
            articleId: 'article-123',
            title: 'Updated Article',
            changes: ['title' => 'New Title', 'content' => 'New content']
        );

        self::assertSame('article-123', $event->articleId());
        self::assertSame('Updated Article', $event->title());
        self::assertIsArray($event->changes());
    }

    public function testArticleDeletedEvent(): void
    {
        $event = new ArticleDeleted(
            articleId: 'article-123',
            title: 'Deleted Article'
        );

        self::assertSame('article-123', $event->articleId());
        self::assertSame('Deleted Article', $event->title());
    }

    public function testPageCreatedEvent(): void
    {
        $event = new PageCreated(
            pageId: 'page-123',
            title: 'Test Page'
        );

        self::assertSame('page-123', $event->pageId());
        self::assertSame('Test Page', $event->title());
    }

    public function testPageUpdatedEvent(): void
    {
        $event = new PageUpdated(
            pageId: 'page-123',
            title: 'Updated Page'
        );

        self::assertSame('page-123', $event->pageId());
        self::assertSame('Updated Page', $event->title());
    }

    public function testFormSubmittedEvent(): void
    {
        $event = new FormSubmitted(
            formId: 'form-123',
            formName: 'Contact Form',
            submissionId: 'submission-456',
            data: ['name' => 'John', 'email' => 'john@example.com']
        );

        self::assertSame('form-123', $event->formId());
        self::assertSame('Contact Form', $event->formName());
        self::assertSame('submission-456', $event->submissionId());
        self::assertSame(['name' => 'John', 'email' => 'john@example.com'], $event->data());
    }

    public function testContactSubmittedEvent(): void
    {
        $event = new ContactSubmitted(
            contactId: 'contact-123',
            name: 'John Doe',
            email: 'john@example.com',
            subject: 'Inquiry',
            message: 'Test message'
        );

        self::assertSame('contact-123', $event->contactId());
        self::assertSame('John Doe', $event->name());
        self::assertSame('john@example.com', $event->email());
        self::assertSame('Inquiry', $event->subject());
        self::assertSame('Test message', $event->message());
    }

    public function testContactSubmittedPayload(): void
    {
        $event = new ContactSubmitted(
            contactId: 'contact-123',
            name: 'John Doe',
            email: 'john@example.com',
            subject: 'Inquiry',
            message: 'Test message'
        );

        $payload = $event->payload();

        self::assertSame('contact-123', $payload['contact_id']);
        self::assertSame('John Doe', $payload['name']);
        self::assertSame('john@example.com', $payload['email']);
        self::assertSame('Test message', $payload['message']);
    }

    public function testEventHasOccurredAt(): void
    {
        $event1 = new ArticleCreated('id1', 'Title1', 'author1');
        $event2 = new ArticleCreated('id2', 'Title2', 'author2');

        self::assertNotNull($event1->occurredAt());
        self::assertNotNull($event2->occurredAt());
        self::assertIsString($event1->occurredAt());
    }
}
