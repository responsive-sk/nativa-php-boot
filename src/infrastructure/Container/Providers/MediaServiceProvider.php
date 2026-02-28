<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\Services\MediaManager;
use Domain\Repository\MediaRepositoryInterface;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;
use Infrastructure\Persistence\Repositories\MediaRepository;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Storage\Providers\LocalStorageProvider;
use Infrastructure\Storage\Providers\MediaProviderInterface;

/**
 * Media Service Provider
 */
class MediaServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register Storage Provider (configurable via env)
        $container->singleton(
            MediaProviderInterface::class,
            function (Container $container): MediaProviderInterface {
                $provider = $_ENV['MEDIA_PROVIDER'] ?? 'local';

                return match ($provider) {
                    'cloudinary' => new \Infrastructure\Storage\Providers\CloudinaryProvider(
                        $_ENV['CLOUDINARY_CLOUD_NAME'] ?? null,
                        $_ENV['CLOUDINARY_API_KEY'] ?? null,
                        $_ENV['CLOUDINARY_API_SECRET'] ?? null,
                        $_ENV['CLOUDINARY_UPLOAD_PRESET'] ?? null,
                    ),
                    default => new LocalStorageProvider(
                        $_ENV['STORAGE_PATH'] ?? null,
                        $_ENV['STORAGE_URL'] ?? null,
                    ),
                };
            }
        );

        // Register MediaRepository
        $container->bind(
            MediaRepositoryInterface::class,
            function (Container $container): MediaRepositoryInterface {
                return new MediaRepository($container->get(UnitOfWork::class));
            }
        );

        // Register MediaManager
        $container->singleton(
            MediaManager::class,
            function (Container $container): MediaManager {
                return new MediaManager(
                    $container->get(MediaProviderInterface::class),
                    $container->get(MediaRepositoryInterface::class),
                );
            }
        );
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
