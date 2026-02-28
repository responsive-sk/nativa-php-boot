<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Domain\Repository\RoleRepositoryInterface;
use Domain\Repository\PermissionRepositoryInterface;
use Infrastructure\Persistence\Repositories\RoleRepository;
use Infrastructure\Persistence\Repositories\PermissionRepository;

/**
 * RBAC Service Provider
 */
class RbacServiceProvider implements ServiceProviderInterface
{
    /**
     * Register bindings
     */
    #[\Override]
    public function register(Container $container): void
    {
        // Repositories
        $container->singleton(RoleRepositoryInterface::class, RoleRepository::class);
        $container->singleton(PermissionRepositoryInterface::class, PermissionRepository::class);
    }

    /**
     * Boot services
     */
    #[\Override]
    public function boot(Container $container): void
    {
        // Services are auto-wired
    }
}
