<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Auth;

use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;

/**
 * Register Action.
 */
final class RegisterAction extends Action
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
    ) {}

    /**
     * Show register form (GET).
     */
    public function show(Request $request): Response
    {
        $content = $this->renderer->render('register', [
            'title' => 'Register',
            'error' => null,
            'old'   => [],
        ]);

        return $this->html($content);
    }

    /**
     * Handle register submission (POST).
     */
    public function post(Request $request): Response
    {
        // TODO: Implement registration logic
        $error = 'Registration not implemented yet';

        $content = $this->renderer->render('register', [
            'title' => 'Register',
            'error' => $error,
            'old'   => [
                'name'  => $request->getRequestParam('name', ''),
                'email' => $request->getRequestParam('email', ''),
            ],
        ]);

        return $this->html($content, 400);
    }

    /**
     * Handle request (required by ActionInterface).
     */
    #[\Override]
    public function handle(Request $request): Response
    {
        if ('GET' === $request->getMethod()) {
            return $this->show($request);
        }

        if ('POST' === $request->getMethod()) {
            return $this->post($request);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(TemplateRenderer::class),
        );
    }
}
