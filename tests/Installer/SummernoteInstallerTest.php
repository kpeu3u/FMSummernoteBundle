<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\Tests\Installer;

use FM\SummernoteBundle\Installer\SummernoteInstaller;
use PHPUnit\Framework\TestCase;

class SummernoteInstallerTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/summernote_test_'.uniqid();
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->tempDir);
    }

    private function removeDir(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($dir);
    }

    public function testConstructorWithDefaultOptions(): void
    {
        $installer = new SummernoteInstaller();
        $this->assertInstanceOf(SummernoteInstaller::class, $installer);
    }

    public function testConstructorWithCustomOptions(): void
    {
        $options = [
            'version' => 'v0.8.18',
            'path' => $this->tempDir,
            'excludes' => ['test'],
        ];
        $installer = new SummernoteInstaller($options);
        $this->assertInstanceOf(SummernoteInstaller::class, $installer);
    }

    public function testClearReturnsDropIfFileDoesNotExist(): void
    {
        $options = ['path' => $this->tempDir];
        $installer = new SummernoteInstaller($options);

        // clear is private, but install() calls it.
        // If we can't easily test private methods, we might need to reflect or test through public API.
        // Let's use reflection for this purpose if we want to unit test.

        $reflection = new \ReflectionClass(SummernoteInstaller::class);
        $method = $reflection->getMethod('clear');
        $method->setAccessible(true);

        $result = $method->invoke($installer, ['path' => $this->tempDir, 'clear' => null, 'notifier' => null]);
        $this->assertSame(SummernoteInstaller::CLEAR_DROP, $result);
    }

    public function testClearReturnsSkipIfFileExistsAndNoNotifier(): void
    {
        $options = ['path' => $this->tempDir];
        file_put_contents($this->tempDir.'/summernote.js', 'test');
        $installer = new SummernoteInstaller($options);

        $reflection = new \ReflectionClass(SummernoteInstaller::class);
        $method = $reflection->getMethod('clear');
        $method->setAccessible(true);

        $result = $method->invoke($installer, ['path' => $this->tempDir, 'clear' => null, 'notifier' => null]);
        $this->assertSame(SummernoteInstaller::CLEAR_SKIP, $result);
    }
}
