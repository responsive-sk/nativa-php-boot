<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\Role;
use Domain\Repository\RoleRepositoryInterface;
use Domain\Repository\PermissionRepositoryInterface;
use Domain\ValueObjects\Role as RoleVO;
use Domain\ValueObjects\PermissionName;

/**
 * Role Service
 *
 * Handles role management operations
 */
final class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionRepositoryInterface $permissionRepository,
    ) {
    }

    /**
     * Create a new role
     */
    public function createRole(string $name, ?string $description = null): Role
    {
        $this->logDebug('[RoleService::createRole] Creating role: ' . $name);

        $roleVO = RoleVO::fromString($name);
        
        // Check if role already exists
        $existing = $this->roleRepository->findByName($roleVO);
        if ($existing !== null) {
            throw new \RuntimeException('Role already exists: ' . $name);
        }

        $role = Role::create($roleVO, $description);
        $this->roleRepository->save($role);

        $this->logDebug('[RoleService::createRole] Role created: ' . $role->id());

        return $role;
    }

    /**
     * Get or create role by name
     */
    public function getOrCreateRole(string $name, ?string $description = null): Role
    {
        $this->logDebug('[RoleService::getOrCreateRole] Getting or creating role: ' . $name);

        $role = $this->roleRepository->getOrCreate(RoleVO::fromString($name), $description);

        $this->logDebug('[RoleService::getOrCreateRole] Role: ' . $role->id());

        return $role;
    }

    /**
     * Find role by ID
     */
    public function findRoleById(string $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    /**
     * Find role by name
     */
    public function findRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findByNameString($name);
    }

    /**
     * Get all roles
     *
     * @return Role[]
     */
    public function getAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }

    /**
     * Update role description
     */
    public function updateRoleDescription(string $roleId, ?string $description): Role
    {
        $this->logDebug('[RoleService::updateRoleDescription] Updating role: ' . $roleId);

        $role = $this->roleRepository->findById($roleId);

        if ($role === null) {
            throw new \RuntimeException('Role not found: ' . $roleId);
        }

        $role->updateDescription($description);
        $this->roleRepository->save($role);

        $this->logDebug('[RoleService::updateRoleDescription] Role updated');

        return $role;
    }

    /**
     * Delete role
     */
    public function deleteRole(string $roleId): void
    {
        $this->logDebug('[RoleService::deleteRole] Deleting role: ' . $roleId);

        $role = $this->roleRepository->findById($roleId);

        if ($role === null) {
            throw new \RuntimeException('Role not found: ' . $roleId);
        }

        // Prevent deletion of system roles
        if (in_array($role->nameString(), ['admin', 'editor', 'viewer', 'user'], true)) {
            throw new \RuntimeException('Cannot delete system role: ' . $role->nameString());
        }

        $this->roleRepository->delete($role);

        $this->logDebug('[RoleService::deleteRole] Role deleted');
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(string $roleId, string $permissionName): Role
    {
        $this->logDebug('[RoleService::assignPermissionToRole] Assigning permission to role: ' . $roleId);

        $role = $this->roleRepository->findById($roleId);

        if ($role === null) {
            throw new \RuntimeException('Role not found: ' . $roleId);
        }

        // Get or create permission
        $permission = $this->permissionRepository->getOrCreate(
            PermissionName::fromString($permissionName),
            null,
            explode('.', $permissionName)[0] // group from resource part
        );

        // Assign permission to role
        $role->assignPermission($permission);

        // Save role-permission relationship in pivot table
        $this->saveRolePermission($role, $permission);

        $this->logDebug('[RoleService::assignPermissionToRole] Permission assigned');

        return $role;
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(string $roleId, string $permissionId): void
    {
        $this->logDebug('[RoleService::removePermissionFromRole] Removing permission from role: ' . $roleId);

        // Delete from pivot table
        $stmt = $this->getConnection()->prepare(
            'DELETE FROM permission_role WHERE role_id = ? AND permission_id = ?'
        );
        $stmt->execute([$roleId, $permissionId]);

        $this->logDebug('[RoleService::removePermissionFromRole] Permission removed');
    }

    /**
     * Save role-permission relationship
     */
    private function saveRolePermission(Role $role, \Domain\Model\Permission $permission): void
    {
        $stmt = $this->getConnection()->prepare(<<<SQL
            INSERT OR IGNORE INTO permission_role (permission_id, role_id)
            VALUES (?, ?)
        SQL);
        $stmt->execute([$permission->id(), $role->id()]);
    }

    /**
     * Get database connection
     */
    private function getConnection(): \PDO
    {
        return \Infrastructure\Persistence\DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * Debug logging
     */
    private function logDebug(string $message): void
    {
        if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
            error_log($message);
        }
    }
}
