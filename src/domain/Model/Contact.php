<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Contact Entity
 */
final class Contact
{
    private string $id;
    private string $name;
    private string $email;
    private ?string $subject;
    private string $message;
    private string $status;
    private string $createdAt;

    private function __construct()
    {
    }

    public static function create(
        string $name,
        string $email,
        string $message,
        ?string $subject = null,
    ): self {
        $contact = new self();
        $contact->id = self::generateId();
        $contact->name = $name;
        $contact->email = $email;
        $contact->subject = $subject;
        $contact->message = $message;
        $contact->status = 'new';
        $contact->createdAt = self::now();

        return $contact;
    }

    public static function fromArray(array $data): self
    {
        $contact = new self();
        $contact->id = $data['id'];
        $contact->name = $data['name'];
        $contact->email = $data['email'];
        $contact->subject = $data['subject'] ?? null;
        $contact->message = $data['message'];
        $contact->status = $data['status'];
        $contact->createdAt = $data['created_at'];

        return $contact;
    }

    public function markAsRead(): void
    {
        $this->status = 'read';
    }

    public function markAsReplied(): void
    {
        $this->status = 'replied';
    }

    public function markAsSpam(): void
    {
        $this->status = 'spam';
    }

    // Getters
    public function id(): string
    {
        return $this->id;
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

    public function status(): string
    {
        return $this->status;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
