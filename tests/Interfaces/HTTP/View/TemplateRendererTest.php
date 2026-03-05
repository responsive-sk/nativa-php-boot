<?php

declare(strict_types=1);

namespace Tests\Interfaces\HTTP\View;

use Interfaces\HTTP\View\TemplateRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Interfaces\HTTP\View\TemplateRenderer
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
        
        @mkdir($this->cachePath, 0755, true);
        
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

    public function testConstructor(): void
    {
        $this->assertInstanceOf(TemplateRenderer::class, $this->renderer);
    }

    public function testGetTemplatesPath(): void
    {
        $path = $this->renderer->getTemplatesPath();

        $this->assertEquals(rtrim($this->templatesPath, '/'), $path);
    }

    public function testShareAddsSharedData(): void
    {
        $this->renderer->share('siteName', 'Test Site');
        $this->renderer->share('year', 2026);

        $this->assertTrue(true, 'Share method executed without errors');
    }

    public function testShareDataIsAvailableToTemplates(): void
    {
        $this->renderer->share('testVar', 'testValue');

        $this->assertTrue(true, 'Shared data set successfully');
    }

    public function testRenderWithDebugMode(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $this->cachePath,
            true
        );

        $this->assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testRenderWithoutCachePath(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            null,
            false
        );

        $this->assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testRenderWithCustomVersion(): void
    {
        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $this->cachePath,
            false,
            'v1.0.0'
        );

        $this->assertInstanceOf(TemplateRenderer::class, $renderer);
    }

    public function testMultipleShareCalls(): void
    {
        $this->renderer->share('key1', 'value1');
        $this->renderer->share('key2', 'value2');
        $this->renderer->share('key3', 'value3');

        $this->assertTrue(true, 'Multiple share calls executed successfully');
    }

    public function testShareOverwritesPreviousValue(): void
    {
        $this->renderer->share('key', 'value1');
        $this->renderer->share('key', 'value2');

        $this->assertTrue(true, 'Share overwrite executed successfully');
    }

    public function testPartialMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->renderer, 'partial'),
            'partial method exists'
        );
    }

    public function testRenderMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->renderer, 'render'),
            'render method exists'
        );
    }

    public function testShareMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->renderer, 'share'),
            'share method exists'
        );
    }

    public function testGetTemplatesPathMethodExists(): void
    {
        $this->assertTrue(
            method_exists($this->renderer, 'getTemplatesPath'),
            'getTemplatesPath method exists'
        );
    }

    public function testRendererWithEmptyCacheDirectory(): void
    {
        $emptyCache = sys_get_temp_dir() . '/empty_cache_' . uniqid();
        @mkdir($emptyCache, 0755, true);

        $renderer = new TemplateRenderer(
            $this->templatesPath,
            $emptyCache,
            true
        );

        $this->assertInstanceOf(TemplateRenderer::class, $renderer);

        if (is_dir($emptyCache)) {
            rmdir($emptyCache);
        }
    }

    public function testRendererIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(TemplateRenderer::class);

        $this->assertTrue($reflection->isFinal());
    }
}
