<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Homepage Action.
 */
final class HomeAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $articles = $this->articleManager->listLatest(10);

        $content = $this->renderer->render(
            'pages/home',
            [
                'articles'  => $articles,
                'pageTitle' => 'Nativa CMS - Modern PHP Blog Platform',
                'page'      => 'home',
            ],
            'layouts/frontend'
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
