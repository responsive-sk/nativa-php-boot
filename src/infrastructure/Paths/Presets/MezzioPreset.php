<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Mezzio/Laminas Framework Directory Preset
 * 
 * Provides Mezzio-specific directory structure and paths.
 */
class MezzioPreset extends AbstractPreset
{
    public function getName(): string
    {
        return 'Mezzio/Laminas';
    }

    public function getDescription(): string
    {
        return 'Mezzio Framework directory structure with src, templates, data, and modules directories';
    }

    /**
     * Get Mezzio directory structure
     * 
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return array_merge($this->getCommonPaths(), [
            // Mezzio core directories
            'src' => $this->buildPath('src'),
            'config' => $this->buildPath('config'),
            'templates' => $this->buildPath('templates'),
            'data' => $this->buildPath('data'),
            'modules' => $this->buildPath('modules'),
            'bin' => $this->buildPath('bin'),

            // Source subdirectories
            'handlers' => $this->buildPath('src/Handler'),
            'middleware' => $this->buildPath('src/Middleware'),
            'services' => $this->buildPath('src/Service'),
            'factories' => $this->buildPath('src/Factory'),
            'entities' => $this->buildPath('src/Entity'),
            'repositories' => $this->buildPath('src/Repository'),

            // Template subdirectories
            'views' => $this->buildPath('templates'),
            'layouts' => $this->buildPath('templates/layout'),
            'app_templates' => $this->buildPath('templates/app'),
            'error_templates' => $this->buildPath('templates/error'),

            // Data directories
            'cache' => $this->buildPath('data/cache'),
            'logs' => $this->buildPath('data/logs'),
            'storage' => $this->buildPath('data/storage'),
            'uploads' => $this->buildPath('data/uploads'),
            'database' => $this->buildPath('data/database'),

            // Configuration subdirectories
            'autoload' => $this->buildPath('config/autoload'),
            'routes' => $this->buildPath('config/routes'),

            // Public assets
            'assets' => $this->buildPath('public/assets'),
            'css' => $this->buildPath('public/assets/css'),
            'js' => $this->buildPath('public/assets/js'),
            'images' => $this->buildPath('public/assets/images'),
            'fonts' => $this->buildPath('public/assets/fonts'),

            // Module directories (common modules)
            'app_module' => $this->buildPath('modules/App'),
            'user_module' => $this->buildPath('modules/User'),
            'admin_module' => $this->buildPath('modules/Admin'),

            // Content directories (for CMS)
            'content' => $this->buildPath('content'),
            'pages' => $this->buildPath('content/pages'),
            'posts' => $this->buildPath('content/posts'),
            'docs' => $this->buildPath('content/docs'),
        ]);
    }

    /**
     * Get Mezzio-specific helper methods
     * 
     * @return array<string, string>
     */
    public function getHelperMethods(): array
    {
        return array_merge(parent::getHelperMethods(), [
            'src' => 'Get source directory path',
            'handlers' => 'Get handlers directory path',
            'middleware' => 'Get middleware directory path',
            'services' => 'Get services directory path',
            'factories' => 'Get factories directory path',
            'entities' => 'Get entities directory path',
            'repositories' => 'Get repositories directory path',
            'templates' => 'Get templates directory path',
            'layouts' => 'Get layouts directory path',
            'data' => 'Get data directory path',
            'modules' => 'Get modules directory path',
            'autoload' => 'Get autoload config directory path',
            'routes' => 'Get routes config directory path',
            'content' => 'Get content directory path',
            'pages' => 'Get pages directory path',
            'posts' => 'Get posts directory path',
            'database' => 'Get database directory path',
        ]);
    }
}
