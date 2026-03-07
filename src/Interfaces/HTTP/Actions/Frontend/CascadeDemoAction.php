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
        // Direct PHP render - cascade-demo.php is self-contained HTML
        $templatePath = $this->renderer->getTemplatesPath() . '/vanilla/frontend/pages/cascade-demo.php';
        
        if (!file_exists($templatePath)) {
            return $this->notFound('Cascade demo page not found');
        }
        
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        
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
