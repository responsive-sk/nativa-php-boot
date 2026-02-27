<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Frontend;

use Application\Services\ArticleManager;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * Homepage Controller
 */
class HomeController
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function index(): Response
    {
        $articles = $this->articleManager->listLatest(10);

        $content = $this->renderer->render(
            'pages/home',
            ['articles' => $articles, 'title' => 'Welcome'],
            'layouts/base'
        );

        return new Response($content);
    }
}
