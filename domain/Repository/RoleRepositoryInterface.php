<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\Role;
use Domain\ValueObjects\Role as RoleVO;

/**
 * RoleRepository Interface
 */
interface RoleRepositoryInterface
{
    /**
     * Find role by ID
     */
    public function findById(string $id): ?Role;

    /**
     * Find role by name
     */
    public function findByName(RoleVO $name): ?Role;

    /**
     * Find role by name string
     */
    public function findByNameString(string $name): ?Role;

    /**
     * Find all roles
     *
     * @return Role[]
     */
    public function findAll(): array;

    /**
     * Save role
     */
    public function save(Role $role): void;

    /**
     * Delete role
     */
    public function delete(Role $role): void;

    /**
     * Get or create role by name
     * Creates if doesn't exist
     */
    public function getOrCreate(RoleVO $name, ?string $description = null): Role;
}
