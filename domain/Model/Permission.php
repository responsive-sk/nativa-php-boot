<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\ValueObjects\PermissionName;

/**
 * Permission Entity
 *
 * Represents a granular permission in the RBAC system
 */
final class Permission
{
    private string $id;
    private PermissionName $name;
    private ?string $description;
    private string $group;
    private string $createdAt;

    private function __construct()
    {
    }

    /**
     * Create a new permission
     */
    public static function create(
        PermissionName $name,
        ?string $description = null,
        string $group = 'default',
    ): self {
        $permission = new self();
        $permission->id = self::generateId();
        $permission->name = $name;
        $permission->description = $description;
        $permission->group = $group;
        $permission->createdAt = self::now();

        return $permission;
    }

    /**
     * Hydrate from database array
     */
    public static function fromArray(array $data): self
    {
        $permission = new self();
        $permission->id = $data['id'];
        $permission->name = PermissionName::fromString($data['name']);
        $permission->description = $data['description'] ?? null;
        $permission->group = $data['group_name'] ?? 'default';
        $permission->createdAt = $data['created_at'];

        return $permission;
    }

    /**
     * Update permission description
     */
    public function updateDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get permission ID
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get permission name value object
     */
    public function name(): PermissionName
    {
        return $this->name;
    }

    /**
     * Get permission name string
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
     * Get group name (e.g., "admin", "articles", "pages")
     */
    public function group(): string
    {
        return $this->group;
    }

    /**
     * Get created at
     */
    public function createdAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Check if permission matches a pattern
     */
    public function matches(string $pattern): bool
    {
        return $this->name->matches($pattern);
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
            'group_name' => $this->group,
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
