<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Frontend;

use Application\DTOs\SubmitContactCommand;
use Application\Services\ContactManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contact Form Action
 */
class ContactAction extends Action
{
    public function __construct(
        private readonly ContactManager $contactManager,
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

    public function show(Request $request): Response
    {
        $content = $this->renderer->render(
            'pages/contact',
            ['title' => 'Contact Us'],
            'layouts/base'
        );

        return $this->html($content);
    }

    public function submit(Request $request): Response
    {
        try {
            $command = new SubmitContactCommand(
                name: (string) $request->request->get('name', ''),
                email: (string) $request->request->get('email', ''),
                message: (string) $request->request->get('message', ''),
                subject: (string) $request->request->get('subject', ''),
            );

            $this->contactManager->submit(
                name: $command->name,
                email: $command->email,
                message: $command->message,
                subject: $command->subject,
            );

            // Redirect with success message
            $request->getSession()?->getFlashBag()->set('success', 'Thank you for your message! We will get back to you soon.');
            
            return $this->redirect('/contact');

        } catch (\Application\Exceptions\ValidationException $e) {
            // Re-display form with errors
            $content = $this->renderer->render(
                'pages/contact',
                [
                    'title' => 'Contact Us',
                    'errors' => $e->getErrors(),
                    'old' => [
                        'name' => $request->request->get('name'),
                        'email' => $request->request->get('email'),
                        'subject' => $request->request->get('subject'),
                        'message' => $request->request->get('message'),
                    ],
                ],
                'layouts/base'
            );

            return $this->html($content, 400);
        } catch (\Throwable $e) {
            return $this->error('An error occurred. Please try again later.', 500);
        }
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(ContactManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
