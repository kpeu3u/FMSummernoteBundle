<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\Tests\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\RootPackageInterface;
use Composer\Script\Event;
use FM\SummernoteBundle\Composer\SummernoteScriptHandler;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class SummernoteScriptHandlerTest extends TestCase
{
    public function testGetOptions(): void
    {
        $event = $this->createMock(Event::class);
        $composer = $this->createMock(Composer::class);
        $package = $this->createMock(RootPackageInterface::class);
        $io = $this->createMock(IOInterface::class);

        $event->method('getComposer')->willReturn($composer);
        $event->method('getIO')->willReturn($io);
        $composer->method('getPackage')->willReturn($package);
        $package->method('getExtra')->willReturn([
            'fm-summernote' => [
                'path' => 'web/vendor/summernote',
                'version' => 'v0.8.18',
            ],
        ]);

        $reflection = new \ReflectionClass(SummernoteScriptHandler::class);
        $method = $reflection->getMethod('getOptions');
        $method->setAccessible(true);

        $options = $method->invoke(null, $event);

        $this->assertArrayHasKey('path', $options);
        $this->assertSame('web/vendor/summernote', $options['path']);
        $this->assertSame('v0.8.18', $options['version']);
        $this->assertIsCallable($options['notifier']);
    }
}
