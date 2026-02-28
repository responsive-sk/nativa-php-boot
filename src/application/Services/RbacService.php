<?php

declare(strict_types=1);

namespace Application\Services;

use Domain\Model\User;
use Domain\Model\Role;
use Domain\Repository\UserRepositoryInterface;
use Domain\Repository\RoleRepositoryInterface;

/**
 * RBAC Service
 *
 * Handles role-based access control operations
 */
final class RbacService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(string $userId, string $roleId): void
    {
        $this->logDebug('[RbacService::assignRoleToUser] Assigning role to user: ' . $userId);

        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new \RuntimeException('User not found: ' . $userId);
        }

        $role = $this->roleRepository->findById($roleId);
        if ($role === null) {
            throw new \RuntimeException('Role not found: ' . $roleId);
        }

        // Save to pivot table
        $stmt = $this->getConnection()->prepare(<<<SQL
            INSERT OR IGNORE INTO role_user (role_id, user_id)
            VALUES (?, ?)
        SQL);
        $stmt->execute([$roleId, $userId]);

        // Also update user's primary role if it's a higher level
        if ($role->getLevel() > $user->role()->getLevel()) {
            $this->updateUserRole($user, $role->nameString());
        }

        $this->logDebug('[RbacService::assignRoleToUser] Role assigned');
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(string $userId, string $roleId): void
    {
        $this->logDebug('[RbacService::removeRoleFromUser] Removing role from user: ' . $userId);

        // Delete from pivot table
        $stmt = $this->getConnection()->prepare(
            'DELETE FROM role_user WHERE role_id = ? AND user_id = ?'
        );
        $stmt->execute([$roleId, $userId]);

        $this->logDebug('[RbacService::removeRoleFromUser] Role removed');
    }

    /**
     * Update user's primary role
     */
    public function updateUserRole(User $user, string $roleName): User
    {
        $this->logDebug('[RbacService::updateUserRole] Updating user role: ' . $user->id());

        // Update user's role value object using domain method
        $user->changeRole(\Domain\ValueObjects\Role::fromString($roleName));

        // Save user
        $this->userRepository->save($user);

        $this->logDebug('[RbacService::updateUserRole] Role updated to: ' . $roleName);

        return $user;
    }

    /**
     * Get all roles for a user
     *
     * @return Role[]
     */
    public function getUserRoles(User $user): array
    {
        return $user->getAssignedRoles();
    }

    /**
     * Get all users with a specific role
     *
     * @return User[]
     */
    public function getUsersByRole(string $roleName): array
    {
        return $this->userRepository->findByRole($roleName);
    }

    /**
     * Check if user has role
     */
    public function userHasRole(User $user, string $roleName): bool
    {
        $roleVO = \Domain\ValueObjects\Role::fromString($roleName);
        
        // Check primary role
        if ($user->role()->equals($roleVO)) {
            return true;
        }

        // Check assigned roles
        foreach ($user->getAssignedRoles() as $role) {
            if ($role->name()->equals($roleVO)) {
                return true;
            }
        }

        return false;
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
