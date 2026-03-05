<?php

declare(strict_types=1);

namespace Tests\Application\Services;

use Application\Services\ContactManager;
use Domain\Events\ContactSubmitted;
use Domain\Events\EventDispatcherInterface;
use Domain\Model\Contact;
use Domain\Repository\ContactRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Application\Services\ContactManager
 */
final class ContactManagerTest extends TestCase
{
    private ContactRepositoryInterface&MockObject $contactRepository;

    private EventDispatcherInterface&MockObject $eventDispatcher;

    private ContactManager $contactManager;

    protected function setUp(): void
    {
        $this->contactRepository = $this->createMock(ContactRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->contactManager = new ContactManager(
            $this->contactRepository,
            $this->eventDispatcher
        );
    }

    public function testSubmitCreatesContactAndDispatchesEvent(): void
    {
        $this->contactRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Contact::class));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ContactSubmitted::class));

        $contact = $this->contactManager->submit(
            name: 'John Doe',
            email: 'john@example.com',
            message: 'Test message',
            subject: 'Inquiry'
        );

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('John Doe', $contact->name());
        $this->assertEquals('john@example.com', $contact->email());
        $this->assertEquals('Test message', $contact->message());
        $this->assertEquals('Inquiry', $contact->subject());
    }

    public function testSubmitWithoutSubject(): void
    {
        $this->contactRepository
            ->expects($this->once())
            ->method('save');

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $contact = $this->contactManager->submit(
            name: 'Jane Doe',
            email: 'jane@example.com',
            message: 'Test message'
        );

        $this->assertNull($contact->subject());
    }

    public function testFindByIdReturnsContact(): void
    {
        $expectedContact = Contact::create('John Doe', 'john@example.com', 'Test message');

        $this->contactRepository
            ->expects($this->once())
            ->method('findById')
            ->with('contact-123')
            ->willReturn($expectedContact);

        $contact = $this->contactManager->findById('contact-123');

        $this->assertSame($expectedContact, $contact);
        $this->assertEquals('John Doe', $contact->name());
    }

    public function testFindByIdReturnsNull(): void
    {
        $this->contactRepository
            ->expects($this->once())
            ->method('findById')
            ->with('non-existent')
            ->willReturn(null);

        $contact = $this->contactManager->findById('non-existent');

        $this->assertNull($contact);
    }

    public function testFindAllReturnsArray(): void
    {
        $contacts = [
            Contact::create('John Doe', 'john@example.com', 'Message 1'),
            Contact::create('Jane Doe', 'jane@example.com', 'Message 2'),
        ];

        $this->contactRepository
            ->expects($this->once())
            ->method('findAll')
            ->with(50, 0)
            ->willReturn($contacts);

        $result = $this->contactManager->findAll();

        $this->assertCount(2, $result);
    }

    public function testFindAllWithCustomLimitAndOffset(): void
    {
        $this->contactRepository
            ->expects($this->once())
            ->method('findAll')
            ->with(10, 20)
            ->willReturn([]);

        $result = $this->contactManager->findAll(10, 20);

        $this->assertCount(0, $result);
    }

    public function testFindNewReturnsNewContacts(): void
    {
        $contacts = [Contact::create('John Doe', 'john@example.com', 'New message')];

        $this->contactRepository
            ->expects($this->once())
            ->method('findByStatus')
            ->with('new', 50)
            ->willReturn($contacts);

        $result = $this->contactManager->findNew();

        $this->assertCount(1, $result);
    }

    public function testMarkAsReadChangesStatus(): void
    {
        $contact = Contact::create('John Doe', 'john@example.com', 'Message');

        $this->contactRepository
            ->expects($this->once())
            ->method('findById')
            ->with('contact-123')
            ->willReturn($contact);

        $this->contactRepository
            ->expects($this->once())
            ->method('save')
            ->with($contact);

        $result = $this->contactManager->markAsRead('contact-123');

        $this->assertSame($contact, $result);
        $this->assertEquals('read', $contact->status());
    }
}
