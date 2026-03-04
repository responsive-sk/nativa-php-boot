<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Form Submission Entity
 */
final class FormSubmission
{
    private string $id;
    private string $formId;
    /** @var array<string, mixed> */
    private array $data;
    private string $ipAddress;
    private string $userAgent;
    private string $submittedAt;

    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(
        string $formId,
        array $data,
        string $ipAddress,
        string $userAgent,
    ): self {
        $submission = new self();
        $submission->id = self::generateId();
        $submission->formId = $formId;
        $submission->data = $data;
        $submission->ipAddress = $ipAddress;
        $submission->userAgent = $userAgent;
        $submission->submittedAt = self::now();

        return $submission;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $submission = new self();
        $submission->id = (string) $data['id'];
        $submission->formId = (string) $data['form_id'];
        $submission->data = (array) (json_decode($data['data'], true) ?? []);
        $submission->ipAddress = (string) ($data['ip_address'] ?? '');
        $submission->userAgent = (string) ($data['user_agent'] ?? '');
        $submission->submittedAt = (string) $data['submitted_at'];

        return $submission;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function formId(): string
    {
        return $this->formId;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    public function submittedAt(): string
    {
        return $this->submittedAt;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->formId,
            'data' => json_encode($this->data),
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'submitted_at' => $this->submittedAt,
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
