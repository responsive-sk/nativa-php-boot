<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Edit Article Action
 */
final class EditArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request, string $id): Response
    {
        $article = $this->articleManager->findById($id);

        if (!$article) {
            return new Response('Article not found', 404);
        }

        $content = $this->renderer->render(
            'admin/articles/edit',
            [
                'title' => 'Edit Article',
                'article' => $article,
                'error' => null,
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function handle(Request $request): Response
    {
        $id = $request->attributes->get('id');
        
        if ($request->getMethod() === 'GET') {
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
