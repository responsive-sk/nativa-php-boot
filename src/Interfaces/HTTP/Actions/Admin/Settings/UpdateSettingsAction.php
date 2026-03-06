<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Admin\Settings;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Update Settings Action.
 */
final class UpdateSettingsAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        try {
            $data = $request->getRequest();

            // TODO: Validate and save settings to database
            // For now, just redirect back with success
            return $this->redirect('/admin/settings');
        } catch (\Throwable $e) {
            return $this->renderPage(
                $request,
                $this->renderer,
                'admin/settings/index',
                [
                    'title'    => 'Settings',
                    'settings' => $data,
                    'error'    => $e->getMessage(),
                ],
                'admin',
                500
            );
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
