<?php

declare(strict_types = 1);

namespace Tests\Infrastructure\Storage\Providers;

use Infrastructure\Storage\Providers\LocalStorageProvider;
use Infrastructure\Storage\Providers\MediaProviderInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Infrastructure\Storage\Providers\LocalStorageProvider
 *
 * @internal
 */
final class LocalStorageProviderTest extends TestCase
{
    private LocalStorageProvider $provider;

    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/storage_test_' . uniqid();
        @mkdir($this->testDir, 0o755, true);

        $this->provider = new LocalStorageProvider($this->testDir, '/storage/uploads');
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->deleteDirectory($this->testDir);
        }
    }

    public function testProviderImplementsInterface(): void
    {
        self::assertInstanceOf(
            MediaProviderInterface::class,
            $this->provider
        );
    }

    public function testGetName(): void
    {
        $name = $this->provider->getName();

        self::assertSame('local', $name);
    }

    public function testConstructorCreatesBaseDirectory(): void
    {
        self::assertDirectoryExists($this->testDir);
    }

    public function testExistsReturnsFalseForNonExistentFile(): void
    {
        $exists = $this->provider->exists('nonexistent.txt');

        self::assertFalse($exists);
    }

    public function testExistsReturnsTrueForExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        $exists = $this->provider->exists('test.txt');

        self::assertTrue($exists);
    }

    public function testGetSizeForExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        $content = 'test content';
        file_put_contents($testFile, $content);

        $size = $this->provider->getSize('test.txt');

        self::assertSame(\strlen($content), $size);
    }

    public function testGetSizeForNonExistentFile(): void
    {
        $size = $this->provider->getSize('nonexistent.txt');

        self::assertSame(0, $size);
    }

    public function testGetUrl(): void
    {
        $url = $this->provider->getUrl('test/file.txt');

        self::assertSame('/storage/uploads/test/file.txt', $url);
    }

    public function testGetUrlWithLeadingSlash(): void
    {
        $url = $this->provider->getUrl('/test/file.txt');

        self::assertSame('/storage/uploads/test/file.txt', $url);
    }

    public function testDeleteNonExistentFile(): void
    {
        $deleted = $this->provider->delete('nonexistent.txt');

        self::assertFalse($deleted);
    }

    public function testDeleteExistentFile(): void
    {
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        $deleted = $this->provider->delete('test.txt');

        self::assertTrue($deleted);
        self::assertFileDoesNotExist($testFile);
    }

    public function testUploadWithInvalidFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => '',
            'name'     => 'test.txt',
        ]);
    }

    public function testUploadWithNonExistentTempFile(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => '/nonexistent/file.txt',
            'name'     => 'test.txt',
            'size'     => 100,
        ]);
    }

    public function testProviderIsFinalClass(): void
    {
        $reflection = new \ReflectionClass(LocalStorageProvider::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testUploadValidatesFileSize(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, str_repeat('a', 11 * 1024 * 1024)); // 11MB

        $this->expectException(\RuntimeException::class);

        $this->provider->upload([
            'tmp_name' => $tempFile,
            'name'     => 'large.txt',
            'size'     => 11 * 1024 * 1024,
            'type'     => 'text/plain',
            'error'    => UPLOAD_ERR_OK,
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
            'name'     => 'malicious.php',
            'size'     => 100,
            'type'     => 'application/x-php',
            'error'    => UPLOAD_ERR_OK,
        ]);

        unlink($tempFile);
    }

    public function testUploadCreatesDateBasedDirectory(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        // Create a minimal PNG file (1x1 pixel)
        file_put_contents($tempFile, hex2bin('89504E470D0A1A0A0000000D4948445200000001000000010802000000907753DE0000000C49444154789CCF63000100000100010018DD8D290000000049454E44AE426082'));

        $result = $this->provider->upload([
            'tmp_name' => $tempFile,
            'name'     => 'test.png',
            'size'     => 100,
            'type'     => 'image/png',
            'error'    => UPLOAD_ERR_OK,
        ]);

        unlink($tempFile);

        self::assertArrayHasKey('path', $result);
        self::assertArrayHasKey('url', $result);
        self::assertStringContainsString(date('Y/m/d'), $result['path']);
    }

    public function testUploadReturnsCorrectData(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        // Create a minimal PNG file (1x1 pixel)
        file_put_contents($tempFile, hex2bin('89504E470D0A1A0A0000000D4948445200000001000000010802000000907753DE0000000C49444154789CCF63000100000100010018DD8D290000000049454E44AE426082'));

        $result = $this->provider->upload([
            'tmp_name' => $tempFile,
            'name'     => 'test.png',
            'size'     => 100,
            'type'     => 'image/png',
            'error'    => UPLOAD_ERR_OK,
        ]);

        unlink($tempFile);

        self::assertArrayHasKey('path', $result);
        self::assertArrayHasKey('url', $result);
        self::assertArrayHasKey('size', $result);
        self::assertArrayHasKey('mime_type', $result);
        self::assertArrayHasKey('original_name', $result);
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
