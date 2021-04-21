<?php

namespace Prokl\BitrixSymfonyRouterBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SymfonyRouterExtension
 * @package Prokl\BitrixSymfonyRouterBundle\DependencyInjection
 *
 * @since 21.04.2021
 */
class SymfonyRouterExtension extends Extension
{
    private const DIR_CONFIG = '/../Resources/config';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container) : void
    {
        // $configuration = new Configuration();
        // $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        $loader->load('services.yaml');
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'symfony_router';
    }
}
