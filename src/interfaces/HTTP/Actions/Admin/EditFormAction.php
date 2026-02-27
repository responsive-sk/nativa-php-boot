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
 * Admin Edit Form Action
 */
class EditFormAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->update($request);
        }
        return $this->edit($request);
    }

    private function edit(Request $request): Response
    {
        $id = $this->param($request, 'id');
        $form = $this->formManager->findById($id);

        if ($form === null) {
            return $this->notFound('Form not found');
        }

        $content = $this->renderer->render(
            'admin/pages/forms/edit',
            [
                'title' => 'Edit Form',
                'form' => $form,
            ],
            'admin/layouts/base'
        );

        return $this->html($content);
    }

    private function update(Request $request): Response
    {
        try {
            $id = $this->param($request, 'id');
            $name = (string) $request->request->get('name', '');
            $schema = json_decode($request->request->get('schema', '[]'), true);
            $emailNotification = (string) $request->request->get('emailNotification', '');
            $successMessage = (string) $request->request->get('successMessage', 'Thank you for your submission!');

            $this->formManager->update(
                formId: $id,
                name: $name,
                schema: $schema ?? [],
                emailNotification: $emailNotification ?: null,
                successMessage: $successMessage
            );

            return $this->redirect('/admin/forms');

        } catch (\Throwable $e) {
            return $this->error('Failed to update form: ' . $e->getMessage(), 500);
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
