<?php

declare(strict_types=1);

namespace Tests\Interfaces\HTTP\Actions\Frontend;

use Application\Services\ArticleManager;
use Domain\Model\Article;
use Infrastructure\Http\Request;
use Interfaces\HTTP\Actions\Frontend\HomeAction;
use Interfaces\HTTP\View\TemplateRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\Actions\Frontend\HomeAction
 */
final class HomeActionTest extends TestCase
{
    private ArticleManager&MockObject $articleManager;

    private TemplateRenderer&MockObject $renderer;

    private HomeAction $action;

    protected function setUp(): void
    {
        $this->articleManager = $this->createMock(ArticleManager::class);
        $this->renderer = $this->createMock(TemplateRenderer::class);
        $this->action = new HomeAction($this->articleManager, $this->renderer);
    }

    public function testHandleRendersHomepageWithArticles(): void
    {
        $articles = [
            $this->createMock(Article::class),
            $this->createMock(Article::class),
        ];

        $this->articleManager
            ->expects($this->once())
            ->method('listLatest')
            ->with(10)
            ->willReturn($articles);

        $expectedContent = '<html>Homepage Content</html>';

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                'pages/frontend/home',
                [
                    'articles'  => $articles,
                    'pageTitle' => 'Nativa CMS - Modern PHP Blog Platform',
                    'page'      => 'home',
                ],
                'frontend'
            )
            ->willReturn($expectedContent);

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedContent, $response->getBody()->getContents());
    }

    public function testHandleRendersWithEmptyArticles(): void
    {
        $this->articleManager
            ->expects($this->once())
            ->method('listLatest')
            ->with(10)
            ->willReturn([]);

        $expectedContent = '<html>Homepage with no articles</html>';

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->willReturn($expectedContent);

        $request = new Request();
        $response = $this->action->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
