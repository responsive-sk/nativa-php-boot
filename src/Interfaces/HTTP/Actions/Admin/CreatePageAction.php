<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Admin Create Page Action.
 */
final class CreatePageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            return $this->store($request);
        }

        return $this->createForm($request);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(PageManager::class),
            $container->get(TemplateRenderer::class),
        );
    }

    private function createForm(Request $request): Response
    {
        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/pages/pages/create',
            ['title' => 'Create Page'],
            'admin'
        );
    }

    private function store(Request $request): Response
    {
        try {
            $title = (string) $request->getRequestParam('title', '');
            $content = (string) $request->getRequestParam('content', '');
            $template = (string) $request->getRequestParam('template', 'default');
            $metaTitle = (string) $request->getRequestParam('metaTitle', '');
            $metaDescription = (string) $request->getRequestParam('metaDescription', '');
            $isPublished = (bool) $request->getRequestParam('isPublished', false);

            if (empty($title) || empty($content)) {
                return $this->json(['error' => 'Title and content are required'], 400);
            }

            $page = $this->pageManager->create(
                title: $title,
                content: $content,
                template: $template,
                metaTitle: $metaTitle ?: null,
                metaDescription: $metaDescription ?: null,
                isPublished: $isPublished,
            );

            return $this->json([
                'success' => true,
                'page'    => [
                    'id'    => $page->id(),
                    'title' => $page->title(),
                    'slug'  => $page->slug(),
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
