<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * PermissionName Value Object
 *
 * Represents a permission identifier in the format: "resource.action"
 * Examples: "admin.dashboard", "articles.create", "users.manage"
 */
final readonly class PermissionName
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $resource
     * @param non-empty-string $action
     */
    private function __construct(
        private string $name,
        private string $resource,
        private string $action,
    ) {
    }

    /**
     * Create from permission name string
     */
    public static function fromString(string $name): self
    {
        self::validate($name);
        
        $parts = explode('.', $name);
        $resource = $parts[0];
        $action = implode('.', array_slice($parts, 1));
        
        return new self($name, $resource, $action);
    }

    /**
     * Create from resource and action parts
     */
    public static function create(string $resource, string $action): self
    {
        $name = sprintf('%s.%s', $resource, $action);
        self::validate($name);
        
        return new self($name, $resource, $action);
    }

    /**
     * Get full permission name
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get resource part (e.g., "admin", "articles")
     */
    public function resource(): string
    {
        return $this->resource;
    }

    /**
     * Get action part (e.g., "view", "create", "delete")
     */
    public function action(): string
    {
        return $this->action;
    }

    /**
     * Check if permission matches a pattern
     * Pattern can use * as wildcard
     * Examples:
     *   - "admin.*" matches all admin permissions
     *   - "*.view" matches all view permissions
     *   - "admin.dashboard" matches exactly
     */
    public function matches(string $pattern): bool
    {
        $pattern = str_replace('*', '[^\.]+', $pattern);
        $pattern = '#^' . $pattern . '$#';
        return (bool) preg_match($pattern, $this->name);
    }

    /**
     * Check if equals another permission
     */
    public function equals(self $other): bool
    {
        return $this->name === $other->name;
    }

    /**
     * Get string representation
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Validate permission name format
     */
    private static function validate(string $name): void
    {
        if (!preg_match('/^[a-z]+(\.[a-z_]+)+$/', $name)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid permission name "%s". Format must be "resource.action" (e.g., "admin.dashboard")', $name)
            );
        }
    }
}
