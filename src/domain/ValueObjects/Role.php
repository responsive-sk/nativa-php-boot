<?php

declare(strict_types=1);

namespace Domain\ValueObjects;

/**
 * Role Enum
 * 
 * Represents user roles in the RBAC system.
 */
enum Role: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case VIEWER = 'viewer';
    case USER = 'user';

    /**
     * Get role name
     */
    public function name(): string
    {
        return $this->value;
    }

    /**
     * Check if this is admin role
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if this is editor role
     */
    public function isEditor(): bool
    {
        return $this === self::EDITOR;
    }

    /**
     * Check if this is viewer role
     */
    public function isViewer(): bool
    {
        return $this === self::VIEWER;
    }

    /**
     * Check if this is user role
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Get role hierarchy level (higher = more permissions)
     */
    public function getLevel(): int
    {
        return match($this) {
            self::ADMIN => 100,
            self::EDITOR => 50,
            self::VIEWER => 20,
            self::USER => 10,
        };
    }

    /**
     * Check if this role has higher or equal level than another
     */
    public function hasHigherOrEqualLevelThan(self $other): bool
    {
        return $this->getLevel() >= $other->getLevel();
    }

    /**
     * Create role from string
     */
    public static function fromString(string $name): self
    {
        return self::from(strtolower(trim($name)));
    }

    /**
     * Create admin role
     */
    public static function admin(): self
    {
        return self::ADMIN;
    }

    /**
     * Create editor role
     */
    public static function editor(): self
    {
        return self::EDITOR;
    }

    /**
     * Create viewer role
     */
    public static function viewer(): self
    {
        return self::VIEWER;
    }

    /**
     * Create user role
     */
    public static function user(): self
    {
        return self::USER;
    }
}
