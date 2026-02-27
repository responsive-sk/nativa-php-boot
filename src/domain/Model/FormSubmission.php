<?php

declare(strict_types=1);

namespace Domain\Model;

/**
 * Form Submission Entity
 */
class FormSubmission
{
    private string $id;
    private string $formId;
    private array $data;
    private string $ipAddress;
    private string $userAgent;
    private string $submittedAt;

    private function __construct()
    {
    }

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

    public static function fromArray(array $data): self
    {
        $submission = new self();
        $submission->id = $data['id'];
        $submission->formId = $data['form_id'];
        $submission->data = json_decode($data['data'], true) ?? [];
        $submission->ipAddress = $data['ip_address'] ?? '';
        $submission->userAgent = $data['user_agent'] ?? '';
        $submission->submittedAt = $data['submitted_at'];

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
