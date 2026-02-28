<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Create Article Action
 */
final class CreateArticleAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/articles/create',
            ['title' => 'Create Article', 'error' => null, 'old' => []],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return $this->show($request);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
