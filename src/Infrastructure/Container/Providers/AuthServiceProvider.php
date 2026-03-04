<?php

declare(strict_types = 1);

namespace Infrastructure\Container\Providers;

use Application\Services\AuthService;
use Application\Services\SessionManager;
use Application\Services\TokenManager;
use Domain\Events\EventDispatcherInterface;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;

/**
 * Auth Service Provider.
 */
final class AuthServiceProvider implements ServiceProviderInterface
{
    #[\Override]
    public function register(Container $container): void
    {
        // Session Manager (singleton)
        $container->singleton(SessionManager::class, static function (): SessionManager {
            return new SessionManager();
        });

        // Token Manager
        $container->singleton(TokenManager::class, static function () use ($container): TokenManager {
            return new TokenManager(
                $container->get(UserRepositoryInterface::class)
            );
        });

        // Auth Service
        $container->singleton(AuthService::class, static function () use ($container): AuthService {
            return new AuthService(
                $container->get(UserRepositoryInterface::class),
                $container->get(SessionManager::class),
                $container->get(TokenManager::class),
                $container->get(EventDispatcherInterface::class)
            );
        });
    }

    #[\Override]
    public function boot(Container $container): void
    {
        // Bootstrapping
    }
}
