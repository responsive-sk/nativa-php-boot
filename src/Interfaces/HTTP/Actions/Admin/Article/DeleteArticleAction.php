<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;

/**
 * Delete Article Action.
 */
final class DeleteArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {}

    public function __invoke(Request $request, string $id): Response
    {
        try {
            $this->articleManager->delete($id);

            return $this->redirect('/admin/articles');
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        /** @var string|null $id */
        $id = $request->getAttribute('id');

        if (null === $id) {
            return new Response('Article ID required', 400);
        }

        if ('DELETE' === $request->getMethod()) {
            return $this($request, $id);
        }

        // Support form POST with _method override
        if ('POST' === $request->getMethod() && 'DELETE' === $request->getRequestParam('_method')) {
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
