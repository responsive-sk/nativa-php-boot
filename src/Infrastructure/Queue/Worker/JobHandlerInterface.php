<?php

declare(strict_types=1);

namespace Infrastructure\Queue\Worker;

use Infrastructure\Queue\Entities\Job;

/**
 * Job Handler Interface
 */
interface JobHandlerInterface
{
    /**
     * Handle a job
     */
    public function handle(Job $job): void;
}
