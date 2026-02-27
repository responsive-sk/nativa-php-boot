<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Services\FormManager;
use Domain\Events\EventDispatcherInterface;
use Domain\Events\FormSubmitted;
use Domain\Repository\FormSubmissionRepositoryInterface;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Queue\Handlers\OutboxProcessor;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend Form Display and Submission Action
 */
class DisplayFormAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly FormSubmissionRepositoryInterface $submissionRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?OutboxProcessor $outboxProcessor,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->submit($request);
        }
        return $this->show($request);
    }

    private function show(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        $form = $this->formManager->findBySlug($slug);

        if ($form === null) {
            return $this->notFound('Form not found');
        }

        $content = $this->renderer->render(
            'pages/form',
            [
                'title' => $form->name(),
                'form' => $form,
            ],
            'layouts/base'
        );

        return $this->html($content);
    }

    private function submit(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        $form = $this->formManager->findBySlug($slug);

        if ($form === null) {
            return $this->notFound('Form not found');
        }

        try {
            // Collect form data
            $formData = $request->request->all();
            
            // Remove CSRF token and submit button from data
            unset($formData['_token'], $formData['submit']);

            // Save submission
            $this->submissionRepository->saveSubmission(
                formId: $form->id(),
                data: $formData,
                ipAddress: $request->getClientIp(),
                userAgent: $request->headers->get('User-Agent', '')
            );

            // Dispatch event
            $event = new FormSubmitted(
                $form->id(),
                $form->name(),
                $formData
            );
            $this->eventDispatcher->dispatch($event);

            // Add to outbox for async processing
            if ($this->outboxProcessor !== null) {
                $this->outboxProcessor->add(
                    'FormSubmitted',
                    [
                        'form_id' => $form->id(),
                        'form_name' => $form->name(),
                        'form_slug' => $form->slug(),
                        'submission' => $formData,
                        'submitted_at' => date('Y-m-d H:i:s'),
                    ]
                );
            }

            // Redirect with success
            return $this->redirect('/form/' . $slug . '?success=1');

        } catch (\Throwable $e) {
            return $this->error('Failed to submit form: ' . $e->getMessage(), 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(FormManager::class),
            $container->get(\Domain\Repository\FormSubmissionRepositoryInterface::class),
            $container->get(\Domain\Events\EventDispatcherInterface::class),
            $container->get(\Infrastructure\Queue\Handlers\OutboxProcessor::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
