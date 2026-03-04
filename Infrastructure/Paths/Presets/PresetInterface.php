<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Interface for framework directory presets
 */
interface PresetInterface
{
    /**
     * Get framework name
     */
    public function getName(): string;

    /**
     * Get framework description
     */
    public function getDescription(): string;

    /**
     * Get directory structure for this framework
     * 
     * @return array<string, string> Array of path name => relative path
     */
    public function getPaths(): array;

    /**
     * Get framework-specific helper methods
     * 
     * @return array<string, string> Array of method name => description
     */
    public function getHelperMethods(): array;
}
