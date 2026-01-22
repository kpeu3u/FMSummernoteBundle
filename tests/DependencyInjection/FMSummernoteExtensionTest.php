<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\Tests\DependencyInjection;

use FM\SummernoteBundle\DependencyInjection\FMSummernoteExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class FMSummernoteExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new FMSummernoteExtension(),
        ];
    }

    #[Test]
    public function testYamlConfiguration(): void
    {
        $yamlFile = __DIR__.'/../../src/Resources/config/fm_summernote.yaml';
        $this->assertFileExists($yamlFile);

        $config = Yaml::parseFile($yamlFile);
        $this->container = new ContainerBuilder(); // Fresh container to avoid double merging from getMinimalConfiguration
        $loader = new FMSummernoteExtension();
        $loader->load($config, $this->container);

        $processedConfig = $this->container->getParameter('fm_summernote');

        $this->assertSame('.summernote', $processedConfig['selector']);
        $this->assertSame(600, $processedConfig['width']);
        $this->assertSame(400, $processedConfig['height']);
        $this->assertSame(['video', 'elfinder'], $processedConfig['plugins']);

        $this->assertArrayHasKey('style', $processedConfig['toolbar']);
        $this->assertSame(['style' => ['style']], $processedConfig['toolbar']['style']);

        $this->assertArrayHasKey('elfinder', $processedConfig['extra_toolbar']);
        $this->assertSame(['elfinder' => ['elfinder']], $processedConfig['extra_toolbar']['elfinder']);
    }

    #[Test]
    public function testServices(): void
    {
        $this->load();
        $this->assertContainerBuilderHasService('twig.extension.fm_summernote');
    }

    #[Test]
    public function testMinimumConfiguration(): void
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('fm_summernote');
        $config = $this->container->getParameter('fm_summernote');
        $this->assertSame('.summernote', $config['selector']);
    }

    #[Test]
    public function testFullConfiguration(): void
    {
        $this->container = new ContainerBuilder();
        $loader = new FMSummernoteExtension();
        $loader->load([[
            'plugins' => ['video', 'elfinder'],
            'selector' => '.my-summernote',
            'width' => 800,
            'height' => 600,
            'toolbar' => [
                'style' => ['style' => ['style']],
                'font' => ['bold' => ['bold']],
            ],
            'extra_toolbar' => [
                'elfinder' => ['elfinder' => ['elfinder']],
            ],
            'fontname' => ['Arial', 'Verdana'],
            'fontnocheck' => ['Arial'],
        ]], $this->container);

        $this->assertTrue($this->container->hasParameter('fm_summernote'));
        $config = $this->container->getParameter('fm_summernote');

        $this->assertSame('.my-summernote', $config['selector']);
        $this->assertSame(800, $config['width']);
        $this->assertSame(600, $config['height']);
        $this->assertSame(['video', 'elfinder'], $config['plugins']);
        $this->assertSame(['style' => ['style' => ['style']], 'font' => ['bold' => ['bold']]], $config['toolbar']);
        $this->assertSame(['elfinder' => ['elfinder' => ['elfinder']]], $config['extra_toolbar']);
        $this->assertSame(['Arial', 'Verdana'], $config['fontname']);
        $this->assertSame(['Arial'], $config['fontnocheck']);
    }

    protected function getMinimalConfiguration(): array
    {
        $yaml = <<<'EOF'
plugins:
    - video
    - elfinder # by default plugins not set, bundle comes with elfinder plugin / provides integration with FMElfinderBundle
selector: .summernote #defines summernote selector for apply to
toolbar: # define toolbars, if no toolbar configured, default toolbars defined
    style: [style]
    bold: [bold]
extra_toolbar: # extra toolbar can be used for plugins toolbar and as additional toolbar setings, when 'toolbar' option is omitted
    elfinder: [elfinder]
width: 600
height: 400
language: '' # define language (with language culture code like de-DE, fr-FR, etc.) by default, it is in english
include_jquery: true #include js libraries, if your template already have them, set to false
include_bootstrap: true
include_fontawesome: true
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
