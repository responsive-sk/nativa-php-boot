<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\DTOs\SubmitContactCommand;
use Application\Exceptions\ValidationException;
use Application\Middleware\RateLimitMiddleware;
use Application\Services\ContactManager;
use Application\Services\RateLimiter;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Infrastructure\Persistence\DatabaseConnection;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Contact Form Action.
 */
final class ContactAction extends Action
{
    public function __construct(
        private readonly ContactManager $contactManager,
        private readonly TemplateRenderer $renderer,
        private readonly RateLimitMiddleware $rateLimitMiddleware,
    ) {}

    #[\Override]
    public function handle(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            return $this->submit($request);
        }

        return $this->show($request);
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'frontend/contact',
            [
                'title'           => 'Contact Us',
                'pageTitle'       => 'Contact',
                'page'            => 'contact',
                'metaDescription' => 'Get in touch with us',
            ],
            'frontend/layouts/frontend'
        );

        return $this->html($content);
    }

    public function submit(Request $request): Response
    {
        // Apply rate limiting
        $rateLimitResponse = $this->rateLimitMiddleware->limitFormSubmission($request);
        if (null !== $rateLimitResponse) {
            return $rateLimitResponse;
        }

        try {
            $command = new SubmitContactCommand(
                name: (string) $request->getRequestParam('name', ''),
                email: (string) $request->getRequestParam('email', ''),
                message: (string) $request->getRequestParam('message', ''),
                subject: (string) $request->getRequestParam('subject', ''),
            );

            $this->contactManager->submit(
                name: $command->name,
                email: $command->email,
                message: $command->message,
                subject: $command->subject,
            );

            // Check if this is an HTMX request
            $isHtmx = 'true' === $request->header('HX-Request');

            if ($isHtmx) {
                // Return just the success message fragment for HTMX
                $message = '<div class="contact__success">Thank you for your message! We will get back to you soon.</div>';

                return $this->html($message);
            }

            // Redirect with success message for regular requests
            $request->getSession()->getFlashBag()->set('success', 'Thank you for your message! We will get back to you soon.');

            return $this->redirect('/contact');
        } catch (ValidationException $e) {
            // Check if this is an HTMX request
            $isHtmx = 'true' === $request->header('HX-Request');

            if ($isHtmx) {
                // Return just the error messages fragment for HTMX
                $errorsHtml = '<div class="contact__errors">';
                foreach ($e->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorsHtml .= '<p class="contact__error">' . htmlspecialchars($error) . '</p>';
                    }
                }
                $errorsHtml .= '</div>';

                return $this->html($errorsHtml, 400);
            }

            // Re-display form with errors for regular requests
            $content = $this->renderer->render(
                'frontend/contact',
                [
                    'title'     => 'Contact Us',
                    'pageTitle' => 'Contact',
                    'page'      => 'contact',
                    'errors'    => $e->getErrors(),
                    'old'       => [
                        'name'    => $request->getRequestParam('name'),
                        'email'   => $request->getRequestParam('email'),
                        'subject' => $request->getRequestParam('subject'),
                        'message' => $request->getRequestParam('message'),
                    ],
                ],
                'frontend/layouts/frontend'
            );

            return $this->html($content, 400);
        } catch (\Throwable $e) {
            return $this->error('An error occurred. Please try again later.', 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        $db = DatabaseConnection::getInstance()->getConnection();
        $rateLimiter = new RateLimiter($db);

        return new self(
            $container->get(ContactManager::class),
            $container->get(TemplateRenderer::class),
            new RateLimitMiddleware($rateLimiter),
        );
    }
}
