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
final class ViewServiceProvider implements ServiceProviderInterface
{
    #[\Override]
    public function register(Container $container): void
    {
        $paths = AppPaths::instance();

        $container->singleton(
            TemplateRenderer::class,
            function () use ($paths) {
                // Cache version from env or file-based auto-versioning
                $cacheVersion = $_ENV['TEMPLATE_CACHE_VERSION'] ?? null;

                return new TemplateRenderer(
                    $paths->getBasePath() . '/src/Templates',
                    $paths->cache('templates'),
                    ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
                    $cacheVersion
                );
            }
        );
    }

    #[\Override]
    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
