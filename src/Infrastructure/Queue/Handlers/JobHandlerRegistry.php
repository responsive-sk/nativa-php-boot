<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Handlers;

/**
 * Job Handler Registry
 */
class JobHandlerRegistry
{
    /** @var array<string, callable> */
    private array $handlers = [];

    /**
     * Register a job handler
     */
    public function register(string $jobName, callable $handler): void
    {
        $this->handlers[$jobName] = $handler;
    }

    /**
     * Get handler for a job name
     */
    public function getHandler(string $jobName): ?callable
    {
        return $this->handlers[$jobName] ?? null;
    }

    /**
     * Check if handler exists
     */
    public function hasHandler(string $jobName): bool
    {
        return isset($this->handlers[$jobName]);
    }

    /**
     * Get all registered handlers
     *
     * @return array<string>
     */
    public function getHandlerNames(): array
    {
        return array_keys($this->handlers);
    }
}
