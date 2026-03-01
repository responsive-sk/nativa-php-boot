<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Infrastructure\Http\Request;
use Infrastructure\Http\JsonResponse;

/**
 * List Articles API Action
 *
 * Returns articles as JSON for frontend consumption
 */
final class ListArticlesApiAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    #[\Override]
    public function handle(Request $request): JsonResponse
    {
        $page = (int) $request->getQueryParam('page', 1);
        $limit = (int) $request->getQueryParam('limit', 10);
        $search = $request->getQueryParam('q', '');

        $offset = ($page - 1) * $limit;

        // Get articles
        if ($search) {
            $articles = $this->articleManager->search($search);
        } else {
            $articles = $this->articleManager->listPublished($limit, $offset);
        }

        // Get total count
        $total = $this->articleManager->countPublished();
        $totalPages = (int) ceil($total / $limit);

        // Convert to array for JSON response
        $articlesData = array_map(fn($article) => [
            'id' => $article->id(),
            'title' => $article->title(),
            'slug' => $article->slug(),
            'excerpt' => $article->excerpt(),
            'content' => $article->content(),
            'author_id' => $article->authorId(),
            'status' => $article->status()->value,
            'views' => $article->views(),
            'published_at' => $article->publishedAt(),
            'created_at' => $article->createdAt(),
            'updated_at' => $article->updatedAt(),
        ], $articles);

        return new JsonResponse([
            'articles' => $articlesData,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ]);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
        );
    }
}
