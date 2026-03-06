<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Svelte Hybrid Test Action.
 */
final class SvelteTestAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $articles = [
            [
                'id' => '1',
                'title' => 'Getting Started with Svelte',
                'excerpt' => 'Learn how to integrate Svelte with PHP in a hybrid approach.',
                'slug' => 'getting-started-svelte',
                'publishedAt' => '2026-03-06',
                'tags' => ['svelte', 'php', 'hybrid'],
            ],
            [
                'id' => '2',
                'title' => 'Modern Web Development',
                'excerpt' => 'Combining the best of both worlds: PHP backend + Svelte frontend.',
                'slug' => 'modern-web-dev',
                'publishedAt' => '2026-03-05',
                'tags' => ['web', 'development'],
            ],
            [
                'id' => '3',
                'title' => 'Progressive Enhancement',
                'excerpt' => 'Why SEO matters and how to keep it while using modern frameworks.',
                'slug' => 'progressive-enhancement',
                'publishedAt' => '2026-03-04',
                'tags' => ['seo', 'performance'],
            ],
        ];

        return $this->renderPage(
            $request,
            $this->renderer,
            'svelte-test',
            ['articles' => $articles, 'pageTitle' => 'Svelte Hybrid Test'],
            null  // Standalone page
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
