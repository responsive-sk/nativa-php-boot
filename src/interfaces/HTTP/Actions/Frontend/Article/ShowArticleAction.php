<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Show Article Detail Action
 */
class ShowArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        $article = $this->articleManager->findBySlug($slug);

        if ($article === null || $article->isDraft()) {
            return $this->notFound('Article not found');
        }

        $content = $this->renderer->render(
            'pages/articles/show',
            ['article' => $article, 'title' => $article->title()],
            'layouts/base'
        );

        return $this->html($content);
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
