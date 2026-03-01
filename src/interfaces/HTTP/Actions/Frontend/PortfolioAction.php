<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Portfolio Action - Displays portfolio page with projects
 */
final class PortfolioAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        error_log("DEBUG: PortfolioAction handling request");

        try {
            $content = $this->renderer->render(
                'frontend/portfolio',
                [
                    'pageTitle' => 'Portfolio',
                    'page' => 'portfolio',
                    'metaDescription' => 'Our latest projects and creative work',
                ],
                'frontend/layouts/frontend'
            );

            error_log("INFO: PortfolioAction portfolio page rendered successfully");

            return $this->html($content);
        } catch (\Throwable $e) {
            error_log("ERROR: PortfolioAction error: " . $e->getMessage());
            return $this->error(500);
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
