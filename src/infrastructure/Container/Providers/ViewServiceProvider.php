<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Paths\AppPaths;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * View Service Provider
 */
class ViewServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $paths = AppPaths::instance();

        $container->singleton(
            TemplateRenderer::class,
            function () use ($paths) {
                // Cache version from env or file-based auto-versioning
                $cacheVersion = $_ENV['TEMPLATE_CACHE_VERSION'] ?? null;

                return new TemplateRenderer(
                    $paths->templates('frontend'),
                    $paths->cache('templates'),
                    ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
                    $cacheVersion
                );
            }
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
