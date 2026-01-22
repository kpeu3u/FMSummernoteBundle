<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\Tests\Twig\Extension;

use FM\SummernoteBundle\Twig\Extension\FMSummernoteExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class FMSummernoteTwigExtensionTest extends TestCase
{
    private array $parameters;

    protected function setUp(): void
    {
        $this->parameters = [
            'jquery_version' => 3,
            'width' => 600,
            'height' => 400,
            'include_jquery' => true,
            'include_bootstrap' => true,
            'include_fontawesome' => true,
            'fontawesome_path' => 'vendor/font-awesome.min.css',
            'bootstrap_css_path' => 'vendor/bootstrap.min.css',
            'bootstrap_js_path' => 'vendor/bootstrap.min.js',
            'jquery_path' => 'vendor/jquery.min.js',
            'summernote_css_path' => 'summernote.css',
            'summernote_js_path' => 'summernote.min.js',
            'init_template' => 'init.html.twig',
            'selector' => '.summernote',
            'language' => 'en-US',
            'plugins' => ['video'],
            'toolbar' => [
                'style' => ['style' => ['style']],
            ],
            'extra_toolbar' => [],
            'fontname' => ['Arial', 'Courier New'],
            'fontnocheck' => ['Arial'],
        ];
    }

    public function testSummernoteInit(): void
    {
        $loader = new ArrayLoader([
            'init.html.twig' => '{{ sn.selector }}',
        ]);
        $twig = new Environment($loader);
        $extension = new FMSummernoteExtension($this->parameters, $twig);

        $result = $extension->summernoteInit();

        $this->assertSame('.summernote', $result);
    }

    public function testSummernoteInitWithCustomToolbar(): void
    {
        $loader = new ArrayLoader([
            'init.html.twig' => '{{ sn.toolbar|raw }}',
        ]);
        $twig = new Environment($loader);
        $extension = new FMSummernoteExtension($this->parameters, $twig);

        $result = $extension->summernoteInit();

        $this->assertSame("[[ 'style', {\"style\":[\"style\"]}], ]", $result);
    }

    public function testSummernoteInitWithExtraToolbar(): void
    {
        $this->parameters['toolbar'] = [];
        $this->parameters['extra_toolbar'] = [
            'elfinder' => ['elfinder'],
        ];

        $loader = new ArrayLoader([
            'init.html.twig' => '{{ sn.toolbar|raw }}',
        ]);
        $twig = new Environment($loader);
        $extension = new FMSummernoteExtension($this->parameters, $twig);

        $result = $extension->summernoteInit();

        // Should include default toolbar because toolbar is empty
        $this->assertStringContainsString("'style', ['style']", $result);
        $this->assertStringContainsString("'elfinder', [\"elfinder\"]", $result);
    }
}
