<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\User;

/**
 * UserRepository Interface
 */
interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function findById(string $id): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find all users
     *
     * @return User[]
     */
    public function findAll(): array;

    /**
     * Find users by role
     *
     * @return User[]
     */
    public function findByRole(string $role): array;

    /**
     * Find active users
     *
     * @return User[]
     */
    public function findActive(): array;

    /**
     * Save user
     */
    public function save(User $user): void;

    /**
     * Delete user
     */
    public function delete(User $user): void;

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?string $excludeId = null): bool;
}
