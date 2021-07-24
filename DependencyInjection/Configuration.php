<?php

namespace Prokl\BitrixSymfonyRouterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Prokl\BitrixSymfonyRouterBundle\DependencyInjection
 *
 * @since 21.04.2021
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfony_router');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->booleanNode('enabled')->defaultValue(true)->end()
            ->scalarNode('router_request_context_host')->defaultValue('%kernel.site.host%')->end()
            ->scalarNode('router_request_context_scheme')->defaultValue('http')->end()
            ->scalarNode('router_request_context_base_url')->defaultValue('')->end()
            ->scalarNode('router_cache_path')->defaultValue('%kernel.cache_dir%/routes')->end()
            ->scalarNode('router_caching_routes')->defaultValue(false)->end()
            ->booleanNode('router_check_exists_controller')->defaultValue(false)->end()
            ->arrayNode('controller_annotations_path')
            ->useAttributeAsKey('name')
            ->prototype('scalar')->end()
            ->defaultValue([])
            ->end()
            ->scalarNode('router_config_file')->defaultValue('local/configs/routes.yaml')->end()
            ->scalarNode('http_port')->defaultValue(80)->end()
            ->scalarNode('https_port')->defaultValue(443)->end()
            ->scalarNode('resource')->defaultValue('%kernel.project_dir%/app/routes.yaml')->end()
            ->booleanNode('utf8')->defaultValue(true)->end()
            ->end();

        return $treeBuilder;
    }
}