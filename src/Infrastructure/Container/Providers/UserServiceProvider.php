<?php

declare(strict_types = 1);

namespace Infrastructure\Container\Providers;

use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\UserRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * User Service Provider.
 */
final class UserServiceProvider implements ServiceProviderInterface
{
    #[\Override]
    public function register(Container $container): void
    {
        // Register UserRepository
        $container->bind(
            UserRepositoryInterface::class,
            static function (Container $container) {
                return new UserRepository($container->get(UnitOfWork::class));
            }
        );
    }

    #[\Override]
    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
