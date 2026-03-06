<?php

declare(strict_types = 1);

namespace Tests\Infrastructure\Paths;

use Infrastructure\Paths\AppPaths;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\Paths\AppPaths
 *
 * @internal
 */
final class AppPathsTest extends TestCase
{
    private AppPaths $paths;

    protected function setUp(): void
    {
        $this->paths = AppPaths::instance();
    }

    public function testGetBasePath(): void
    {
        $basePath = $this->paths->getBasePath();

        self::assertNotEmpty($basePath);
        self::assertDirectoryExists($basePath);
    }

    public function testGetBasePathEndsWithProjectName(): void
    {
        $basePath = $this->paths->getBasePath();

        self::assertStringContainsString('nativa-php-boot', $basePath);
    }

    public function testGetBasePathIsAbsolute(): void
    {
        $basePath = $this->paths->getBasePath();

        self::assertMatchesRegularExpression('/^\//', $basePath);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = AppPaths::instance();
        $instance2 = AppPaths::instance();

        self::assertSame($instance1, $instance2);
    }

    public function testAppPathsIsSingleton(): void
    {
        $instance1 = AppPaths::instance();
        $instance2 = AppPaths::instance();

        self::assertSame($instance1->getBasePath(), $instance2->getBasePath());
    }
}
