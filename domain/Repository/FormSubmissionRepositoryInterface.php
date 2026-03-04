<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\FormSubmission;

/**
 * Form Submission Repository Interface
 */
interface FormSubmissionRepositoryInterface
{
    /**
     * Save form submission
     */
    public function save(FormSubmission $submission): void;

    /**
     * Save submission (convenience method)
     */
    public function saveSubmission(
        string $formId,
        array $data,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void;

    /**
     * Find submission by ID
     */
    public function findById(string $id): ?FormSubmission;

    /**
     * Find submissions by form ID
     *
     * @return array<FormSubmission>
     */
    public function findByFormId(string $formId, int $limit = 20, int $offset = 0): array;

    /**
     * Count submissions by form ID
     */
    public function countByFormId(string $formId): int;

    /**
     * Delete submission
     */
    public function delete(string $id): void;

    public function deleteByFormId(string $formId): void;
}
