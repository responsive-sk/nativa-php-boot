<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Admin\Settings;

use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * View Settings Action
 */
final class ViewSettingsAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/settings/index',
            ['title' => 'Settings', 'settings' => $this->getSettings()],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function getSettings(): array
    {
        // TODO: Load settings from database/config
        return [
            'site_name' => $_ENV['APP_NAME'] ?? 'PHP CMS',
            'site_url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
            'admin_email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@phpcms.local',
        ];
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
