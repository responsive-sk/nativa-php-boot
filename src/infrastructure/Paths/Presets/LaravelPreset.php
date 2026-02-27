<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Presets;

/**
 * Laravel Framework Directory Preset
 * 
 * Provides Laravel-specific directory structure and paths.
 */
class LaravelPreset extends AbstractPreset
{
    public function getName(): string
    {
        return 'Laravel';
    }

    public function getDescription(): string
    {
        return 'Laravel Framework directory structure with app, resources, storage, and database directories';
    }

    /**
     * Get Laravel directory structure
     * 
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return array_merge($this->getCommonPaths(), [
            // Laravel core directories
            'app' => $this->buildPath('app'),
            'bootstrap' => $this->buildPath('bootstrap'),
            'config' => $this->buildPath('config'),
            'database' => $this->buildPath('database'),
            'resources' => $this->buildPath('resources'),
            'routes' => $this->buildPath('routes'),
            'storage' => $this->buildPath('storage'),

            // App subdirectories
            'controllers' => $this->buildPath('app/Http/Controllers'),
            'middleware' => $this->buildPath('app/Http/Middleware'),
            'models' => $this->buildPath('app/Models'),
            'providers' => $this->buildPath('app/Providers'),
            'console' => $this->buildPath('app/Console'),
            'exceptions' => $this->buildPath('app/Exceptions'),
            'jobs' => $this->buildPath('app/Jobs'),
            'listeners' => $this->buildPath('app/Listeners'),
            'mail' => $this->buildPath('app/Mail'),
            'notifications' => $this->buildPath('app/Notifications'),
            'policies' => $this->buildPath('app/Policies'),
            'rules' => $this->buildPath('app/Rules'),

            // Resources subdirectories
            'views' => $this->buildPath('resources/views'),
            'lang' => $this->buildPath('resources/lang'),
            'css' => $this->buildPath('resources/css'),
            'js' => $this->buildPath('resources/js'),
            'sass' => $this->buildPath('resources/sass'),

            // Storage subdirectories
            'logs' => $this->buildPath('storage/logs'),
            'cache' => $this->buildPath('storage/framework/cache'),
            'sessions' => $this->buildPath('storage/framework/sessions'),
            'uploads' => $this->buildPath('storage/app/public'),
            'private_storage' => $this->buildPath('storage/app'),

            // Database directories
            'migrations' => $this->buildPath('database/migrations'),
            'seeders' => $this->buildPath('database/seeders'),
            'factories' => $this->buildPath('database/factories'),

            // Public assets
            'assets' => $this->buildPath('public/assets'),
            'images' => $this->buildPath('public/images'),
            'build' => $this->buildPath('public/build'),

            // Bootstrap
            'bootstrap_cache' => $this->buildPath('bootstrap/cache'),
        ]);
    }

    /**
     * Get Laravel-specific helper methods
     * 
     * @return array<string, string>
     */
    public function getHelperMethods(): array
    {
        return array_merge(parent::getHelperMethods(), [
            'app' => 'Get app directory path',
            'controllers' => 'Get controllers directory path',
            'models' => 'Get models directory path',
            'views' => 'Get views directory path',
            'migrations' => 'Get migrations directory path',
            'seeders' => 'Get seeders directory path',
            'middleware' => 'Get middleware directory path',
            'providers' => 'Get service providers directory path',
            'resources' => 'Get resources directory path',
            'uploads' => 'Get public uploads directory path',
            'private_storage' => 'Get private storage directory path',
        ]);
    }
}
