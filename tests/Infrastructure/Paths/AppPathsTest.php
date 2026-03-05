<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Paths;

use Infrastructure\Paths\AppPaths;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\Paths\AppPaths
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

        $this->assertNotEmpty($basePath);
        $this->assertDirectoryExists($basePath);
    }

    public function testGetBasePathEndsWithProjectName(): void
    {
        $basePath = $this->paths->getBasePath();

        $this->assertStringContainsString('nativa-php-boot', $basePath);
    }

    public function testGetBasePathIsAbsolute(): void
    {
        $basePath = $this->paths->getBasePath();

        $this->assertMatchesRegularExpression('/^\//', $basePath);
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = AppPaths::instance();
        $instance2 = AppPaths::instance();

        $this->assertSame($instance1, $instance2);
    }

    public function testAppPathsIsSingleton(): void
    {
        $instance1 = AppPaths::instance();
        $instance2 = AppPaths::instance();

        $this->assertEquals($instance1->getBasePath(), $instance2->getBasePath());
    }
}
