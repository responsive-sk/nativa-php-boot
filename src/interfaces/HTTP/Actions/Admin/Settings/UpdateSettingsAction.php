<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Settings;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Update Settings Action
 */
final class UpdateSettingsAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $data = $request->request->all();
            
            // TODO: Validate and save settings to database
            // For now, just redirect back with success
            return $this->redirect('/admin/settings');
        } catch (\Throwable $e) {
            $content = $this->renderer->render(
                'admin/settings/index',
                [
                    'title' => 'Settings',
                    'settings' => $data,
                    'error' => $e->getMessage(),
                ],
                'admin/layouts/base'
            );

            return $this->html($content, 500);
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
