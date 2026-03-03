<?php

declare(strict_types = 1);

namespace Application\Saga;

/**
 * Saga Step Interface - Represents a single step in a saga.
 */
interface SagaStepInterface
{
    /**
     * Execute the step.
     *
     * @throws \Throwable If step fails
     *
     * @return mixed Result of the execution
     */
    public function execute(): mixed;

    /**
     * Compensate (rollback) the step
     * Called when a subsequent step fails.
     */
    public function compensate(): void;

    /**
     * Get step name for logging.
     */
    public function getName(): string;

    /**
     * Execute with tracking (records execution state).
     *
     * @return mixed Result of the execution
     */
    public function executeWithTracking(): mixed;

    /**
     * Compensate with tracking (records compensation state).
     */
    public function compensateWithTracking(): void;

    /**
     * Check if step was executed.
     */
    public function isExecuted(): bool;

    /**
     * Check if step was compensated.
     */
    public function isCompensated(): bool;
}
