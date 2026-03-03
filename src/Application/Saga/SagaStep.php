<?php

declare(strict_types = 1);

namespace Application\Saga;

/**
 * Abstract Saga Step - Base class for saga steps.
 */
abstract class SagaStep implements SagaStepInterface
{
    private bool $executed = false;

    private bool $compensated = false;

    private mixed $result = null;

    /**
     * Get step name from class name.
     */
    #[\Override]
    final public function getName(): string
    {
        // Convert "PublishArticleStep" to "Publish Article"
        $name = str_replace('Step', '', static::class);
        $name = preg_replace('/(?<!^)[A-Z]/', ' $0', $name);

        return null !== $name ? $name : static::class;
    }

    /**
     * Check if step was executed.
     */
    #[\Override]
    final public function isExecuted(): bool
    {
        return $this->executed;
    }

    /**
     * Check if step was compensated.
     */
    #[\Override]
    final public function isCompensated(): bool
    {
        return $this->compensated;
    }

    /**
     * Get execution result.
     */
    final public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Execute with tracking.
     */
    #[\Override]
    final public function executeWithTracking(): mixed
    {
        $this->result = $this->execute();
        $this->executed = true;

        return $this->result;
    }

    /**
     * Compensate with tracking.
     */
    #[\Override]
    final public function compensateWithTracking(): void
    {
        if (!$this->executed || $this->compensated) {
            return;
        }

        $this->compensate();
        $this->compensated = true;
    }
}
