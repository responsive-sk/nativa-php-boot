<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\Permission;
use Domain\Repository\PermissionRepositoryInterface;
use Domain\ValueObjects\PermissionName;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Permission Repository Implementation
 */
 final class PermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow,
    ) {
    }

    public function findById(string $id): ?Permission
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM permissions WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Permission::fromArray($data);
    }

    public function findByName(PermissionName $name): ?Permission
    {
        return $this->findByNameString($name->name());
    }

    public function findByNameString(string $name): ?Permission
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM permissions WHERE name = ?');
        $stmt->execute([$name]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Permission::fromArray($data);
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM permissions ORDER BY group_name, name');

        return array_map(function ($row) {
            return Permission::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findByGroup(string $group): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM permissions WHERE group_name = ? ORDER BY name'
        );
        $stmt->execute([$group]);

        return array_map(function ($row) {
            return Permission::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function findByResourcePattern(string $pattern): array
    {
        // Convert pattern to SQL LIKE
        // e.g., "admin.*" → "admin.%"
        $likePattern = str_replace('*', '%', $pattern);

        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM permissions WHERE name LIKE ? ORDER BY name'
        );
        $stmt->execute([$likePattern]);

        return array_map(function ($row) {
            return Permission::fromArray($row);
        }, $stmt->fetchAll());
    }

    public function save(Permission $permission): void
    {
        $data = $permission->toArray();

        $sql = <<<SQL
            INSERT INTO permissions (id, name, description, group_name, created_at)
            VALUES (:id, :name, :description, :group_name, :created_at)
            ON CONFLICT(id) DO UPDATE SET
                name = excluded.name,
                description = excluded.description,
                group_name = excluded.group_name
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(Permission $permission): void
    {
        $stmt = $this->uow->getConnection()->prepare('DELETE FROM permissions WHERE id = ?');
        $stmt->execute([$permission->id()]);
    }

    public function getOrCreate(PermissionName $name, ?string $description = null, string $group = 'default'): Permission
    {
        $permission = $this->findByName($name);

        if ($permission === null) {
            $permission = Permission::create($name, $description, $group);
            $this->save($permission);
        }

        return $permission;
    }
}
