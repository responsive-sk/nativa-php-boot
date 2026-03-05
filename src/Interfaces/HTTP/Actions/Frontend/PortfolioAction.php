<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Portfolio Action - Displays portfolio page with projects.
 */
final class PortfolioAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        try {
            $content = $this->renderer->render(
                'pages/frontend/portfolio',
                [
                    'pageTitle' => 'Portfolio',
                    'page' => 'portfolio',
                ],
                'frontend'
            );

            return $this->html($content);
        } catch (\Throwable $e) {
            error_log('ERROR: PortfolioAction error: ' . $e->getMessage());

            return $this->error('Internal server error', 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
