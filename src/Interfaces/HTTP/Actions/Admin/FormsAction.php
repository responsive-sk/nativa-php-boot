<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin;

use Application\Services\FormManager;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Admin Forms List Action.
 */
final class FormsAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        $forms = $this->formManager->findAll();

        return $this->renderPage(
            $request,
            $this->renderer,
            'admin/pages/forms/index',
            [
                'title' => 'Forms',
                'forms' => $forms,
            ],
            'admin'
        );
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
