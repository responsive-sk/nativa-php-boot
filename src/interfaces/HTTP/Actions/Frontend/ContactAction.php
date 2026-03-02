<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\DTOs\SubmitContactCommand;
use Application\Services\ContactManager;
use Application\Middleware\RateLimitMiddleware;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Contact Form Action
 */
final class ContactAction extends Action
{
    public function __construct(
        private readonly ContactManager $contactManager,
        private readonly TemplateRenderer $renderer,
        private readonly RateLimitMiddleware $rateLimitMiddleware,
    ) {
    }

    #[\Override]
    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->submit($request);
        }
        return $this->show($request);
    }

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'frontend/contact',
            [
                'title' => 'Contact Us',
                'pageTitle' => 'Contact',
                'page' => 'contact',
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
        if ($rateLimitResponse !== null) {
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
            $isHtmx = $request->header('HX-Request') === 'true';
            
            if ($isHtmx) {
                // Return just the success message fragment for HTMX
                $message = '<div class="contact__success">Thank you for your message! We will get back to you soon.</div>';
                return $this->html($message);
            }

            // Redirect with success message for regular requests
            $request->getSession()->getFlashBag()->set('success', 'Thank you for your message! We will get back to you soon.');

            return $this->redirect('/contact');

        } catch (\Application\Exceptions\ValidationException $e) {
            // Check if this is an HTMX request
            $isHtmx = $request->header('HX-Request') === 'true';
            
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
                    'title' => 'Contact Us',
                    'pageTitle' => 'Contact',
                    'page' => 'contact',
                    'errors' => $e->getErrors(),
                    'old' => [
                        'name' => $request->getRequestParam('name'),
                        'email' => $request->getRequestParam('email'),
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
        $db = \Infrastructure\Persistence\DatabaseConnection::getInstance()->getConnection();
        $rateLimiter = new \Application\Services\RateLimiter($db);
        
        return new self(
            $container->get(ContactManager::class),
            $container->get(TemplateRenderer::class),
            new \Application\Middleware\RateLimitMiddleware($rateLimiter),
        );
    }
}
