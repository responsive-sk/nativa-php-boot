<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Article;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Create Article Action.
 */
final class CreateArticleAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    public function show(Request $request): Response
    {
        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/articles/create',
            ['title' => 'Create Article', 'error' => null, 'old' => []],
            'admin'
        );
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('GET' === $request->getMethod()) {
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
