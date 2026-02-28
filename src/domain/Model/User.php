<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\ValueObjects\Email;
use Domain\ValueObjects\Password;
use Domain\ValueObjects\Role as RoleVO;
use Domain\ValueObjects\PermissionName;

/**
 * User Entity
 *
 * Represents a user with role-based access control (RBAC)
 */
final class User
{
    private string $id;
    private string $name;
    private Email $email;
    private Password $password;
    private RoleVO $role;
    private ?string $avatar;
    private bool $isActive;
    private ?string $lastLoginAt;
    private ?string $lastLoginIp;
    private string $createdAt;
    private string $updatedAt;

    /**
     * @var Role[] User's assigned roles (from role_user pivot)
     */
    private array $assignedRoles = [];

    /**
     * @var Permission[] User's direct permissions (cached)
     */
    private array $permissions = [];

    /**
     * @var array<string, bool> Cache for permission checks (permission_name => result)
     */
    private array $permissionCache = [];

    private function __construct()
    {
    }

    public static function create(
        string $name,
        Email $email,
        Password $password,
        ?RoleVO $role = null,
        ?string $avatar = null,
    ): self {
        $user = new self();
        $user->id = self::generateId();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role ?? RoleVO::user();
        $user->avatar = $avatar;
        $user->isActive = true;
        $user->createdAt = self::now();
        $user->updatedAt = self::now();
        $user->lastLoginAt = null;
        $user->lastLoginIp = null;

        return $user;
    }

    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = $data['id'];
        $user->name = $data['name'];
        $user->email = Email::fromString($data['email']);
        $user->password = Password::fromHash($data['password']);
        $user->role = RoleVO::fromString($data['role'] ?? 'user');
        $user->avatar = $data['avatar'] ?? null;
        $user->isActive = (bool) ($data['is_active'] ?? true);
        $user->lastLoginAt = $data['last_login_at'] ?? null;
        $user->lastLoginIp = $data['last_login_ip'] ?? null;
        $user->createdAt = $data['created_at'];
        $user->updatedAt = $data['updated_at'];

        return $user;
    }

    public function update(
        ?string $name = null,
        ?string $email = null,
        ?string $avatar = null,
    ): void {
        if ($name !== null) {
            $this->name = $name;
        }

        if ($email !== null) {
            $this->email = Email::fromString($email);
        }

        if ($avatar !== null) {
            $this->avatar = $avatar;
        }

        $this->updatedAt = self::now();
    }

    public function changePassword(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->updatedAt = self::now();
    }

    /**
     * Record user login
     */
    public function recordLogin(string $ipAddress): void
    {
        $this->lastLoginAt = self::now();
        $this->lastLoginIp = $ipAddress;
        $this->updatedAt = self::now();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(RoleVO $role): bool
    {
        return $this->role->equals($role);
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    /**
     * Check if user has editor or higher role
     */
    public function isEditorOrHigher(): bool
    {
        return $this->role->getLevel() >= RoleVO::EDITOR()->getLevel();
    }

    /**
     * Assign a role to this user
     * Prevents duplicate role assignments
     */
    public function assignRole(Role $role): void
    {
        // Check if role is already assigned
        foreach ($this->assignedRoles as $assignedRole) {
            if ($assignedRole->id() === $role->id()) {
                return; // Role already assigned, prevent duplicate
            }
        }

        $this->assignedRoles[] = $role;
        $this->clearPermissionCache();
    }

    /**
     * Change user's primary role
     */
    public function changeRole(RoleVO $role): void
    {
        $this->role = $role;
    }

    /**
     * Check if user has a specific permission
     * Admin users automatically have all permissions
     * Uses internal caching to avoid repeated checks
     */
    public function hasPermission(PermissionName $permission): bool
    {
        $permissionName = $permission->toString();
        
        // Check cache first
        if (isset($this->permissionCache[$permissionName])) {
            return $this->permissionCache[$permissionName];
        }
        
        // Admin has all permissions
        if ($this->isAdmin()) {
            $this->permissionCache[$permissionName] = true;
            return true;
        }

        // Check direct permissions
        foreach ($this->permissions as $userPermission) {
            if ($userPermission->name()->equals($permission)) {
                $this->permissionCache[$permissionName] = true;
                return true;
            }
        }

        // Check permissions from assigned roles
        foreach ($this->assignedRoles as $role) {
            foreach ($role->getPermissions() as $rolePermission) {
                if ($rolePermission->name()->equals($permission)) {
                    $this->permissionCache[$permissionName] = true;
                    return true;
                }
            }
        }

        $this->permissionCache[$permissionName] = false;
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($permission instanceof PermissionName) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            } elseif (is_string($permission)) {
                if ($this->hasPermission(PermissionName::fromString($permission))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($permission instanceof PermissionName) {
                if (!$this->hasPermission($permission)) {
                    return false;
                }
            } elseif (is_string($permission)) {
                if (!$this->hasPermission(PermissionName::fromString($permission))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Set user's permissions (for hydration from repository)
     *
     * @param Permission[] $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
        $this->clearPermissionCache();
    }

    /**
     * Get user's permissions
     *
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set user's assigned roles (for hydration from repository)
     *
     * @param Role[] $roles
     */
    public function setAssignedRoles(array $roles): void
    {
        $this->assignedRoles = $roles;
        $this->clearPermissionCache();
    }

    /**
     * Get user's assigned roles
     *
     * @return Role[]
     */
    public function getAssignedRoles(): array
    {
        return $this->assignedRoles;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Deactivate user
     */
    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = self::now();
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = self::now();
    }

    // Getters
    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function emailString(): string
    {
        return $this->email->value();
    }

    public function password(): Password
    {
        return $this->password;
    }

    public function role(): RoleVO
    {
        return $this->role;
    }

    public function roleString(): string
    {
        return $this->role->name();
    }

    public function avatar(): ?string
    {
        return $this->avatar;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }

    public function lastLoginAt(): ?string
    {
        return $this->lastLoginAt;
    }

    public function lastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email->value(),
            'password' => $this->password->hash(),
            'role' => $this->role->name(),
            'avatar' => $this->avatar,
            'is_active' => $this->isActive,
            'last_login_at' => $this->lastLoginAt,
            'last_login_ip' => $this->lastLoginIp,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(16));
    }

    private static function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Clear permission cache (called when roles or permissions change)
     */
    private function clearPermissionCache(): void
    {
        $this->permissionCache = [];
    }
}
