<?php

declare(strict_types=1);

namespace Tests\Interfaces\HTTP\Actions\Frontend;

use Application\Middleware\RateLimitMiddleware;
use Application\Services\ContactManager;
use Infrastructure\Http\Request;
use Interfaces\HTTP\Actions\Frontend\ContactAction;
use Interfaces\HTTP\View\TemplateRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\Actions\Frontend\ContactAction
 */
final class ContactActionTest extends TestCase
{
    private ContactManager&MockObject $contactManager;

    private TemplateRenderer&MockObject $renderer;

    private RateLimitMiddleware&MockObject $rateLimitMiddleware;

    private ContactAction $action;

    protected function setUp(): void
    {
        $this->contactManager = $this->createMock(ContactManager::class);
        $this->renderer = $this->createMock(TemplateRenderer::class);
        $this->rateLimitMiddleware = $this->createMock(RateLimitMiddleware::class);
        $this->action = new ContactAction(
            $this->contactManager,
            $this->renderer,
            $this->rateLimitMiddleware,
        );
    }

    public function testShowDisplaysContactForm(): void
    {
        $expectedContent = '<html>Contact Form</html>';

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                'frontend/contact',
                [
                    'title'           => 'Contact Us',
                    'pageTitle'       => 'Contact',
                    'page'            => 'contact',
                    'metaDescription' => 'Get in touch with us',
                ],
                'frontend/layouts/frontend'
            )
            ->willReturn($expectedContent);

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedContent, $response->getBody()->getContents());
    }

    public function testSubmitSuccessfulSubmission(): void
    {
        $this->contactManager
            ->expects($this->once())
            ->method('submit')
            ->with(
                'John Doe',
                'john@example.com',
                'Test message',
                'Subject'
            );

        $this->rateLimitMiddleware
            ->expects($this->once())
            ->method('limitFormSubmission')
            ->willReturn(null);

        $expectedContent = '<div class="contact__success">Thank you for your message!</div>';

        $this->renderer
            ->expects($this->never())
            ->method('render');

        $request = new Request();
        $request->setMethod('POST');
        $request->setBody([
            'name'    => 'John Doe',
            'email'   => 'john@example.com',
            'message' => 'Test message',
            'subject' => 'Subject',
        ]);
        $request->headers->set('HX-Request', 'true');

        $response = $this->action->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSubmitWithValidationErrors(): void
    {
        $this->rateLimitMiddleware
            ->expects($this->once())
            ->method('limitFormSubmission')
            ->willReturn(null);

        $expectedContent = '<div class="contact__errors">Error</div>';

        $this->renderer
            ->expects($this->never())
            ->method('render');

        $request = new Request();
        $request->setMethod('POST');
        $request->setBody([
            'name'    => '',
            'email'   => 'invalid',
            'message' => '',
            'subject' => '',
        ]);
        $request->headers->set('HX-Request', 'true');

        $response = $this->action->handle($request);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
