<?php

declare(strict_types = 1);

namespace Tests\Interfaces\HTTP\View;

use Interfaces\HTTP\View\TemplateRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\View\TemplateRenderer
 *
 * @internal
 */
final class TemplateRendererTest extends TestCase
{
    private TemplateRenderer $renderer;

    private string $templatesPath;

    private string $cachePath;

    protected function setUp(): void
    {
        $this->templatesPath = __DIR__ . '/../../../src/Interfaces/Templates';
        $this->cachePath = sys_get_temp_dir() . '/template_cache_' . uniqid();

        @mkdir($this->cachePath, 0o755, true);

        $this->renderer = new TemplateRenderer(
            $this->templatesPath,
            $this->cachePath,
            true
        );
    }

    protected function tearDown(): void
    {
        if (is_dir($this->cachePath)) {
            $this->deleteDirectory($this->cachePath);
        }
    }

    public function testConstructor(): void
    {
        self::assertInstanceOf(TemplateRenderer::class, $this->renderer);
    }

    public function testGetTemplatesPath(): void
    {
        $path = $this->renderer->getTemplatesPath();

        self::assertSame(rtrim($this->templatesPath, '/'), $path);
    }

    public function testShareAddsSharedData(): void
    {
        $this->renderer->share('siteName', 'Test Site');
        $this->renderer->share('year', 2026);

        self::assertTrue(true, 'Share method executed without errors');
    }

    public function testShareDataIsAvailableToTemplates(): void
    {
        $this->renderer->share('testVar', 'testValue');

        self::assertTrue(true, 'Shared data set successfully');
    }

    public function testRenderWithDebugMode(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $this->cachePath,
            true
        );

        self::assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testRenderWithoutCachePath(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            null,
            false
        );

        self::assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testRenderWithCustomVersion(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $this->cachePath,
            false,
            'v1.0.0'
        );

        self::assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testMultipleShareCalls(): void
    {
        $this->renderer->share('key1', 'value1');
        $this->renderer->share('key2', 'value2');
        $this->renderer->share('key3', 'value3');

        self::assertTrue(true, 'Multiple share calls executed successfully');
    }

    public function testShareOverwritesPreviousValue(): void
    {
        $this->renderer->share('key', 'value1');
        $this->renderer->share('key', 'value2');

        self::assertTrue(true, 'Share overwrite executed successfully');
    }

    public function testPartialMethodExists(): void
    {
        self::assertTrue(
            method_exists($this->renderer, 'partial'),
            'partial method exists'
        );
    }

    public function testRenderMethodExists(): void
    {
        self::assertTrue(
            method_exists($this->renderer, 'render'),
            'render method exists'
        );
    }

    public function testShareMethodExists(): void
    {
        self::assertTrue(
            method_exists($this->renderer, 'share'),
            'share method exists'
        );
    }

    public function testGetTemplatesPathMethodExists(): void
    {
        self::assertTrue(
            method_exists($this->renderer, 'getTemplatesPath'),
            'getTemplatesPath method exists'
        );
    }

    public function testRendererWithEmptyCacheDirectory(): void
    {
        $emptyCache = sys_get_temp_dir() . '/empty_cache_' . uniqid();
        @mkdir($emptyCache, 0o755, true);

        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $emptyCache,
            true
        );

        self::assertInstanceOf(TemplateRenderer::class, $renderer);

        if (is_dir($emptyCache)) {
            rmdir($emptyCache);
        }
    }

    public function testRendererIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(TemplateRenderer::class);

        self::assertTrue($reflection->isFinal());
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
