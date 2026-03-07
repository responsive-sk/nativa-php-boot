<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Cascade Demo Action
 */
final class CascadeDemoAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'vanilla/frontend/pages/cascade-demo',
            [
                'pageTitle' => 'Cascade Demo - Nativa CMS',
            ]
            // No layout - page includes header/footer directly
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
