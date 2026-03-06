<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Edit Article Action.
 */
final class EditArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function show(Request $request, string $id): Response
    {
        $article = $this->articleManager->findById($id);

        if (!$article) {
            return new Response('Article not found', 404);
        }

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/articles/edit',
            [
                'title'   => 'Edit Article',
                'article' => $article,
                'error'   => null,
            ],
            'admin'
        );
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        /** @var string|null $id */
        $id = $request->getAttribute('id');

        if (null === $id) {
            return new Response('Article ID required', 400);
        }

        if ('GET' === $request->getMethod()) {
            return $this->show($request, $id);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(ArticleManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
