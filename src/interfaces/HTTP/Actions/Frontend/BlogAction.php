<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blog Listing Action - Displays blog homepage with article listing
 */
final class BlogAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        error_log("DEBUG: BlogAction handling request - page: {$page}, limit: {$limit}, offset: {$offset}");

        try {
            // Get published articles with pagination
            $articles = $this->articleManager->listPublished($limit, $offset);
            $total = $this->articleManager->countPublished();
            $totalPages = (int) ceil($total / $limit);
            
            error_log("DEBUG: BlogAction found {$total} articles, showing page {$page} of {$totalPages}");

            $content = $this->renderer->render(
                'frontend/blog',
                [
                    'articles' => $articles,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'pageTitle' => 'Blog',
                    'page' => 'blog',
                    'metaDescription' => 'Latest articles and insights from Nativa CMS',
                ],
                'frontend/layouts/frontend'
            );

            error_log("INFO: BlogAction blog listing rendered successfully");

            return $this->html($content);
        } catch (\Throwable $e) {
            error_log("ERROR: BlogAction error: " . $e->getMessage());
            return $this->error(500);
        }
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
