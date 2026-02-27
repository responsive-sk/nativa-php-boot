<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\Services\ContactManager;
use Domain\Repository\ContactRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\ContactRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Contact Service Provider
 */
class ContactServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register ContactRepository
        $container->bind(
            ContactRepositoryInterface::class,
            function (Container $container) {
                return new ContactRepository($container->get(UnitOfWork::class));
            }
        );

        // Register ContactManager
        $container->singleton(
            ContactManager::class,
            function (Container $container) {
                return new ContactManager(
                    $container->get(ContactRepositoryInterface::class),
                    $container->get(\Domain\Events\EventDispatcherInterface::class)
                );
            }
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
