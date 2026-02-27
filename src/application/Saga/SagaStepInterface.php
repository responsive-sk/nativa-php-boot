<?php

declare(strict_types=1);

namespace Application\Saga;

/**
 * Saga Step Interface - Represents a single step in a saga
 */
interface SagaStepInterface
{
    /**
     * Execute the step
     * @return mixed Result of the execution
     * @throws \Throwable If step fails
     */
    public function execute(): mixed;

    /**
     * Compensate (rollback) the step
     * Called when a subsequent step fails
     */
    public function compensate(): void;

    /**
     * Get step name for logging
     */
    public function getName(): string;
}
