<?php

declare(strict_types = 1);

namespace Infrastructure\Container\Providers;

use Application\Services\ContactManager;
use Domain\Events\EventDispatcherInterface;
use Domain\Repository\ContactRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\ContactRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Contact Service Provider.
 */
final class ContactServiceProvider implements ServiceProviderInterface
{
    #[\Override]
    public function register(Container $container): void
    {
        // Register ContactRepository
        $container->bind(
            ContactRepositoryInterface::class,
            static function (Container $container) {
                return new ContactRepository($container->get(UnitOfWork::class));
            }
        );

        // Register ContactManager
        $container->singleton(
            ContactManager::class,
            static function (Container $container) {
                return new ContactManager(
                    $container->get(ContactRepositoryInterface::class),
                    $container->get(EventDispatcherInterface::class)
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
