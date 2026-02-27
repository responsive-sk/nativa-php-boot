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
 * List Articles Action
 */
class ListArticlesAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $articles = $this->articleManager->listPublished(20);

        $content = $this->renderer->render(
            'pages/articles/index',
            ['articles' => $articles, 'title' => 'Articles'],
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
