<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Admin Dashboard Action.
 */
final class DashboardAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/dashboard',
            ['title' => 'Dashboard'],
            'admin'
        );
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
