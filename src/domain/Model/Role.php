<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\ValueObjects\Role as RoleVO;

/**
 * Role Entity
 *
 * Represents a role in the RBAC system
 */
class Role
{
    private string $id;
    private RoleVO $name;
    private ?string $description;
    private string $createdAt;

    private function __construct()
    {
    }

    /**
     * Create a new role
     */
    public static function create(
        RoleVO $name,
        ?string $description = null,
    ): self {
        $role = new self();
        $role->id = self::generateId();
        $role->name = $name;
        $role->description = $description;
        $role->createdAt = self::now();

        return $role;
    }

    /**
     * Hydrate from database array
     */
    public static function fromArray(array $data): self
    {
        $role = new self();
        $role->id = $data['id'];
        $role->name = RoleVO::fromString($data['name']);
        $role->description = $data['description'] ?? null;
        $role->createdAt = $data['created_at'];

        return $role;
    }

    /**
     * Update role description
     */
    public function updateDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get role ID
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get role name value object
     */
    public function name(): RoleVO
    {
        return $this->name;
    }

    /**
     * Get role name string
     */
    public function nameString(): string
    {
        return $this->name->name();
    }

    /**
     * Get description
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * Get created at
     */
    public function createdAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Check if this is admin role
     */
    public function isAdmin(): bool
    {
        return $this->name->isAdmin();
    }

    /**
     * Check if this is editor role
     */
    public function isEditor(): bool
    {
        return $this->name->isEditor();
    }

    /**
     * Get role hierarchy level
     */
    public function getLevel(): int
    {
        return $this->name->getLevel();
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->name(),
            'description' => $this->description,
            'created_at' => $this->createdAt,
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
}
