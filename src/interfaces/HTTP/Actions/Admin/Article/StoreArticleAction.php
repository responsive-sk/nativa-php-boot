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
 * Store Article Action
 */
final class StoreArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $data = $request->request->all();

            $article = $this->articleManager->create(
                title: $data['title'],
                content: $data['content'],
                authorId: '1', // TODO: Get from session (current user ID)
                excerpt: $data['excerpt'] ?? null,
            );

            if ($data['status'] === 'published') {
                $this->articleManager->publish($article->id());
            }

            return $this->redirect('/admin/articles');
        } catch (\Throwable $e) {
            $content = $this->renderer->render(
                'admin/articles/create',
                [
                    'title' => 'Create Article',
                    'error' => $e->getMessage(),
                    'old' => $data,
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 500);
        }
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this($request);
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
