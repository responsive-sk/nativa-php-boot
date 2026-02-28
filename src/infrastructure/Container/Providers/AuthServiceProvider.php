<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\Services\AuthService;
use Application\Services\SessionManager;
use Application\Services\TokenManager;
use Domain\Events\EventDispatcherInterface;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\UserRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Auth Service Provider
 */
class AuthServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Session Manager (singleton)
        $container->singleton(SessionManager::class, function (): SessionManager {
            return new SessionManager();
        });

        // Token Manager
        $container->singleton(TokenManager::class, function () use ($container): TokenManager {
            return new TokenManager(
                $container->get(UserRepositoryInterface::class)
            );
        });

        // Auth Service
        $container->singleton(AuthService::class, function () use ($container): AuthService {
            return new AuthService(
                $container->get(UserRepositoryInterface::class),
                $container->get(SessionManager::class),
                $container->get(TokenManager::class),
                $container->get(EventDispatcherInterface::class)
            );
        });
    }

    public function boot(Container $container): void
    {
        // Bootstrapping
    }
}
