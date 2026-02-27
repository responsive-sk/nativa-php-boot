<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Model\User;

/**
 * User Repository Interface
 */
interface UserRepositoryInterface
{
    /**
     * Save user
     */
    public function save(User $user): void;

    /**
     * Delete user by ID
     */
    public function delete(string $id): void;

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
     * @return array<User>
     */
    public function findAll(): array;

    /**
     * Find users by role
     *
     * @return array<User>
     */
    public function findByRole(string $role): array;

    /**
     * Count all users
     */
    public function count(): int;
}
