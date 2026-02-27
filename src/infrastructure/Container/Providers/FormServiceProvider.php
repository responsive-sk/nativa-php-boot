<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\Services\FormManager;
use Domain\Repository\FormRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\FormRepository;
use Infrastructure\Persistence\UnitOfWork;

/**
 * Form Service Provider
 */
class FormServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register FormRepository
        $container->bind(
            FormRepositoryInterface::class,
            function (Container $container) {
                return new FormRepository($container->get(UnitOfWork::class));
            }
        );

        // Register FormManager
        $container->singleton(
            FormManager::class,
            function (Container $container) {
                return new FormManager(
                    $container->get(FormRepositoryInterface::class)
                );
            }
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
