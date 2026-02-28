<?php

declare(strict_types=1);

namespace Domain\Events;

/**
 * Form Submitted Event
 */
final class FormSubmitted extends DomainEvent
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly string $formId,
        private readonly string $formName,
        private readonly string $submissionId,
        private readonly array $data,
        private readonly ?string $ipAddress = null,
    ) {
        parent::__construct();
    }

    public function formId(): string
    {
        return $this->formId;
    }

    public function formName(): string
    {
        return $this->formName;
    }

    public function submissionId(): string
    {
        return $this->submissionId;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function ipAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function payload(): array
    {
        return [
            'form_id' => $this->formId,
            'form_name' => $this->formName,
            'submission_id' => $this->submissionId,
            'data' => $this->data,
            'ip_address' => $this->ipAddress,
            'occurred_at' => $this->occurredAt(),
        ];
    }
}
