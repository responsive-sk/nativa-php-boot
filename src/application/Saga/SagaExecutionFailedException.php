<?php

declare(strict_types=1);

namespace Application\Saga;

/**
 * Saga Execution Failed Exception
 */
class SagaExecutionFailedException extends SagaException
{
    public function __construct(
        string $message,
        private readonly SagaStepInterface $failedStep,
        private readonly int $failedStepIndex,
        \Throwable $previous,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getFailedStep(): SagaStepInterface
    {
        return $this->failedStep;
    }

    public function getFailedStepIndex(): int
    {
        return $this->failedStepIndex;
    }
}
