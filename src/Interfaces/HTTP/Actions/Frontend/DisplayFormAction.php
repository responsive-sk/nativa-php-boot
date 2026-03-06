<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\Middleware\RateLimitMiddleware;
use Application\Services\FormManager;
use Application\Services\FormValidationService;
use Application\Services\RateLimiter;
use Domain\Events\EventDispatcherInterface;
use Domain\Events\FormSubmitted;
use Domain\Repository\FormSubmissionRepositoryInterface;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Infrastructure\Persistence\DatabaseConnection;
use Infrastructure\Queue\Handlers\OutboxProcessor;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Frontend Form Display and Submission Action.
 */
final class DisplayFormAction extends Action
{
    public function __construct(
        private readonly FormManager $formManager,
        private readonly FormSubmissionRepositoryInterface $submissionRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ?OutboxProcessor $outboxProcessor,
        private readonly TemplateRenderer $renderer,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            return $this->submit($request);
        }

        return $this->show($request);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(FormManager::class),
            $container->get(FormSubmissionRepositoryInterface::class),
            $container->get(EventDispatcherInterface::class),
            $container->get(OutboxProcessor::class),
            $container->get(TemplateRenderer::class),
        );
    }

    private function show(Request $request): Response
    {
        $slug = $this->param($request, 'slug');
        $form = $this->formManager->findBySlug($slug);

        if (null === $form) {
            return $this->notFound('Form not found');
        }

        $content = $this->renderer->render(
            'pages/form',
            [
                'title' => $form->name(),
                'form'  => $form,
            ],
            'frontend'
        );

        return $this->html($content);
    }

    private function submit(Request $request): Response
    {
        // Apply rate limiting
        $rateLimitMiddleware = new RateLimitMiddleware(
            new RateLimiter(
                DatabaseConnection::getInstance()->getConnection()
            )
        );

        $rateLimitResponse = $rateLimitMiddleware->limitFormSubmission($request);
        if (null !== $rateLimitResponse) {
            return $rateLimitResponse;
        }

        $slug = $this->param($request, 'slug');
        $form = $this->formManager->findBySlug($slug);

        if (null === $form) {
            return $this->notFound('Form not found');
        }

        try {
            // Validate form data
            $validator = new FormValidationService();
            $formData = $request->getRequest();

            // Remove CSRF token and submit button from data
            unset($formData['_token'], $formData['submit']);

            if (!$validator->validate($formData, $form->schema())) {
                // Redirect back with errors
                $session = $request->getSession();
                $session->getFlashBag()->set('errors', $validator->getErrors());
                $session->getFlashBag()->set('old', $formData);

                return $this->redirect('/form/' . $slug);
            }

            // Use sanitized data
            $sanitizedData = $validator->getSanitizedData();

            // Save submission
            $this->submissionRepository->saveSubmission(
                formId: $form->id(),
                data: $sanitizedData,
                ipAddress: $request->getClientIp(),
                userAgent: $request->getUserAgent()
            );

            // Dispatch event
            $submissionId = bin2hex(random_bytes(8));
            $event = new FormSubmitted(
                $form->id(),
                $form->name(),
                $submissionId,
                $sanitizedData,
                $request->getClientIp()
            );
            $this->eventDispatcher->dispatch($event);

            // Add to outbox for async processing
            if (null !== $this->outboxProcessor) {
                $this->outboxProcessor->add(
                    'FormSubmitted',
                    [
                        'form_id'      => $form->id(),
                        'form_name'    => $form->name(),
                        'form_slug'    => $form->slug(),
                        'submission'   => $formData,
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
}
