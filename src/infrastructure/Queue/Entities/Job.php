<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Entities;

/**
 * Job Entity
 */
class Job
{
    private ?string $id = null;
    private string $queue;
    private array $payload;
    private int $attempts;
    private ?string $reservedAt;
    private ?string $availableAt;
    private string $createdAt;

    public function __construct(
        string $queue,
        array $payload,
        int $attempts = 0,
        ?string $reservedAt = null,
        ?string $availableAt = null
    ) {
        $this->queue = $queue;
        $this->payload = $payload;
        $this->attempts = $attempts;
        $this->reservedAt = $reservedAt;
        $this->availableAt = $availableAt ?? date('Y-m-d H:i:s');
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public static function fromArray(array $data): self
    {
        $job = new self(
            queue: $data['queue'],
            payload: json_decode($data['payload'], true),
            attempts: (int) $data['attempts'],
            reservedAt: $data['reserved_at'],
            availableAt: $data['available_at']
        );
        $job->setId($data['id']);
        $job->createdAt = $data['created_at'];
        return $job;
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

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function reservedAt(): ?string
    {
        return $this->reservedAt;
    }

    public function availableAt(): ?string
    {
        return $this->availableAt;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    // Setters
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setReservedAt(?string $reservedAt): void
    {
        $this->reservedAt = $reservedAt;
    }

    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    // Business logic
    public function isAvailable(): bool
    {
        if ($this->availableAt === null) {
            return false;
        }
        return strtotime($this->availableAt) <= time();
    }

    public function isReserved(): bool
    {
        return $this->reservedAt !== null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue,
            'payload' => json_encode($this->payload),
            'attempts' => $this->attempts,
            'reserved_at' => $this->reservedAt,
            'available_at' => $this->availableAt,
            'created_at' => $this->createdAt,
        ];
    }
}
