<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\Services\EventDispatcher;
use Domain\Events\EventDispatcherInterface;
use Domain\Repository\ArticleRepositoryInterface;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\Repositories\ArticleRepository;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;

/**
 * Article Service Provider
 */
class ArticleServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register DatabaseConnection as singleton
        $container->singleton(DatabaseConnection::class, function () {
            return new DatabaseConnection();
        });

        // Register UnitOfWork as singleton
        $container->singleton(UnitOfWork::class, function (Container $container) {
            return new UnitOfWork($container->get(DatabaseConnection::class));
        });

        // Register ArticleRepository
        $container->bind(
            ArticleRepositoryInterface::class,
            function (Container $container) {
                return new ArticleRepository($container->get(UnitOfWork::class));
            }
        );

        // Register EventDispatcher as singleton
        $container->singleton(
            EventDispatcherInterface::class,
            EventDispatcher::class
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
