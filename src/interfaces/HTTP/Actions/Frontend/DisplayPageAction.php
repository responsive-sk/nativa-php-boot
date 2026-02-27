<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\PageManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend Display Page Action
 */
class DisplayPageAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        
        // Handle homepage case (if needed in future)
        if (empty($slug) || $slug === 'home') {
            // Could redirect to homepage or show a "home" page
            // For now, let HomeAction handle /
            return new Response('Page not found', 404);
        }
        
        $page = $this->pageManager->findBySlug($slug);

        if ($page === null || !$page->isPublished()) {
            return $this->notFound('Page not found');
        }

        // Get page with all relationships
        $pageData = $this->pageManager->getPageWithRelations($page->id());

        // Choose template based on page template setting
        $template = 'pages/' . ($page->template() !== 'default' ? $page->template() : 'default');

        $content = $this->renderer->render(
            $template,
            [
                'title' => $page->metaTitle() ?: $page->title(),
                'page' => $page,
                'blocks' => $pageData['blocks'] ?? [],
                'media' => $pageData['media'] ?? [],
                'forms' => $pageData['forms'] ?? [],
            ],
            'layouts/base'
        );

        return $this->html($content);
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
