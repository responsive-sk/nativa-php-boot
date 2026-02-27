<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Form;
use Domain\Repository\FormRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Form Repository Implementation
 */
class FormRepository implements FormRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(Form $form): void
    {
        $data = $form->toArray();

        $sql = <<<SQL
            INSERT INTO forms (
                id, name, slug, schema, email_notification,
                success_message, created_at, updated_at
            ) VALUES (
                :id, :name, :slug, :schema, :email_notification,
                :success_message, :created_at, :updated_at
            )
            ON CONFLICT(id) DO UPDATE SET
                name = excluded.name,
                slug = excluded.slug,
                schema = excluded.schema,
                email_notification = excluded.email_notification,
                success_message = excluded.success_message,
                updated_at = excluded.updated_at
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM forms WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findById(string $id): ?Form
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM forms WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? Form::fromArray($data) : null;
    }

    public function findBySlug(string $slug): ?Form
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM forms WHERE slug = ?');
        $stmt->execute([$slug]);
        $data = $stmt->fetch();

        return $data ? Form::fromArray($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM forms ORDER BY created_at DESC');
        return array_map(fn($row) => Form::fromArray($row), $stmt->fetchAll());
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM forms');
        return (int) $stmt->fetchColumn();
    }
}
