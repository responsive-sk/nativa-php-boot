<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Dashboard Action
 */
final class DashboardAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/pages/dashboard',
            ['title' => 'Dashboard'],
            'admin/layouts/base'
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
