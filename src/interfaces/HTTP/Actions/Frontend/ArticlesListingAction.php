<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Articles Listing Action
 * 
 * Displays the articles listing page
 */
final class ArticlesListingAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'frontend/articles',
            [
                'pageTitle' => 'All Articles',
                'page' => 'articles',
                'metaDescription' => 'Browse all articles and tutorials',
            ],
            'frontend/layouts/frontend'
        );

        return $this->html($content);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
