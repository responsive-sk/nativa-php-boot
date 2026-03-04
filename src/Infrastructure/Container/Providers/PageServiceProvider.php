<?php

declare(strict_types = 1);

namespace Infrastructure\Container\Providers;

use Application\Services\PageManager;
use Domain\Repository\PageRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\PageRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Page Service Provider.
 */
final class PageServiceProvider implements ServiceProviderInterface
{
    #[\Override]
    public function register(Container $container): void
    {
        // Register PageRepository
        $container->bind(
            PageRepositoryInterface::class,
            static function (Container $container) {
                return new PageRepository($container->get(UnitOfWork::class));
            }
        );

        // Register PageManager
        $container->singleton(
            PageManager::class,
            static function (Container $container) {
                return new PageManager(
                    $container->get(PageRepositoryInterface::class)
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
