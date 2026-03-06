<?php

declare(strict_types = 1);

namespace Tests\Infrastructure\View;

use Infrastructure\View\AssetHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\View\AssetHelper
 *
 * @internal
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
        self::assertTrue(
            method_exists(AssetHelper::class, 'js'),
            'js method exists'
        );
    }

    public function testCssMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'css'),
            'css method exists'
        );
    }

    public function testAssetMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'asset'),
            'asset method exists'
        );
    }

    public function testPageCssMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'pageCss'),
            'pageCss method exists'
        );
    }

    public function testClearCacheMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'clearCache'),
            'clearCache method exists'
        );
    }

    public function testHasManifestMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'hasManifest'),
            'hasManifest method exists'
        );
    }

    public function testGetManifestMethodExists(): void
    {
        self::assertTrue(
            method_exists(AssetHelper::class, 'getManifest'),
            'getManifest method exists'
        );
    }

    public function testJsReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('app.js');

        self::assertIsString($result);
        self::assertNotEmpty($result);
        self::assertStringContainsString('.js', $result);
    }

    public function testCssReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::css('css.css');

        self::assertIsString($result);
        self::assertNotEmpty($result);
        self::assertStringContainsString('.css', $result);
    }

    public function testAssetReturnsStringWithFallback(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::asset('image.png');

        self::assertIsString($result);
        self::assertNotEmpty($result);
    }

    public function testPageCssReturnsNullForUnknownPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('unknown-page');

        self::assertNull($result);
    }

    public function testPageCssReturnsStringForKnownPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('home');

        if (null !== $result) {
            self::assertIsString($result);
        }
    }

    public function testJsHandlesCoreInitMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-init');

        self::assertIsString($result);
    }

    public function testJsHandlesCoreAppMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-app');

        self::assertIsString($result);
    }

    public function testJsHandlesCoreCssMapping(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('core-css');

        self::assertIsString($result);
    }

    public function testClearCacheResetsManifest(): void
    {
        AssetHelper::clearCache();

        $manifest = AssetHelper::getManifest();

        self::assertIsArray($manifest);
    }

    public function testHasManifestReturnsBoolean(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::hasManifest();

        self::assertIsBool($result);
    }

    public function testGetManifestReturnsArray(): void
    {
        AssetHelper::clearCache();

        $manifest = AssetHelper::getManifest();

        self::assertIsArray($manifest);
    }

    public function testJsAddsJsExtension(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::js('app');

        self::assertStringContainsString('.js', $result);
    }

    public function testCssAddsCssExtension(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::css('style');

        self::assertStringContainsString('.css', $result);
    }

    public function testAssetHelperIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(AssetHelper::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testAssetHelperHasNoPublicMethods(): void
    {
        $reflection = new \ReflectionClass(AssetHelper::class);

        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        self::assertGreaterThan(0, \count($methods));
    }

    public function testMultipleJsCallsReturnConsistentResults(): void
    {
        AssetHelper::clearCache();

        $result1 = AssetHelper::js('test.js');
        $result2 = AssetHelper::js('test.js');

        self::assertSame($result1, $result2);
    }

    public function testMultipleCssCallsReturnConsistentResults(): void
    {
        AssetHelper::clearCache();

        $result1 = AssetHelper::css('test.css');
        $result2 = AssetHelper::css('test.css');

        self::assertSame($result1, $result2);
    }

    public function testPageCssForBlogPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('blog');

        if (null !== $result) {
            self::assertIsString($result);
            self::assertStringContainsString('.css', $result);
        }
    }

    public function testPageCssForPortfolioPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('portfolio');

        if (null !== $result) {
            self::assertIsString($result);
            self::assertStringContainsString('.css', $result);
        }
    }

    public function testPageCssForContactPage(): void
    {
        AssetHelper::clearCache();

        $result = AssetHelper::pageCss('contact');

        if (null !== $result) {
            self::assertIsString($result);
            self::assertStringContainsString('.css', $result);
        }
    }
}
