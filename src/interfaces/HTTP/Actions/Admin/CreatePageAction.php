<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Create Page Action
 */
class CreatePageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->store($request);
        }
        return $this->createForm($request);
    }

    private function createForm(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/pages/pages/create',
            ['title' => 'Create Page'],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function store(Request $request): Response
    {
        try {
            $title = (string) $request->request->get('title', '');
            $content = (string) $request->request->get('content', '');
            $template = (string) $request->request->get('template', 'default');
            $metaTitle = (string) $request->request->get('metaTitle', '');
            $metaDescription = (string) $request->request->get('metaDescription', '');
            $isPublished = $request->request->getBoolean('isPublished', false);

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
                'page' => [
                    'id' => $page->id(),
                    'title' => $page->title(),
                    'slug' => $page->slug(),
                ],
            ]);

        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(PageManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
