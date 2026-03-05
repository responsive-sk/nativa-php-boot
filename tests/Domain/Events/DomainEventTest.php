<?php

declare(strict_types=1);

namespace Tests\Domain\Events;

use Domain\Events\ArticleCreated;
use Domain\Events\ArticlePublished;
use Domain\Events\ArticleUpdated;
use Domain\Events\ArticleDeleted;
use Domain\Events\PageCreated;
use Domain\Events\PageUpdated;
use Domain\Events\FormSubmitted;
use Domain\Events\ContactSubmitted;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Domain\Events\ArticleCreated
 * @covers \Domain\Events\ArticlePublished
 * @covers \Domain\Events\ArticleUpdated
 * @covers \Domain\Events\ArticleDeleted
 * @covers \Domain\Events\PageCreated
 * @covers \Domain\Events\PageUpdated
 * @covers \Domain\Events\FormSubmitted
 * @covers \Domain\Events\ContactSubmitted
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

        $this->assertEquals('article-123', $event->articleId());
        $this->assertEquals('Test Article', $event->title());
        $this->assertEquals('author-456', $event->authorId());
        $this->assertEquals('category-789', $event->categoryId());
        $this->assertNotNull($event->occurredAt());
    }

    public function testArticleCreatedEventWithoutCategory(): void
    {
        $event = new ArticleCreated(
            articleId: 'article-123',
            title: 'Test Article',
            authorId: 'author-456'
        );

        $this->assertNull($event->categoryId());
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

        $this->assertIsArray($payload);
        $this->assertEquals('article-123', $payload['article_id']);
        $this->assertEquals('Test Article', $payload['title']);
        $this->assertEquals('author-456', $payload['author_id']);
        $this->assertEquals('category-789', $payload['category_id']);
        $this->assertArrayHasKey('occurred_at', $payload);
    }

    public function testArticlePublishedEvent(): void
    {
        $event = new ArticlePublished(
            articleId: 'article-123',
            title: 'Published Article',
            publishedAt: '2026-03-05 12:00:00'
        );

        $this->assertEquals('article-123', $event->articleId());
        $this->assertEquals('Published Article', $event->title());
        $this->assertEquals('2026-03-05 12:00:00', $event->publishedAt());
    }

    public function testArticlePublishedPayload(): void
    {
        $event = new ArticlePublished(
            articleId: 'article-123',
            title: 'Published Article',
            publishedAt: '2026-03-05 12:00:00'
        );

        $payload = $event->payload();

        $this->assertEquals('article-123', $payload['article_id']);
        $this->assertEquals('Published Article', $payload['title']);
        $this->assertEquals('2026-03-05 12:00:00', $payload['published_at']);
    }

    public function testArticleUpdatedEvent(): void
    {
        $event = new ArticleUpdated(
            articleId: 'article-123',
            title: 'Updated Article',
            changes: ['title' => 'New Title', 'content' => 'New content']
        );

        $this->assertEquals('article-123', $event->articleId());
        $this->assertEquals('Updated Article', $event->title());
        $this->assertIsArray($event->changes());
    }

    public function testArticleDeletedEvent(): void
    {
        $event = new ArticleDeleted(
            articleId: 'article-123',
            title: 'Deleted Article'
        );

        $this->assertEquals('article-123', $event->articleId());
        $this->assertEquals('Deleted Article', $event->title());
    }

    public function testPageCreatedEvent(): void
    {
        $event = new PageCreated(
            pageId: 'page-123',
            title: 'Test Page'
        );

        $this->assertEquals('page-123', $event->pageId());
        $this->assertEquals('Test Page', $event->title());
    }

    public function testPageUpdatedEvent(): void
    {
        $event = new PageUpdated(
            pageId: 'page-123',
            title: 'Updated Page'
        );

        $this->assertEquals('page-123', $event->pageId());
        $this->assertEquals('Updated Page', $event->title());
    }

    public function testFormSubmittedEvent(): void
    {
        $event = new FormSubmitted(
            formId: 'form-123',
            formName: 'Contact Form',
            submissionId: 'submission-456',
            data: ['name' => 'John', 'email' => 'john@example.com']
        );

        $this->assertEquals('form-123', $event->formId());
        $this->assertEquals('Contact Form', $event->formName());
        $this->assertEquals('submission-456', $event->submissionId());
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com'], $event->data());
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

        $this->assertEquals('contact-123', $event->contactId());
        $this->assertEquals('John Doe', $event->name());
        $this->assertEquals('john@example.com', $event->email());
        $this->assertEquals('Inquiry', $event->subject());
        $this->assertEquals('Test message', $event->message());
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

        $this->assertEquals('contact-123', $payload['contact_id']);
        $this->assertEquals('John Doe', $payload['name']);
        $this->assertEquals('john@example.com', $payload['email']);
        $this->assertEquals('Test message', $payload['message']);
    }

    public function testEventHasOccurredAt(): void
    {
        $event1 = new ArticleCreated('id1', 'Title1', 'author1');
        $event2 = new ArticleCreated('id2', 'Title2', 'author2');

        $this->assertNotNull($event1->occurredAt());
        $this->assertNotNull($event2->occurredAt());
        $this->assertIsString($event1->occurredAt());
    }
}
