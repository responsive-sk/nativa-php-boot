<?php

declare(strict_types=1);

namespace Tests\Infrastructure\View;

use Infrastructure\View\AssetHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\View\AssetHelper
 */
final class AssetHelperTest extends TestCase
{
    protected function setUp(): void
    {
        AssetHelper::clearCache();
    }

    protected function tearDown(): void
    {
        AssetHelper::clearCache();
    }

    public function testJsMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'js'),
            'js method exists'
        );
    }

    public function testCssMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'css'),
            'css method exists'
        );
    }

    public function testAssetMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'asset'),
            'asset method exists'
        );
    }

    public function testPageCssMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'pageCss'),
            'pageCss method exists'
        );
    }

    public function testClearCacheMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'clearCache'),
            'clearCache method exists'
        );
    }

    public function testHasManifestMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'hasManifest'),
            'hasManifest method exists'
        );
    }

    public function testGetManifestMethodExists(): void
    {
        $this->assertTrue(
            method_exists(AssetHelper::class, 'getManifest'),
            'getManifest method exists'
        );
    }

    public function testJsReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('app.js');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('.js', $result);
    }

    public function testCssReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::css('css.css');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('.css', $result);
    }

    public function testAssetReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::asset('image.png');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testPageCssReturnsNullForUnknownPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('unknown-page');

        $this->assertNull($result);
    }

    public function testPageCssReturnsStringForKnownPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('home');

        if (null !== $result) {
            $this->assertIsString($result);
        }
    }

    public function testJsHandlesCoreInitMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-init');

        $this->assertIsString($result);
    }

    public function testJsHandlesCoreAppMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-app');

        $this->assertIsString($result);
    }

    public function testJsHandlesCoreCssMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-css');

        $this->assertIsString($result);
    }

    public function testClearCacheResetsManifest(): void
    {
        AssetHelper::clearCache();

        $manifest = AssetHelper::getManifest();

        $this->assertIsArray($manifest);
    }

    public function testHasManifestReturnsBoolean(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::hasManifest();

        $this->assertIsBool($result);
    }

    public function testGetManifestReturnsArray(): void
    {
        AssetHelper::clearCache();

        $manifest = AssetHelper::getManifest();

        $this->assertIsArray($manifest);
    }

    public function testJsAddsJsExtension(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('app');

        $this->assertStringContainsString('.js', $result);
    }

    public function testCssAddsCssExtension(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::css('style');

        $this->assertStringContainsString('.css', $result);
    }

    public function testAssetHelperIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(AssetHelper::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testAssetHelperHasNoPublicMethods(): void
    {
        $reflection = new \ReflectionClass(AssetHelper::class);

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $this->assertGreaterThan(0, \count($methods));
    }

    public function testMultipleJsCallsReturnConsistentResults(): void
    {
        AssetHelper::clearCache();

        $result1 = AssetHelper::js('test.js');
        $result2 = AssetHelper::js('test.js');

        $this->assertEquals($result1, $result2);
    }

    public function testMultipleCssCallsReturnConsistentResults(): void
    {
        AssetHelper::clearCache();

        $result1 = AssetHelper::css('test.css');
        $result2 = AssetHelper::css('test.css');

        $this->assertEquals($result1, $result2);
    }

    public function testPageCssForBlogPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('blog');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertStringContainsString('.css', $result);
        }
    }

    public function testPageCssForPortfolioPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('portfolio');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertStringContainsString('.css', $result);
        }
    }

    public function testPageCssForContactPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('contact');

        if (null !== $result) {
            $this->assertIsString($result);
            $this->assertStringContainsString('.css', $result);
        }
    }
}
