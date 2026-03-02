<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Update Article Action
 */
final class UpdateArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {
    }

    public function __invoke(Request $request, ?string $id): Response
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->getRequest();

            $this->articleManager->update(
                articleId: $id,
                title: isset($data['title']) ? (string) $data['title'] : null,
                content: isset($data['content']) ? (string) $data['content'] : null,
                excerpt: isset($data['excerpt']) ? (string) $data['excerpt'] : null,
            );

            return $this->redirect('/admin/articles');
        } catch (\Throwable $e) {
            return new Response('Error: ' . $e->getMessage(), 500);
        }
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        /** @var string|null $id */
        $id = $request->getAttribute('id');

        if ($id === null) {
            return new Response('Article ID required', 400);
        }

        if ($request->getMethod() === 'POST') {
            return $this($request, $id);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ArticleManager::class),
        );
    }
}
