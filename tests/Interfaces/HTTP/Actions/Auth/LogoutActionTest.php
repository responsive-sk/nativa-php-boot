<?php

declare(strict_types = 1);

namespace Tests\Interfaces\HTTP\Actions\Auth;

use Application\Services\AuthService;
use Infrastructure\Http\Request;
use Interfaces\HTTP\Actions\Auth\LogoutAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\Actions\Auth\LogoutAction
 *
 * @internal
 */
final class LogoutActionTest extends TestCase
{
    private AuthService & MockObject $authService;

    private LogoutAction $action;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->action = new LogoutAction($this->authService);
    }

    public function testLogoutCallsAuthService(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('logout');

        $request = new Request();
        $this->action->handle($request);
    }

    public function testLogoutReturnsRedirectResponse(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('logout');

        $request = new Request();
        $response = $this->action->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/login', $response->headers->get('Location'));
    }

    public function testLogoutClearsSessionCookie(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('logout');

        $request = new Request();
        $response = $this->action->handle($request);

        $cookies = $response->headers->get('Set-Cookie');
        self::assertStringContainsString('PHPSESSID', $cookies);
        self::assertStringContainsString('expires', $cookies);
    }
}
