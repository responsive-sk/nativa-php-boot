<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Model\User;
use Domain\Model\Role;
use Domain\Model\Permission;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Persistence\UnitOfWork;

/**
 * User Repository Implementation
 */
 final class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly UnitOfWork $uow
    ) {
    }

    public function save(User $user): void
    {
        $data = $user->toArray();

        $sql = <<<SQL
            INSERT INTO users (id, name, email, password, role, avatar, is_active, last_login_at, last_login_ip, created_at, updated_at)
            VALUES (:id, :name, :email, :password, :role, :avatar, :is_active, :last_login_at, :last_login_ip, :created_at, :updated_at)
            ON CONFLICT(id) DO UPDATE SET
                name = excluded.name,
                email = excluded.email,
                password = excluded.password,
                role = excluded.role,
                avatar = excluded.avatar,
                is_active = excluded.is_active,
                last_login_at = excluded.last_login_at,
                last_login_ip = excluded.last_login_ip,
                updated_at = excluded.updated_at
        SQL;

        $stmt = $this->uow->getConnection()->prepare($sql);
        $stmt->execute($data);
    }

    public function findById(string $id): ?User
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $user = User::fromArray($data);
        
        // Load user's assigned roles and permissions
        $this->loadUserRolesAndPermissions($user);
        
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->uow->getConnection()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $user = User::fromArray($data);
        
        // Load user's assigned roles and permissions
        $this->loadUserRolesAndPermissions($user);
        
        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->uow->getConnection()->query('SELECT * FROM users ORDER BY created_at DESC');

        $users = array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
        
        // Load roles and permissions for each user
        foreach ($users as $user) {
            $this->loadUserRolesAndPermissions($user);
        }
        
        return $users;
    }

    public function findByRole(string $role): array
    {
        $stmt = $this->uow->getConnection()->prepare(
            'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$role]);

        $users = array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
        
        // Load roles and permissions for each user
        foreach ($users as $user) {
            $this->loadUserRolesAndPermissions($user);
        }
        
        return $users;
    }

    public function findActive(): array
    {
        $stmt = $this->uow->getConnection()->query(
            'SELECT * FROM users WHERE is_active = 1 ORDER BY created_at DESC'
        );

        $users = array_map(function ($row) {
            return User::fromArray($row);
        }, $stmt->fetchAll());
        
        // Load roles and permissions for each user
        foreach ($users as $user) {
            $this->loadUserRolesAndPermissions($user);
        }
        
        return $users;
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

    /**
     * Load user's assigned roles and permissions from pivot tables
     */
    private function loadUserRolesAndPermissions(User $user): void
    {
        $userId = $user->id();
        $conn = $this->uow->getConnection();
        
        // Check if pivot tables exist
        try {
            // Load assigned roles from role_user pivot table
            $stmt = $conn->prepare(<<<SQL
                SELECT r.* FROM roles r
                INNER JOIN role_user ru ON r.id = ru.role_id
                WHERE ru.user_id = ?
            SQL);
            $stmt->execute([$userId]);
            $roleRows = $stmt->fetchAll();

            $roles = [];
            foreach ($roleRows as $row) {
                $role = Role::fromArray($row);
                // Load permissions for this role
                $this->loadRolePermissions($role);
                $roles[] = $role;
            }
            $user->setAssignedRoles($roles);
        } catch (\Throwable $e) {
            // Pivot tables don't exist yet, use empty arrays
            error_log('[UserRepository] Could not load user roles: ' . $e->getMessage());
            $user->setAssignedRoles([]);
        }

        // Load direct permissions from user_permission pivot table (if exists)
        // For now, users only get permissions through roles
        $user->setPermissions([]);
    }

    /**
     * Load role's permissions from permission_role pivot table
     */
    private function loadRolePermissions(Role $role): void
    {
        $roleId = $role->id();
        $conn = $this->uow->getConnection();

        try {
            $stmt = $conn->prepare(<<<SQL
                SELECT p.* FROM permissions p
                INNER JOIN permission_role pr ON p.id = pr.permission_id
                WHERE pr.role_id = ?
            SQL);
            $stmt->execute([$roleId]);
            $permissionRows = $stmt->fetchAll();

            $permissions = [];
            foreach ($permissionRows as $row) {
                $permissions[] = Permission::fromArray($row);
            }
            $role->setPermissions($permissions);
        } catch (\Throwable $e) {
            // Pivot table doesn't exist, use empty array
            error_log('[UserRepository] Could not load role permissions: ' . $e->getMessage());
            $role->setPermissions([]);
        }
    }
}
