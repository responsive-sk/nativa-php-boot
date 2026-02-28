<?php

declare(strict_types=1);

namespace Application\Saga;

/**
 * Saga Orchestrator - Coordinates saga execution and rollback
 */
class SagaOrchestrator
{
    /** @var array<SagaStepInterface> */
    private array $steps = [];
    private int $currentStep = 0;
    private bool $completed = false;
    private bool $compensating = false;

    /**
     * Add a step to the saga
     */
    public function addStep(SagaStepInterface $step): self
    {
        if ($this->completed) {
            throw new \RuntimeException('Cannot add step to completed saga');
        }
        $this->steps[] = $step;
        return $this;
    }

    /**
     * Execute all steps in order
     *
     * @return array<int, mixed> Results from each step
     * @throws SagaException If any step fails (with compensation already executed)
     */
    public function execute(): array
    {
        $results = [];

        try {
            foreach ($this->steps as $index => $step) {
                $this->currentStep = $index;
                
                try {
                    $results[] = $step->executeWithTracking();
                } catch (\Throwable $e) {
                    throw new SagaExecutionFailedException(
                        "Step '{$step->getName()}' failed: {$e->getMessage()}",
                        $step,
                        $index,
                        $e
                    );
                }
            }

            $this->completed = true;
            return $results;

        } catch (SagaExecutionFailedException $e) {
            // Trigger compensation (rollback)
            $this->compensate();
            throw $e;
        }
    }

    /**
     * Compensate (rollback) all executed steps in reverse order
     */
    public function compensate(): void
    {
        if ($this->compensating) {
            return; // Prevent recursive compensation
        }

        $this->compensating = true;

        // Rollback in reverse order
        for ($i = $this->currentStep; $i >= 0; $i--) {
            $step = $this->steps[$i];
            
            try {
                $step->compensateWithTracking();
            } catch (\Throwable $e) {
                // Log compensation failure but continue
                error_log(sprintf(
                    "Compensation failed for step '%s': %s",
                    $step->getName(),
                    $e->getMessage()
                ));
            }
        }

        $this->compensating = false;
    }

    /**
     * Check if saga is completed
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * Get all steps
     *
     * @return array<SagaStepInterface>
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Get current step index
     */
    public function getCurrentStep(): int
    {
        return $this->currentStep;
    }

    /**
     * Get saga status for logging/debugging
     *
     * @return array<string, mixed>
     */
    public function getStatus(): array
    {
        $status = [
            'completed' => $this->completed,
            'compensating' => $this->compensating,
            'current_step' => $this->currentStep,
            'steps' => [],
        ];

        foreach ($this->steps as $index => $step) {
            $status['steps'][] = [
                'index' => $index,
                'name' => $step->getName(),
                'executed' => $step->isExecuted(),
                'compensated' => $step->isCompensated(),
            ];
        }

        return $status;
    }
}
