<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Entities;

/**
 * Failed Job Entity
 */
class FailedJob
{
    private ?string $id = null;
    private string $queue;
    private array $payload;
    private string $exception;
    private string $failedAt;

    public function __construct(
        string $queue,
        array $payload,
        string $exception
    ) {
        $this->queue = $queue;
        $this->payload = $payload;
        $this->exception = $exception;
        $this->failedAt = date('Y-m-d H:i:s');
    }

    public static function fromArray(array $data): self
    {
        $failedJob = new self(
            queue: $data['queue'],
            payload: json_decode($data['payload'], true),
            exception: $data['exception']
        );
        $failedJob->setId($data['id']);
        $failedJob->failedAt = $data['failed_at'];
        return $failedJob;
    }

    // Getters
    public function id(): ?string
    {
        return $this->id;
    }

    public function queue(): string
    {
        return $this->queue;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function exception(): string
    {
        return $this->exception;
    }

    public function failedAt(): string
    {
        return $this->failedAt;
    }

    // Setters
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue,
            'payload' => json_encode($this->payload),
            'exception' => $this->exception,
            'failed_at' => $this->failedAt,
        ];
    }
}
