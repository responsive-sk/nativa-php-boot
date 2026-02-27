<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Persistence\Repositories\UserRepository;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;

/**
 * User Service Provider
 */
class UserServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register UserRepository
        $container->bind(
            UserRepositoryInterface::class,
            function (Container $container) {
                return new UserRepository($container->get(UnitOfWork::class));
            }
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
