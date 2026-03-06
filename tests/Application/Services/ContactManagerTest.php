<?php

declare(strict_types = 1);

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
 *
 * @internal
 */
final class ContactManagerTest extends TestCase
{
    private ContactRepositoryInterface & MockObject $contactRepository;

    private EventDispatcherInterface & MockObject $eventDispatcher;

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
            ->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(Contact::class));

        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(ContactSubmitted::class));

        $contact = $this->contactManager->submit(
            name: 'John Doe',
            email: 'john@example.com',
            message: 'Test message',
            subject: 'Inquiry'
        );

        self::assertInstanceOf(Contact::class, $contact);
        self::assertSame('John Doe', $contact->name());
        self::assertSame('john@example.com', $contact->email());
        self::assertSame('Test message', $contact->message());
        self::assertSame('Inquiry', $contact->subject());
    }

    public function testSubmitWithoutSubject(): void
    {
        $this->contactRepository
            ->expects(self::once())
            ->method('save');

        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatch');

        $contact = $this->contactManager->submit(
            name: 'Jane Doe',
            email: 'jane@example.com',
            message: 'Test message'
        );

        self::assertNull($contact->subject());
    }

    public function testFindByIdReturnsContact(): void
    {
        $expectedContact = Contact::create('John Doe', 'john@example.com', 'Test message');

        $this->contactRepository
            ->expects(self::once())
            ->method('findById')
            ->with('contact-123')
            ->willReturn($expectedContact);

        $contact = $this->contactManager->findById('contact-123');

        self::assertSame($expectedContact, $contact);
        self::assertSame('John Doe', $contact->name());
    }

    public function testFindByIdReturnsNull(): void
    {
        $this->contactRepository
            ->expects(self::once())
            ->method('findById')
            ->with('non-existent')
            ->willReturn(null);

        $contact = $this->contactManager->findById('non-existent');

        self::assertNull($contact);
    }

    public function testFindAllReturnsArray(): void
    {
        $contacts = [
            Contact::create('John Doe', 'john@example.com', 'Message 1'),
            Contact::create('Jane Doe', 'jane@example.com', 'Message 2'),
        ];

        $this->contactRepository
            ->expects(self::once())
            ->method('findAll')
            ->with(50, 0)
            ->willReturn($contacts);

        $result = $this->contactManager->findAll();

        self::assertCount(2, $result);
    }

    public function testFindAllWithCustomLimitAndOffset(): void
    {
        $this->contactRepository
            ->expects(self::once())
            ->method('findAll')
            ->with(10, 20)
            ->willReturn([]);

        $result = $this->contactManager->findAll(10, 20);

        self::assertCount(0, $result);
    }

    public function testFindNewReturnsNewContacts(): void
    {
        $contacts = [Contact::create('John Doe', 'john@example.com', 'New message')];

        $this->contactRepository
            ->expects(self::once())
            ->method('findByStatus')
            ->with('new', 50)
            ->willReturn($contacts);

        $result = $this->contactManager->findNew();

        self::assertCount(1, $result);
    }

    public function testMarkAsReadChangesStatus(): void
    {
        $contact = Contact::create('John Doe', 'john@example.com', 'Message');

        $this->contactRepository
            ->expects(self::once())
            ->method('findById')
            ->with('contact-123')
            ->willReturn($contact);

        $this->contactRepository
            ->expects(self::once())
            ->method('save')
            ->with($contact);

        $result = $this->contactManager->markAsRead('contact-123');

        self::assertSame($contact, $result);
        self::assertSame('read', $contact->status());
    }
}
