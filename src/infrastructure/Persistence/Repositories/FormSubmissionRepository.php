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

    public function saveSubmission(
        string $formId,
        array $data,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void {
        $submission = FormSubmission::create($formId, $data, $ipAddress, $userAgent);
        $this->save($submission);
    }

    public function findById(string $id): ?FormSubmission
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM form_submissions WHERE id = ?'
        );
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return FormSubmission::fromArray($data);
    }

    public function findByFormId(string $formId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM form_submissions WHERE form_id = ? ORDER BY submitted_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':form_id', $formId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

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

    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare(
            'DELETE FROM form_submissions WHERE id = ?'
        );
        $stmt->execute([$id]);
    }

    public function deleteByFormId(string $formId): void
    {
        $stmt = $this->uow->getConnection()->prepare(
            'DELETE FROM form_submissions WHERE form_id = ?'
        );
        $stmt->execute([$formId]);
    }
}
