<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Contact Submitted Event
 */
final class ContactSubmitted extends DomainEvent
{
    public function __construct(
        private readonly string $contactId,
        private readonly string $name,
        private readonly string $email,
        private readonly ?string $subject,
        private readonly string $message,
    ) {
        parent::__construct();
    }

    public function contactId(): string
    {
        return $this->contactId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function subject(): ?string
    {
        return $this->subject;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function payload(): array
    {
        return [
            'contact_id' => $this->contactId,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
