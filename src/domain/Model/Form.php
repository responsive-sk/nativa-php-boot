<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Form Entity (Form Builder)
 */
class Form
{
    private string $id;
    private string $name;
    private string $slug;
    private array $schema;
    private ?string $emailNotification;
    private string $successMessage;
    private string $createdAt;
    private string $updatedAt;

    private function __construct()
    {
    }

    public static function create(
        string $name,
        array $schema,
        ?string $emailNotification = null,
        string $successMessage = 'Thank you for your submission!',
    ): self {
        $form = new self();
        $form->id = self::generateId();
        $form->name = $name;
        $form->slug = self::generateSlug($name);
        $form->schema = $schema;
        $form->emailNotification = $emailNotification;
        $form->successMessage = $successMessage;
        $form->createdAt = self::now();
        $form->updatedAt = self::now();

        return $form;
    }

    public static function fromArray(array $data): self
    {
        $form = new self();
        $form->id = $data['id'];
        $form->name = $data['name'];
        $form->slug = $data['slug'];
        $form->schema = json_decode($data['schema'], true) ?? [];
        $form->emailNotification = $data['email_notification'] ?? null;
        $form->successMessage = $data['success_message'] ?? 'Thank you for your submission!';
        $form->createdAt = $data['created_at'];
        $form->updatedAt = $data['updated_at'];

        return $form;
    }

    public function update(
        ?string $name = null,
        ?array $schema = null,
        ?string $emailNotification = null,
        ?string $successMessage = null,
    ): void {
        if ($name !== null) {
            $this->name = $name;
            $this->slug = self::generateSlug($name);
        }

        if ($schema !== null) {
            $this->schema = $schema;
        }

        if ($emailNotification !== null) {
            $this->emailNotification = $emailNotification;
        }

        if ($successMessage !== null) {
            $this->successMessage = $successMessage;
        }

        $this->updatedAt = self::now();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function emailNotification(): ?string
    {
        return $this->emailNotification;
    }

    public function successMessage(): string
    {
        return $this->successMessage;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'schema' => json_encode($this->schema),
            'email_notification' => $this->emailNotification,
            'success_message' => $this->successMessage,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}
