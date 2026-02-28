<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\FormManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Forms List Action
 */
final class FormsAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $forms = $this->formManager->findAll();

        $content = $this->renderer->render(
            'admin/pages/forms/index',
            [
                'title' => 'Forms',
                'forms' => $forms,
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(FormManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
