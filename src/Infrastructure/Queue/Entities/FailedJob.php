<?php

declare(strict_types = 1);

namespace Infrastructure\Queue\Entities;

/**
 * Failed Job Entity.
 */
final class FailedJob
{
    private ?string $id = null;

    private string $queue;

    /** @var array<string, mixed> */
    private array $payload;

    private string $exception;

    private string $failedAt;

    /**
     * @param array<string, mixed> $payload
     */
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

    /**
     * @param array<string, mixed> $data
     */
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'queue'     => $this->queue,
            'payload'   => json_encode($this->payload),
            'exception' => $this->exception,
            'failed_at' => $this->failedAt,
        ];
    }
}
