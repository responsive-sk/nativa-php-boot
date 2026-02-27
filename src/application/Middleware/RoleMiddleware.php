<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based Middleware
 *
 * Checks if user has required role
 */
class RoleMiddleware
{
    /**
     * @param string[] $roles Allowed roles
     */
    public function __construct(
        private readonly AuthService $authService,
        private readonly array $roles,
    ) {
    }

    /**
     * Handle request
     * Returns null if user has required role, 403 response if not
     */
    public function handle(Request $request): ?Response
    {
        $user = $this->authService->user();

        if ($user === null) {
            return new Response('', 302, ['Location' => '/login']);
        }

        if (!in_array($user->roleString(), $this->roles, true)) {
            return new Response('Forbidden - Insufficient role', 403);
        }

        return null;
    }
}
