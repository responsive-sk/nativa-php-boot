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
 * Admin Create Form Action
 */
class CreateFormAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->store($request);
        }
        return $this->createForm($request);
    }

    private function createForm(Request $request): Response
    {
        $content = $this->renderer->render(
            'admin/pages/forms/create',
            ['title' => 'Create Form'],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function store(Request $request): Response
    {
        try {
            $name = (string) $request->request->get('name', '');
            $slug = (string) $request->request->get('slug', '');
            $schema = json_decode($request->request->get('schema', '[]'), true);
            $emailNotification = (string) $request->request->get('emailNotification', '');
            $successMessage = (string) $request->request->get('successMessage', 'Thank you for your submission!');

            $form = $this->formManager->create(
                name: $name,
                slug: $slug,
                schema: $schema ?? [],
                emailNotification: $emailNotification ?: null,
                successMessage: $successMessage
            );

            return $this->redirect('/admin/forms');

        } catch (\Throwable $e) {
            return $this->error('Failed to create form: ' . $e->getMessage(), 500);
        }
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
