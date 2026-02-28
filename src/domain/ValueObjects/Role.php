<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Role Value Object
 *
 * Represents user roles in the RBAC system
 */
final class Role
{
    public const ADMIN = 'admin';
    public const EDITOR = 'editor';
    public const VIEWER = 'viewer';
    public const USER = 'user';

    private const VALID_ROLES = [
        self::ADMIN,
        self::EDITOR,
        self::VIEWER,
        self::USER,
    ];

    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create role from string
     */
    public static function fromString(string $name): self
    {
        $name = strtolower(trim($name));
        self::validate($name);
        return new self($name);
    }

    /**
     * Create admin role
     */
    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    /**
     * Create editor role
     */
    public static function editor(): self
    {
        return new self(self::EDITOR);
    }

    /**
     * Create viewer role
     */
    public static function viewer(): self
    {
        return new self(self::VIEWER);
    }

    /**
     * Create user role
     */
    public static function user(): self
    {
        return new self(self::USER);
    }

    /**
     * Get role name
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Check if this is admin role
     */
    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    /**
     * Check if this is editor role
     */
    public function isEditor(): bool
    {
        return $this->name === self::EDITOR;
    }

    /**
     * Check if role equals another role
     */
    public function equals(Role $other): bool
    {
        return $this->name === $other->name;
    }

    /**
     * Get role hierarchy level (higher = more permissions)
     */
    public function getLevel(): int
    {
        return match ($this->name) {
            self::ADMIN => 100,
            self::EDITOR => 50,
            self::VIEWER => 20,
            self::USER => 10,
            default => 0,
        };
    }

    /**
     * Check if this role has higher or equal level than another
     */
    public function hasHigherOrEqualLevelThan(Role $other): bool
    {
        return $this->getLevel() >= $other->getLevel();
    }

    /**
     * Validate role name
     */
    private static function validate(string $name): void
    {
        if (!in_array($name, self::VALID_ROLES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid role "%s". Valid roles are: %s', $name, implode(', ', self::VALID_ROLES))
            );
        }
    }
}
