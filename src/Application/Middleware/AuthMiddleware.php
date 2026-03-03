<?php

declare(strict_types = 1);

namespace Application\Middleware;

use Application\Services\AuthService;
use Application\Services\SessionManager;
use Domain\Model\User;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;

/**
 * Authentication Middleware.
 *
 * Checks if user is authenticated
 */
final class AuthMiddleware
{
    private const SESSION_TIMEOUT = 1800; // 30 minutes

    public function __construct(
        private readonly AuthService $authService,
        private readonly SessionManager $sessionManager,
    ) {}

    /**
     * Handle request
     * Returns null if authenticated, redirect response if not.
     */
    public function handle(Request $request): ?Response
    {
        // Check session timeout
        if (!$this->sessionManager->isActive(self::SESSION_TIMEOUT)) {
            // Session timed out - destroy and redirect to login
            $this->sessionManager->destroy();

            // Note: Flash messages not available in Symfony HttpFoundation without SessionBundle
            return new Response('', 302, ['Location' => '/login']);
        }

        if (!$this->authService->check()) {
            // Store intended destination
            $request->getSession()->set('intended_url', $request->getPathInfo());

            return new Response('', 302, ['Location' => '/login']);
        }

        // Update last activity time
        $this->sessionManager->touch();

        return null;
    }

    /**
     * Get current authenticated user.
     */
    public function user(): ?User
    {
        return $this->authService->user();
    }

    /**
     * Get current user ID.
     */
    public function userId(): ?string
    {
        $user = $this->user();

        return $user?->id();
    }
}
