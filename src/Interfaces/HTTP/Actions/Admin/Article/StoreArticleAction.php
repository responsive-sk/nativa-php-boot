<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Application\Services\ArticleManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Store Article Action.
 */
final class StoreArticleAction extends Action
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function __invoke(Request $request): Response
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->getRequest();

            $title = isset($data['title']) && \is_string($data['title']) ? $data['title'] : '';
            $content = isset($data['content']) && \is_string($data['content']) ? $data['content'] : '';
            $excerpt = isset($data['excerpt']) && \is_string($data['excerpt']) ? $data['excerpt'] : null;

            $article = $this->articleManager->create(
                title: $title,
                content: $content,
                authorId: '1', // TODO: Get from session (current user ID)
                excerpt: $excerpt,
            );

            if (isset($data['status']) && 'published' === $data['status']) {
                $this->articleManager->publish($article->id());
            }

            return $this->redirect('/admin/articles');
        } catch (\Throwable $e) {
            $content = $this->renderer->render(
                'admin/articles/create',
                [
                    'title' => 'Create Article',
                    'error' => $e->getMessage(),
                    'old'   => $data,
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 500);
        }
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            return $this($request);
        }

        return new Response('Method not allowed', 405);
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
