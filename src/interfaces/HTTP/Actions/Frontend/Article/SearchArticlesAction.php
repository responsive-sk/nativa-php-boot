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
 * Search Articles Action
 */
final class SearchArticlesAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return $this->error('Please enter a search term (minimum 2 characters)', 400);
        }

        $articles = $this->articleManager->search($query);

        $content = $this->renderer->render(
            'pages/articles/index',
            [
                'articles' => $articles,
                'title' => 'Search results for: ' . htmlspecialchars($query),
                'searchQuery' => $query,
            ],
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
