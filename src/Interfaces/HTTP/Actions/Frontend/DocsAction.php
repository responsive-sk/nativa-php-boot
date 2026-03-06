<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Documentation page action
 * Pure PHP - no Symfony dependencies in production.
 */
final class DocsAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'frontend/docs',
            [
                'pageTitle' => 'Documentation - Nativa CMS',
                'page'      => 'docs',
            ],
            'frontend'
        );

        return $this->html($content);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        /** @var TemplateRenderer $renderer */
        $renderer = $container->get(TemplateRenderer::class);

        return new self($renderer);
    }
}
