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
 * Blog Listing Action - Displays blog homepage with article listing.
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
        $page = (int) $request->getQueryParam('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            // Get published articles with pagination
            $articles = $this->articleManager->listPublished($limit, $offset);
            $total = $this->articleManager->countPublished();
            $totalPages = (int) ceil($total / $limit);

            $content = $this->renderer->render(
                'pages/frontend/blog',
                [
                    'articles'        => $articles,
                    'currentPage'     => $page,
                    'totalPages'      => $totalPages,
                    'pageTitle'       => 'Blog - Nativa CMS',
                    'page'            => 'blog',
                    'metaDescription' => 'Latest articles and insights from Nativa CMS',
                ],
                'layouts/frontend'
            );

            return $this->html($content);
        } catch (\Throwable $e) {
            error_log('ERROR: BlogAction error: ' . $e->getMessage());

            return $this->error('Internal Server Error', 500);
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
