<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\FormSubmission;

/**
 * Form Submission Repository Interface
 */
interface FormSubmissionRepositoryInterface
{
    public function save(FormSubmission $submission): void;

    public function findByFormId(string $formId): array;

    public function countByFormId(string $formId): int;

    public function deleteByFormId(string $formId): void;
}
