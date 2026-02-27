<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use Infrastructure\Container\Providers\ArticleServiceProvider;
use Infrastructure\Container\Providers\CQRSServiceProvider;
use Infrastructure\Container\Providers\UserServiceProvider;
use Infrastructure\Container\Providers\ViewServiceProvider;

/**
 * Container Factory - Bootstrap the DI Container
 */
class ContainerFactory
{
    /**
     * Create and configure the container
     */
    public static function create(): Container
    {
        $container = new Container();

        // Register service providers
        $providers = [
            new ArticleServiceProvider(),
            new UserServiceProvider(),
            new ViewServiceProvider(),
            new CQRSServiceProvider(),
        ];

        // Register all providers
        foreach ($providers as $provider) {
            $provider->register($container);
        }

        // Boot all providers
        foreach ($providers as $provider) {
            $provider->boot($container);
        }

        return $container;
    }
}
