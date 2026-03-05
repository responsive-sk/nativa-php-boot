<?php

declare(strict_types=1);

namespace Tests\Interfaces\HTTP\Actions\Auth;

use Application\DTOs\LoginCommand;
use Application\Services\AuthService;
use Application\Services\SessionManager;
use Infrastructure\Http\Request;
use Interfaces\HTTP\Actions\Auth\LoginAction;
use Interfaces\HTTP\View\TemplateRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\Actions\Auth\LoginAction
 */
final class LoginActionTest extends TestCase
{
    private AuthService&MockObject $authService;

    private SessionManager&MockObject $sessionManager;

    private TemplateRenderer&MockObject $renderer;

    private LoginAction $action;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->sessionManager = $this->createMock(SessionManager::class);
        $this->renderer = $this->createMock(TemplateRenderer::class);
        $this->action = new LoginAction(
            $this->authService,
            $this->sessionManager,
            $this->renderer
        );
    }

    public function testShowDisplaysLoginForm(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $expectedContent = '<html>Login Form</html>';

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with('login', [
                'title' => 'Login',
                'error' => null,
            ])
            ->willReturn($expectedContent);

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedContent, $response->getBody()->getContents());
    }

    public function testShowRedirectsIfAlreadyLoggedIn(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->renderer
            ->expects($this->never())
            ->method('render');

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/admin', $response->headers->get('Location'));
    }

    public function testPostSuccessfulLogin(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects($this->once())
            ->method('attempt')
            ->with(
                $this->isInstanceOf(LoginCommand::class),
                '127.0.0.1'
            )
            ->willReturn(true);

        $this->sessionManager
            ->expects($this->once())
            ->method('regenerate')
            ->with(true);

        $this->sessionManager
            ->expects($this->once())
            ->method('get')
            ->with('intended_url', '/admin')
            ->willReturn('/admin');

        $this->sessionManager
            ->expects($this->once())
            ->method('remove')
            ->with('intended_url');

        $request = new Request();
        $request->setMethod('POST');
        $request->setBody([
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $response = $this->action->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testPostWithInvalidCredentials(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects($this->once())
            ->method('attempt')
            ->willReturn(false);

        $expectedContent = '<html>Login Form with Error</html>';

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with('login', [
                'title' => 'Login',
                'error' => 'Invalid email or password',
                'old'   => ['email' => 'test@example.com'],
            ])
            ->willReturn($expectedContent);

        $request = new Request();
        $request->setMethod('POST');
        $request->setBody([
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $response = $this->action->handle($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testPostWithIntendedUrl(): void
    {
        $this->authService
            ->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects($this->once())
            ->method('attempt')
            ->willReturn(true);

        $this->sessionManager
            ->expects($this->once())
            ->method('regenerate');

        $this->sessionManager
            ->expects($this->once())
            ->method('get')
            ->with('intended_url', '/admin')
            ->willReturn('/dashboard');

        $this->sessionManager
            ->expects($this->once())
            ->method('remove')
            ->with('intended_url');

        $request = new Request();
        $request->setMethod('POST');
        $request->setBody([
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $response = $this->action->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/dashboard', $response->headers->get('Location'));
    }

    public function testHandleWithInvalidMethod(): void
    {
        $this->authService
            ->expects($this->never())
            ->method('check');

        $request = new Request();
        $request->setMethod('PUT');

        $response = $this->action->handle($request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('Method not allowed', $response->getBody()->getContents());
    }
}
