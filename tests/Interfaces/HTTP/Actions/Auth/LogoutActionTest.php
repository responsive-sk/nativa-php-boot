<?php

declare(strict_types=1);

namespace Tests\Interfaces\HTTP\Actions\Auth;

use Application\Services\AuthService;
use Infrastructure\Http\Request;
use Interfaces\HTTP\Actions\Auth\LogoutAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\Actions\Auth\LogoutAction
 */
final class LogoutActionTest extends TestCase
{
    private AuthService&MockObject $authService;

    private LogoutAction $action;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->action = new LogoutAction($this->authService);
    }

    public function testLogoutCallsAuthService(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('logout');

        $request = new Request();
        $this->action->handle($request);
    }

    public function testLogoutReturnsRedirectResponse(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('logout');

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->headers->get('Location'));
    }

    public function testLogoutClearsSessionCookie(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('logout');

        $request = new Request();
        $response = $this->action->handle($request);

        $cookies = $response->headers->get('Set-Cookie');
        $this->assertStringContainsString('PHPSESSID', $cookies);
        $this->assertStringContainsString('expires', $cookies);
    }
}
