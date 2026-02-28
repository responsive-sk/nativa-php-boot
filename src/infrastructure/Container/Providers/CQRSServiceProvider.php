<?php

declare(strict_types=1);

namespace Infrastructure\Container\Providers;

use Application\CQRS\Article\Commands\CreateArticle;
use Application\CQRS\Article\Commands\PublishArticle;
use Application\CQRS\Article\Handlers\CreateArticleHandler;
use Application\CQRS\Article\Handlers\GetArticleBySlugHandler;
use Application\CQRS\Article\Handlers\ListArticlesHandler;
use Application\CQRS\Article\Handlers\PublishArticleHandler;
use Application\CQRS\Article\Queries\GetArticleBySlug;
use Application\CQRS\Article\Queries\ListArticles;
use Application\CQRS\CommandBus;
use Application\CQRS\QueryBus;
use Infrastructure\Container\Container;
use Infrastructure\Container\ServiceProviderInterface;

/**
 * CQRS Service Provider
 */
class CQRSServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Register CommandBus as singleton
        $container->singleton(CommandBus::class, function (Container $container): CommandBus {
            $commandBus = new CommandBus();

            // Register Article command handlers
            $commandBus->register(
                CreateArticle::class,
                new CreateArticleHandler($container->get(\Application\Services\ArticleManager::class))
            );
            $commandBus->register(
                PublishArticle::class,
                new PublishArticleHandler($container->get(\Application\Services\ArticleManager::class))
            );

            return $commandBus;
        });

        // Register QueryBus as singleton
        $container->singleton(QueryBus::class, function (Container $container): QueryBus {
            $queryBus = new QueryBus();

            // Register Article query handlers
            $queryBus->register(
                ListArticles::class,
                new ListArticlesHandler($container->get(\Application\Services\ArticleManager::class))
            );
            $queryBus->register(
                GetArticleBySlug::class,
                new GetArticleBySlugHandler($container->get(\Application\Services\ArticleManager::class))
            );

            return $queryBus;
        });
    }

    public function boot(Container $container): void
    {
        // Bootstrapping logic if needed
    }
}
