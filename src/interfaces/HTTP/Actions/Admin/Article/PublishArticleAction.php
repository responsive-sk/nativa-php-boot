<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Publish Article Action
 */
final class PublishArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    public function __invoke(Request $request, string $id): Response
    {
        try {
            $this->articleManager->publish($id);
            return $this->redirect('/admin/articles');
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        
        if ($request->getMethod() === 'POST') {
            return $this($request, $id);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
        );
    }
}
