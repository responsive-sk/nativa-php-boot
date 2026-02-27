<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\User;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;

/**
 * User Repository Implementation
 */
class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(User $user): void
    {
        $data = $user->toArray();

        $sql = <<<SQL
            INSERT INTO users (id, name, email, password, role, avatar, created_at, updated_at)
            VALUES (:id, :name, :email, :password, :role, :avatar, :created_at, :updated_at)
            ON CONFLICT(id) DO UPDATE SET
                name = excluded.name,
                email = excluded.email,
                password = excluded.password,
                role = excluded.role,
                avatar = excluded.avatar,
                updated_at = excluded.updated_at
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(string $id): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function findById(string $id): ?User
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return User::fromArray($data);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return User::fromArray($data);
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM users ORDER BY created_at DESC');

        return array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findByRole(string $role): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$role]);

        return array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findActive(): array
    {
        $stmt = $this->uow->getConnection()->query(
            'SELECT * FROM users WHERE is_active = 1 ORDER BY created_at DESC'
        );

        return array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [$email];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function delete(User $user): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$user->id()]);
    }

    public function count(): int
    {
        $stmt = $this->uow->getConnection()->query('SELECT COUNT(*) FROM users');
        return (int) $stmt->fetchColumn();
    }
}
