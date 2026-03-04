<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Permission;
use Domain\ValueObjects\PermissionName;

/**
 * PermissionRepository Interface
 */
interface PermissionRepositoryInterface
{
    /**
     * Find permission by ID
     */
    public function findById(string $id): ?Permission;

    /**
     * Find permission by name
     */
    public function findByName(PermissionName $name): ?Permission;

    /**
     * Find permission by name string
     */
    public function findByNameString(string $name): ?Permission;

    /**
     * Find all permissions
     *
     * @return Permission[]
     */
    public function findAll(): array;

    /**
     * Find permissions by group
     *
     * @return Permission[]
     */
    public function findByGroup(string $group): array;

    /**
     * Find permissions by resource pattern
     * e.g., "admin.*" returns all admin permissions
     *
     * @return Permission[]
     */
    public function findByResourcePattern(string $pattern): array;

    /**
     * Save permission
     */
    public function save(Permission $permission): void;

    /**
     * Delete permission
     */
    public function delete(Permission $permission): void;

    /**
     * Get or create permission by name
     * Creates if doesn't exist
     */
    public function getOrCreate(PermissionName $name, ?string $description = null, string $group = 'default'): Permission;
}
