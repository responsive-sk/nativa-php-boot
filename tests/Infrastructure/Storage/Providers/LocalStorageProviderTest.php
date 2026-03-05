<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Storage\Providers;

use Infrastructure\Storage\Providers\LocalStorageProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\Storage\Providers\LocalStorageProvider
 */
final class LocalStorageProviderTest extends TestCase
{
    private LocalStorageProvider $provider;

    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/storage_test_' . uniqid();
        @mkdir($this->testDir, 0755, true);

        $this->provider = new LocalStorageProvider($this->testDir, '/storage/uploads');
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->deleteDirectory($this->testDir);
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

    public function testProviderImplementsInterface(): void
    {
        $this->assertInstanceOf(
            \Infrastructure\Storage\Providers\MediaProviderInterface::class,
            $this->provider
        );
    }

    public function testGetName(): void
    {
        $name = $this->provider->getName();

        $this->assertEquals('local', $name);
    }

    public function testConstructorCreatesBaseDirectory(): void
    {
        $this->assertDirectoryExists($this->testDir);
    }

    public function testExistsReturnsFalseForNonExistentFile(): void
    {
        $exists = $this->provider->exists('nonexistent.txt');

        $this->assertFalse($exists);
    }

    public function testExistsReturnsTrueForExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        $exists = $this->provider->exists('test.txt');

        $this->assertTrue($exists);
    }

    public function testGetSizeForExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        $content = 'test content';
        file_put_contents($testFile, $content);

        $size = $this->provider->getSize('test.txt');

        $this->assertEquals(\strlen($content), $size);
    }

    public function testGetSizeForNonExistentFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->provider->getSize('nonexistent.txt');
    }

    public function testGetUrl(): void
    {
        $url = $this->provider->getUrl('test/file.txt');

        $this->assertEquals('/storage/uploads/test/file.txt', $url);
    }

    public function testGetUrlWithLeadingSlash(): void
    {
        $url = $this->provider->getUrl('/test/file.txt');

        $this->assertEquals('/storage/uploads/test/file.txt', $url);
    }

    public function testDeleteNonExistentFile(): void
    {
        $deleted = $this->provider->delete('nonexistent.txt');

        $this->assertFalse($deleted);
    }

    public function testDeleteExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        $deleted = $this->provider->delete('test.txt');

        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist($testFile);
    }

    public function testUploadWithInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => '',
            'name' => 'test.txt',
        ]);
    }

    public function testUploadWithNonExistentTempFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => '/nonexistent/file.txt',
            'name' => 'test.txt',
            'size' => 100,
        ]);
    }

    public function testProviderIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(LocalStorageProvider::class);

        $this->assertTrue($reflection->isFinal());
    }

    public function testUploadValidatesFileSize(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, str_repeat('a', 11 * 1024 * 1024)); // 11MB

        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => $tempFile,
            'name' => 'large.txt',
            'size' => 11 * 1024 * 1024,
            'type' => 'text/plain',
        ]);

        unlink($tempFile);
    }

    public function testUploadValidatesMimeType(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, '<?php echo "malicious"; ?>');

        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => $tempFile,
            'name' => 'malicious.php',
            'size' => 100,
            'type' => 'application/x-php',
        ]);

        unlink($tempFile);
    }

    public function testUploadCreatesDateBasedDirectory(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');

        $result = $this->provider->upload([
            'tmp_name' => $tempFile,
            'name' => 'test.txt',
            'size' => 100,
            'type' => 'text/plain',
        ]);

        unlink($tempFile);

        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertStringContainsString(date('Y/m/d'), $result['path']);
    }

    public function testUploadReturnsCorrectData(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');

        $result = $this->provider->upload([
            'tmp_name' => $tempFile,
            'name' => 'test.txt',
            'size' => 100,
            'type' => 'text/plain',
        ]);

        unlink($tempFile);

        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertArrayHasKey('original_name', $result);
    }
}
