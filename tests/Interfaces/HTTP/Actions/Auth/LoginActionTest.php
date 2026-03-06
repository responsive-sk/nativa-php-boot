<?php

declare(strict_types = 1);

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
 *
 * @internal
 */
final class LoginActionTest extends TestCase
{
    private AuthService & MockObject $authService;

    private MockObject & SessionManager $sessionManager;

    private MockObject & TemplateRenderer $renderer;

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
            ->expects(self::once())
            ->method('check')
            ->willReturn(false);

        $expectedContent = '<html>Login Form</html>';

        $this->renderer
            ->expects(self::once())
            ->method('render')
            ->with('login', [
                'title' => 'Login',
                'error' => null,
            ])
            ->willReturn($expectedContent);

        $request = new Request();
        $response = $this->action->handle($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($expectedContent, $response->getBody()->getContents());
    }

    public function testShowRedirectsIfAlreadyLoggedIn(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('check')
            ->willReturn(true);

        $this->renderer
            ->expects(self::never())
            ->method('render');

        $request = new Request();
        $response = $this->action->handle($request);

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/admin', $response->headers->get('Location'));
    }

    public function testPostSuccessfulLogin(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects(self::once())
            ->method('attempt')
            ->with(
                self::isInstanceOf(LoginCommand::class),
                '127.0.0.1'
            )
            ->willReturn(true);

        $this->sessionManager
            ->expects(self::once())
            ->method('regenerate')
            ->with(true);

        $this->sessionManager
            ->expects(self::once())
            ->method('get')
            ->with('intended_url', '/admin')
            ->willReturn('/admin');

        $this->sessionManager
            ->expects(self::once())
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

        self::assertSame(302, $response->getStatusCode());
    }

    public function testPostWithInvalidCredentials(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects(self::once())
            ->method('attempt')
            ->willReturn(false);

        $expectedContent = '<html>Login Form with Error</html>';

        $this->renderer
            ->expects(self::once())
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

        self::assertSame(401, $response->getStatusCode());
    }

    public function testPostWithIntendedUrl(): void
    {
        $this->authService
            ->expects(self::once())
            ->method('check')
            ->willReturn(false);

        $this->authService
            ->expects(self::once())
            ->method('attempt')
            ->willReturn(true);

        $this->sessionManager
            ->expects(self::once())
            ->method('regenerate');

        $this->sessionManager
            ->expects(self::once())
            ->method('get')
            ->with('intended_url', '/admin')
            ->willReturn('/dashboard');

        $this->sessionManager
            ->expects(self::once())
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

        self::assertSame(302, $response->getStatusCode());
        self::assertSame('/dashboard', $response->headers->get('Location'));
    }

    public function testHandleWithInvalidMethod(): void
    {
        $this->authService
            ->expects(self::never())
            ->method('check');

        $request = new Request();
        $request->setMethod('PUT');

        $response = $this->action->handle($request);

        self::assertSame(405, $response->getStatusCode());
        self::assertSame('Method not allowed', $response->getBody()->getContents());
    }
}
