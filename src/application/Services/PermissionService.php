<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\Permission;
use Domain\Repository\PermissionRepositoryInterface;
use Domain\ValueObjects\PermissionName;

/**
 * Permission Service
 *
 * Handles permission management operations
 */
final class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository,
    ) {
    }

    /**
     * Create a new permission
     */
    public function createPermission(
        string $name,
        ?string $description = null,
        string $group = 'default'
    ): Permission {
        $this->logDebug('[PermissionService::createPermission] Creating permission: ' . $name);

        $permissionName = PermissionName::fromString($name);
        
        // Check if permission already exists
        $existing = $this->permissionRepository->findByName($permissionName);
        if ($existing !== null) {
            throw new \RuntimeException('Permission already exists: ' . $name);
        }

        $permission = Permission::create($permissionName, $description, $group);
        $this->permissionRepository->save($permission);

        $this->logDebug('[PermissionService::createPermission] Permission created: ' . $permission->id());

        return $permission;
    }

    /**
     * Get or create permission by name
     */
    public function getOrCreatePermission(
        string $name,
        ?string $description = null,
        string $group = 'default'
    ): Permission {
        $this->logDebug('[PermissionService::getOrCreatePermission] Getting or creating permission: ' . $name);

        $permission = $this->permissionRepository->getOrCreate(
            PermissionName::fromString($name),
            $description,
            $group
        );

        $this->logDebug('[PermissionService::getOrCreatePermission] Permission: ' . $permission->id());

        return $permission;
    }

    /**
     * Find permission by ID
     */
    public function findPermissionById(string $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }

    /**
     * Find permission by name
     */
    public function findPermissionByName(string $name): ?Permission
    {
        return $this->permissionRepository->findByNameString($name);
    }

    /**
     * Get all permissions
     *
     * @return Permission[]
     */
    public function getAllPermissions(): array
    {
        return $this->permissionRepository->findAll();
    }

    /**
     * Get permissions by group
     *
     * @return Permission[]
     */
    public function getPermissionsByGroup(string $group): array
    {
        return $this->permissionRepository->findByGroup($group);
    }

    /**
     * Get permissions matching resource pattern
     * e.g., "admin.*" returns all admin permissions
     *
     * @return Permission[]
     */
    public function getPermissionsByPattern(string $pattern): array
    {
        return $this->permissionRepository->findByResourcePattern($pattern);
    }

    /**
     * Update permission description
     */
    public function updatePermissionDescription(string $permissionId, ?string $description): Permission
    {
        $this->logDebug('[PermissionService::updatePermissionDescription] Updating permission: ' . $permissionId);

        $permission = $this->permissionRepository->findById($permissionId);

        if ($permission === null) {
            throw new \RuntimeException('Permission not found: ' . $permissionId);
        }

        $permission->updateDescription($description);
        $this->permissionRepository->save($permission);

        $this->logDebug('[PermissionService::updatePermissionDescription] Permission updated');

        return $permission;
    }

    /**
     * Delete permission
     */
    public function deletePermission(string $permissionId): void
    {
        $this->logDebug('[PermissionService::deletePermission] Deleting permission: ' . $permissionId);

        $permission = $this->permissionRepository->findById($permissionId);

        if ($permission === null) {
            throw new \RuntimeException('Permission not found: ' . $permissionId);
        }

        $this->permissionRepository->delete($permission);

        $this->logDebug('[PermissionService::deletePermission] Permission deleted');
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
