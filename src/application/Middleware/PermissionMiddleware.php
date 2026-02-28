<?php

declare(strict_types=1);

namespace Application\Middleware;

use Application\Services\AuthService;
use Domain\ValueObjects\PermissionName;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permission-based Middleware
 *
 * Checks if user has required permission
 */
class PermissionMiddleware
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly string $permission,
    ) {
    }

    /**
     * Handle request
     * Returns null if user has permission, 403 response if not
     */
    public function handle(Request $request): ?Response
    {
        $user = $this->authService->user();

        if ($user === null) {
            return new Response('', 302, ['Location' => '/login']);
        }

        // Check if user has the required permission
        $permissionName = PermissionName::fromString($this->permission);
        
        if (!$user->hasPermission($permissionName)) {
            return new Response(
                'Forbidden - Missing permission: ' . $this->permission,
                403
            );
        }

        return null;
    }
}
