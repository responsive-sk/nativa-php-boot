<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * List Articles Action.
 */
final class ListArticlesAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $articles = $this->articleManager->listPublished(20);

        $content = $this->renderer->render(
            'frontend/articles/index',
            ['articles' => $articles, 'title' => 'Articles'],
            'frontend/layouts/frontend'
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
