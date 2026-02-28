<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Services\AuthService;
use Application\Services\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Middleware
 *
 * Checks if user is authenticated
 */
class AuthMiddleware
{
    private const SESSION_TIMEOUT = 1800; // 30 minutes

    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionManager $sessionManager,
    ) {
    }

    /**
     * Handle request
     * Returns null if authenticated, redirect response if not
     */
    public function handle(Request $request): ?Response
    {
        // Check session timeout
        if (!$this->sessionManager->isActive(self::SESSION_TIMEOUT)) {
            // Session timed out - destroy and redirect to login
            $this->sessionManager->destroy();
            $request->getSession()?->getFlashBag()->set('error', 'Your session has timed out. Please log in again.');
            return new Response('', 302, ['Location' => '/login']);
        }

        if (!$this->authService->check()) {
            // Store intended destination
            $request->getSession()?->set('intended_url', $request->getPathInfo());

            return new Response('', 302, ['Location' => '/login']);
        }

        // Update last activity time
        $this->sessionManager->touch();

        return null;
    }

    /**
     * Get current authenticated user
     */
    public function user(): ?\Domain\Model\User
    {
        return $this->authService->user();
    }

    /**
     * Get current user ID
     */
    public function userId(): ?string
    {
        $user = $this->user();
        return $user?->id();
    }
}
