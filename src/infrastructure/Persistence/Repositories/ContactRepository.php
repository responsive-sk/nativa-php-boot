<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Contact;
use Domain\Repository\ContactRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;
use PDO;

/**
 * Contact Repository Implementation
 */
class ContactRepository implements ContactRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(Contact $contact): void
    {
        $data = $contact->toArray();

        $sql = <<<SQL
            INSERT INTO contacts (id, name, email, subject, message, status, created_at)
            VALUES (:id, :name, :email, :subject, :message, :status, :created_at)
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function findById(string $id): ?Contact
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Contact::fromArray($data);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM contacts
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        SQL);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Contact::fromArray($row), $stmt->fetchAll());
    }

    public function findByStatus(string $status, int $limit = 50): array
    {
        $stmt = $this->uow->getConnection()->prepare(<<<SQL
            SELECT * FROM contacts
            WHERE status = :status
            ORDER BY created_at DESC
            LIMIT :limit
        SQL);

        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => Contact::fromArray($row), $stmt->fetchAll());
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT COUNT(*) FROM contacts WHERE status = ?'
        );
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM contacts');
        return (int) $stmt->fetchColumn();
    }
}
