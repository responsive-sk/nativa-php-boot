<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Middleware
 *
 * Checks if user is authenticated
 */
class AuthMiddleware
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * Handle request
     * Returns null if authenticated, redirect response if not
     */
    public function handle(Request $request): ?Response
    {
        if (!$this->authService->check()) {
            // Store intended destination
            $request->getSession()?->set('intended_url', $request->getPathInfo());

            return new Response('', 302, ['Location' => '/login']);
        }

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
