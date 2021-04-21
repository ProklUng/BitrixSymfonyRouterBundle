<?php

namespace Prokl\BitrixSymfonyRouterBundle\DependencyInjection;

use Exception;
use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;

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
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerRouterConfiguration(
            $config,
            $container
        );
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'symfony_router';
    }

    /**
     * А-ля Симфоническая инициализация роутера.
     *
     * @param array            $config    Конфигурация.
     * @param ContainerBuilder $container Контейнер.
     *
     * @throws Exception
     * @return void
     *
     * @since 20.11.2020
     */
    private function registerRouterConfiguration(
        array $config,
        ContainerBuilder $container
    ): void {
        $annotationsConfigEnabled = $container->getParameter('enable_annotations');

        if ($config['default_uri'] === null) {
            $host = $container->getParameter('kernel.http.host');
            $schema = $container->getParameter('kernel.schema');
            $config['default_uri'] = $schema . '://' . $host . '/';
        }

        if (!$this->isConfigEnabled($container, $config)) {
            $container->removeDefinition('console.command.router_debug');
            $container->removeDefinition('console.command.router_match');
            return;
        }

        if (null === $config['utf8']) {
            trigger_deprecation(
                'symfony/framework-bundle',
                '5.2',
                'Not setting the "framework.router.utf8" configuration option is deprecated, 
                it will default to "true" in version 6.0.');
        }

        $container->setParameter('router.request.context.host', $config['router_request_context_host']);
        $container->setParameter('router.request.context.scheme', $config['router_request_context_scheme']);
        $container->setParameter('router.request.context.base_url', $config['router_request_context_base_url']);
        $container->setParameter('router.cache.path', $config['router_cache_path']);
        $container->setParameter('default_uri', $config['default_uri']);

        if ($config['utf8']) {
            $container->getDefinition('routing.loader')->replaceArgument(1, ['utf8' => true]);
        }

        if (!class_exists(ExpressionLanguage::class)) {
            $container->removeDefinition('router.expression_language_provider');
        }

        $container->setParameter('router.resource', $config['resource']);
        $router = $container->findDefinition('router.default');
        $argument = $router->getArgument(2);
        $argument['strict_requirements'] = $config['strict_requirements'];
        // @phpstan-ignore-next-line
        if (isset($config['type'])) {
            $argument['resource_type'] = $config['type'];
        }
        $router->replaceArgument(2, $argument);

        $container->setParameter('request_listener.http_port', $config['http_port']);
        $container->setParameter('request_listener.https_port', $config['https_port']);

        if ($annotationsConfigEnabled) {
            $container->register('routing.loader.annotation', AnnotatedRouteControllerLoader::class)
                ->setPublic(false)
                ->addTag('routing.loader', ['priority' => -10])
                ->addArgument(new Reference('annotation_reader'));

            $container->register('routing.loader.annotation.directory', AnnotationDirectoryLoader::class)
                ->setPublic(false)
                ->addTag('routing.loader', ['priority' => -10])
                ->setArguments([
                    new Reference('file_locator'),
                    new Reference('routing.loader.annotation'),
                ]);

            $container->register('routing.loader.annotation.file', AnnotationFileLoader::class)
                ->setPublic(false)
                ->addTag('routing.loader', ['priority' => -10])
                ->setArguments([
                    new Reference('file_locator'),
                    new Reference('routing.loader.annotation'),
                ]);
        }
    }
}
