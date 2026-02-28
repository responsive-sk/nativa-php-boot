<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Role;
use Domain\Repository\RoleRepositoryInterface;
use Domain\ValueObjects\Role as RoleVO;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Role Repository Implementation
 */
 final class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow,
    ) {
    }

    public function findById(string $id): ?Role
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM roles WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Role::fromArray($data);
    }

    public function findByName(RoleVO $name): ?Role
    {
        return $this->findByNameString($name->name());
    }

    public function findByNameString(string $name): ?Role
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM roles WHERE name = ?');
        $stmt->execute([$name]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Role::fromArray($data);
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM roles ORDER BY name');

        return array_map(function ($row) {
            return Role::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function save(Role $role): void
    {
        $data = $role->toArray();

        $sql = <<<SQL
            INSERT INTO roles (id, name, description, created_at)
            VALUES (:id, :name, :description, :created_at)
            ON CONFLICT(id) DO UPDATE SET
                name = excluded.name,
                description = excluded.description
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(Role $role): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM roles WHERE id = ?');
        $stmt->execute([$role->id()]);
    }

    public function getOrCreate(RoleVO $name, ?string $description = null): Role
    {
        $role = $this->findByName($name);

        if ($role === null) {
            $role = Role::create($name, $description);
            $this->save($role);
        }

        return $role;
    }
}
