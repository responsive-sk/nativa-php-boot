<?php

declare(strict_types = 1);

namespace Interfaces\HTTP\Actions\Auth;

use Application\Services\AuthService;
use Infrastructure\Container\ContainerFactory;
use Infrastructure\Http\Request;
use Infrastructure\Http\Response;
use Interfaces\HTTP\Actions\Action;

/**
 * Logout Action.
 */
final class LogoutAction extends Action
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Handle logout.
     */
    #[\Override]
    public function handle(Request $request): Response
    {
        $this->authService->logout();

        // Create redirect response with cleared session cookie
        $response = new Response('', 302, ['Location' => '/login']);
        $response->clearCookie('PHPSESSID', '/', 'localhost', false, true);

        return $response;
    }

    public static function create(): self
    {
        $container = ContainerFactory::create();

        return new self(
            $container->get(AuthService::class),
        );
    }
}
