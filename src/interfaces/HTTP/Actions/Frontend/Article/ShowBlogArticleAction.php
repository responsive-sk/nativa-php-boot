<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Show Blog Article Detail Action - Uses new frontend templates
 */
final class ShowBlogArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        
        error_log("DEBUG: ShowBlogArticleAction looking for article slug: {$slug}");
        
        $article = $this->articleManager->findBySlug($slug);

        if ($article === null || $article->isDraft()) {
            error_log("WARN: ShowBlogArticleAction article not found or draft: {$slug}");
            return $this->notFound('Article not found');
        }

        error_log("DEBUG: ShowBlogArticleAction found article: " . $article->title());

        // Increment view count
        $this->articleManager->incrementViewCount($article->id());

        // Get related articles (same category or tags, excluding current)
        $relatedArticles = $this->articleManager->findRelated($article, 3);

        $content = $this->renderer->render(
            'frontend/blog/show',
            [
                'article' => $article,
                'relatedArticles' => $relatedArticles,
                'pageTitle' => $article->title(),
                'page' => 'blog',
                'metaDescription' => $article->excerpt() ?: substr(strip_tags($article->content()), 0, 160),
            ],
            'frontend/layouts/frontend'
        );

        error_log("INFO: ShowBlogArticleAction article detail rendered successfully: {$slug}");

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
