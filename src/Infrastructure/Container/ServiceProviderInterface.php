<?php

declare(strict_types=1);

namespace Infrastructure\Container;

/**
 * Service Provider Interface
 */
interface ServiceProviderInterface
{
    /**
     * Register services in the container
     */
    public function register(Container $container): void;

    /**
     * Bootstrap services after all providers are registered
     */
    public function boot(Container $container): void;
}
