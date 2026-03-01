<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Slim 4 Framework Directory Preset
 * 
 * Provides Slim 4-specific directory structure and paths.
 */
class Slim4Preset extends AbstractPreset
{
    public function getName(): string
    {
        return 'Slim 4';
    }

    public function getDescription(): string
    {
        return 'Slim 4 Framework directory structure with src, templates, and var directories';
    }

    /**
     * Get Slim 4 directory structure
     * 
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return array_merge($this->getCommonPaths(), [
            // Slim 4 core directories
            'src' => $this->buildPath('src'),
            'config' => $this->buildPath('config'),
            'templates' => $this->buildPath('templates'),
            'var' => $this->buildPath('var'),
            'bin' => $this->buildPath('bin'),

            // Source subdirectories
            'actions' => $this->buildPath('src/Action'),
            'handlers' => $this->buildPath('src/Handler'),
            'middleware' => $this->buildPath('src/Middleware'),
            'services' => $this->buildPath('src/Service'),
            'repositories' => $this->buildPath('src/Repository'),
            'entities' => $this->buildPath('src/Entity'),
            'factories' => $this->buildPath('src/Factory'),
            'exceptions' => $this->buildPath('src/Exception'),

            // Template subdirectories
            'views' => $this->buildPath('templates'),
            'layouts' => $this->buildPath('templates/layout'),
            'partials' => $this->buildPath('templates/partial'),

            // Runtime directories
            'cache' => $this->buildPath('var/cache'),
            'logs' => $this->buildPath('var/log'),
            'storage' => $this->buildPath('var/storage'),
            'uploads' => $this->buildPath('var/uploads'),
            'tmp' => $this->buildPath('var/tmp'),

            // Public assets
            'assets' => $this->buildPath('public/assets'),
            'css' => $this->buildPath('public/assets/css'),
            'js' => $this->buildPath('public/assets/js'),
            'images' => $this->buildPath('public/assets/images'),
            'fonts' => $this->buildPath('public/assets/fonts'),

            // Configuration subdirectories
            'routes' => $this->buildPath('config/routes'),
            'settings' => $this->buildPath('config/settings'),
            'dependencies' => $this->buildPath('config/dependencies'),
        ]);
    }

    /**
     * Get Slim 4-specific helper methods
     * 
     * @return array<string, string>
     */
    public function getHelperMethods(): array
    {
        return array_merge(parent::getHelperMethods(), [
            'src' => 'Get source directory path',
            'actions' => 'Get actions directory path',
            'handlers' => 'Get handlers directory path',
            'middleware' => 'Get middleware directory path',
            'services' => 'Get services directory path',
            'repositories' => 'Get repositories directory path',
            'entities' => 'Get entities directory path',
            'templates' => 'Get templates directory path',
            'layouts' => 'Get layouts directory path',
            'partials' => 'Get partials directory path',
            'var' => 'Get var directory path',
            'uploads' => 'Get uploads directory path',
        ]);
    }
}
