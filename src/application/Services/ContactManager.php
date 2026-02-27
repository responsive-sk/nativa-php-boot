<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Events\ContactSubmitted;
use Domain\Events\EventDispatcherInterface;
use Domain\Model\Contact;
use Domain\Repository\ContactRepositoryInterface;

/**
 * Contact Manager
 */
class ContactManager
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Submit contact form
     */
    public function submit(
        string $name,
        string $email,
        string $message,
        ?string $subject = null,
    ): Contact {
        $contact = Contact::create($name, $email, $message, $subject);

        $this->contactRepository->save($contact);

        // Dispatch event
        $this->eventDispatcher->dispatch(
            new ContactSubmitted(
                $contact->id(),
                $contact->name(),
                $contact->email(),
                $contact->subject(),
                $contact->message()
            )
        );

        return $contact;
    }

    /**
     * Get contact by ID
     */
    public function findById(string $id): ?Contact
    {
        return $this->contactRepository->findById($id);
    }

    /**
     * Get all contacts
     *
     * @return array<Contact>
     */
    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return $this->contactRepository->findAll($limit, $offset);
    }

    /**
     * Get new contacts
     *
     * @return array<Contact>
     */
    public function findNew(int $limit = 50): array
    {
        return $this->contactRepository->findByStatus('new', $limit);
    }

    /**
     * Mark contact as read
     */
    public function markAsRead(string $id): Contact
    {
        $contact = $this->findById($id);

        if ($contact === null) {
            throw new \RuntimeException('Contact not found');
        }

        $contact->markAsRead();
        $this->contactRepository->save($contact);

        return $contact;
    }

    /**
     * Mark contact as replied
     */
    public function markAsReplied(string $id): Contact
    {
        $contact = $this->findById($id);

        if ($contact === null) {
            throw new \RuntimeException('Contact not found');
        }

        $contact->markAsReplied();
        $this->contactRepository->save($contact);

        return $contact;
    }

    /**
     * Mark contact as spam
     */
    public function markAsSpam(string $id): Contact
    {
        $contact = $this->findById($id);

        if ($contact === null) {
            throw new \RuntimeException('Contact not found');
        }

        $contact->markAsSpam();
        $this->contactRepository->save($contact);

        return $contact;
    }

    /**
     * Get count of new contacts
     */
    public function countNew(): int
    {
        return $this->contactRepository->countByStatus('new');
    }
}
