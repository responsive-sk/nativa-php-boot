<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\FormSubmission;
use Domain\Repository\FormSubmissionRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Form Submission Repository Implementation
 */
class FormSubmissionRepository implements FormSubmissionRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(FormSubmission $submission): void
    {
        $data = $submission->toArray();

        $sql = <<<SQL
            INSERT INTO form_submissions (
                id, form_id, data, ip_address, user_agent, submitted_at
            ) VALUES (
                :id, :form_id, :data, :ip_address, :user_agent, :submitted_at
            )
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function findByFormId(string $formId): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM form_submissions WHERE form_id = ? ORDER BY submitted_at DESC'
        );
        $stmt->execute([$formId]);

        return array_map(fn($row) => FormSubmission::fromArray($row), $stmt->fetchAll());
    }

    public function countByFormId(string $formId): int
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT COUNT(*) FROM form_submissions WHERE form_id = ?'
        );
        $stmt->execute([$formId]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteByFormId(string $formId): void
    {
        $stmt = $this->uow->getConnection()->prepare(
            'DELETE FROM form_submissions WHERE form_id = ?'
        );
        $stmt->execute([$formId]);
    }
}
