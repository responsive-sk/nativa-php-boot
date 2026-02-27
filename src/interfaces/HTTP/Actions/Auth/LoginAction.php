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
class LoginAction extends Action
{
    public function __construct(
        private readonly AuthService $authService,
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
                // Login successful
                $intendedUrl = $request->getSession()?->get('intended_url', '/admin');
                $request->getSession()?->remove('intended_url');

                return $this->redirect($intendedUrl ?: '/admin');
            }

            $error = 'Invalid email or password';
        } catch (\InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (\RuntimeException $e) {
            $error = $e->getMessage();
        } catch (\Throwable $e) {
            $error = 'An error occurred. Please try again.';
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

    public static function create(): self
    {
        $container = ContainerFactory::create();
        return new self(
            $container->get(AuthService::class),
            $container->get(TemplateRenderer::class),
        );
    }
}
