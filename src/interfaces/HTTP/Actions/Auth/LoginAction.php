<?php

declare(strict_types=1);

namespace Interfaces\HTTP\Actions\Auth;

use Application\DTOs\LoginCommand;
use Application\Services\AuthService;
use Application\Services\SessionManager;
use Infrastructure\Container\ContainerFactory;
use Interfaces\HTTP\Actions\Action;
use Interfaces\HTTP\View\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Login Action
 */
final class LoginAction extends Action
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionManager $sessionManager,
        private readonly TemplateRenderer $renderer,
    ) {
    }

    /**
     * Show login form (GET)
     */
    public function show(Request $request): Response
    {
        // If already logged in, redirect to dashboard
        if ($this->authService->check()) {
            return $this->redirect('/admin');
        }

        $content = $this->renderer->render('auth/login', [
            'title' => 'Login',
            'error' => null,
        ]);

        return $this->html($content);
    }

    /**
     * Handle login submission (POST)
     */
    public function post(Request $request): Response
    {
        // If already logged in, redirect to dashboard
        if ($this->authService->check()) {
            return $this->redirect('/admin');
        }

        try {
            $command = LoginCommand::fromArray([
                'email' => $request->request->get('email', ''),
                'password' => $request->request->get('password', ''),
                'remember_me' => $request->request->get('remember_me', false),
            ]);

            $ipAddress = $request->getClientIp() ?? 'unknown';

            if ($this->authService->attempt($command, $ipAddress)) {
                // Login successful - redirect to intended URL or default to /admin
                $intendedUrl = $this->sessionManager->get('intended_url', '/admin');
                $this->sessionManager->remove('intended_url');
                
                return $this->redirect($intendedUrl);
            }

            $error = 'Invalid email or password';
        } catch (\InvalidArgumentException $e) {
            error_log('[LoginAction] InvalidArgumentException: ' . $e->getMessage());
            $error = $e->getMessage();
        } catch (\RuntimeException $e) {
            error_log('[LoginAction] RuntimeException: ' . $e->getMessage());
            $error = $e->getMessage();
        }

        $content = $this->renderer->render('auth/login', [
            'title' => 'Login',
            'error' => $error ?? 'Login failed',
            'old' => [
                'email' => $request->request->get('email', ''),
            ],
        ]);

        return $this->html($content, 401);
    }

    /**
     * Handle request (required by ActionInterface)
     */
    public function handle(Request $request): Response
    {
        if ($request->getMethod() === 'GET') {
            return $this->show($request);
        }

        if ($request->getMethod() === 'POST') {
            return $this->post($request);
        }

        return new Response('Method not allowed', 405);
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(AuthService::class),
            $container->get(SessionManager::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
