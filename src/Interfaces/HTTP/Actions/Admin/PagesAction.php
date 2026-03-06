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
 * Admin Pages List Action.
 */
final class PagesAction extends Action
{
    public function __construct(
        private readonly PageManager $pageManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $pages = $this->pageManager->findAll(50, 0);

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/pages/pages/index',
            [
                'title' => 'Pages',
                'pages' => $pages,
            ],
            'admin'
        );
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
