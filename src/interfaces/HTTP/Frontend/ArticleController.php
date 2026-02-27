<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Frontend;

use Application\Services\ArticleManager;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Persistence\UnitOfWork;
use Infrastructure\Persistence\Repositories\ArticleRepository;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Article Controller
 */
class ArticleController
{
    private ArticleManager $articleManager;
    private TemplateRenderer $renderer;

    public function __construct()
    {
        $basePath = dirname(__DIR__, 3);

        $db = new DatabaseConnection();
        $uow = new UnitOfWork($db);
        $articleRepo = new ArticleRepository($uow);
        $eventDispatcher = new \Application\Services\EventDispatcher();
        $this->articleManager = new ArticleManager($articleRepo, $eventDispatcher);

        $this->renderer = new TemplateRenderer(
            $basePath . '/interfaces/Templates/frontend',
            $basePath . '/storage/cache/templates',
            ($_ENV['APP_DEBUG'] ?? 'false') === 'true'
        );
    }

    public function index(): Response
    {
        $articles = $this->articleManager->listPublished(20);

        $content = $this->renderer->render(
            'pages/articles/index',
            ['articles' => $articles, 'title' => 'Articles'],
            'layouts/base'
        );

        return new Response($content);
    }

    public function show(string $slug): Response
    {
        $article = $this->articleManager->findBySlug($slug);

        if ($article === null || $article->isDraft()) {
            return new Response('Article not found', 404);
        }

        $content = $this->renderer->render(
            'pages/articles/show',
            ['article' => $article, 'title' => $article->title()],
            'layouts/base'
        );

        return new Response($content);
    }

    public function byTag(string $slug): Response
    {
        $articles = $this->articleManager->findByTag($slug);

        $content = $this->renderer->render(
            'pages/articles/index',
            [
                'articles' => $articles,
                'title' => 'Articles tagged: ' . htmlspecialchars($slug),
            ],
            'layouts/base'
        );

        return new Response($content);
    }

    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return new Response('Please enter a search term (minimum 2 characters)', 400);
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

        return new Response($content);
    }
}
