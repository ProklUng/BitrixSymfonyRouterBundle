<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use InvalidArgumentException;
use Prokl\BitrixSymfonyRouterBundle\Services\Router\InitRouter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 * Trait LoaderBundleRoutesTrait
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 *
 * @since 17.08.2021
 */
trait LoaderBundleRoutesTrait
{
    /**
     * Загрузить роуты в бандле.
     *
     * @param string $path   Путь к конфигу.
     * @param string $config Конфигурационный файл.
     *
     * @return void
     *
     * @throws InvalidArgumentException Нет класса-конфигуратора роутов.
     */
    private function loadRoutes(string $path, string $config = 'routes.yaml') : void
    {
        $routeLoader = new YamlFileLoader(
            new FileLocator($path)
        );

        $routes = $routeLoader->load($config);

        if (class_exists(InitRouter::class)) {
            InitRouter::addRoutesBundle($routes);
            return;
        }

        throw new InvalidArgumentException('Class InitRouter not exist.');
    }
}