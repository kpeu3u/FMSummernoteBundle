<?php

declare(strict_types=1);

namespace FM\SummernoteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FMSummernoteExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('fm_summernote', $config);
    }

    public function getAlias(): string
    {
        return 'fm_summernote';
    }

    public function getNamespace(): string
    {
        return 'http://helios-ag.github.io/schema/dic/fm_summernote';
    }
}
