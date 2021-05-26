<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use LogicException;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteCheckerExist
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 */
class RouteCheckerExist
{
    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * RouteCheckerExist constructor.
     *
     * @param RouteCollection $routeCollection Коллекция роутов.
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    /**
     * Проверка роутов на существование контроллеров.
     *
     * @return void
     * @throws LogicException Когда контроллер не существует.
     */
    public function check() : void
    {
        $allRoutes = $this->routeCollection->all();
        foreach ($allRoutes as $nameRoute => $route) {
            $controller = $route->getDefault('_controller');

            $method = '__invoke';
            if (is_string($controller)) {
                if (strpos($controller, '::') !== false) {
                    $callback = explode('::', $controller, 2);
                    $class = (string)$callback[0];
                    $method = (string)$callback[1];
                } else {
                    // __invoke
                    $class = $controller;
                }

                if (!class_exists($class)) {
                    throw new LogicException(
                        sprintf(
                            'Class %s declaring as controller for route %s not exists.',
                            $class,
                            $nameRoute
                        )
                    );
                }

                if (!method_exists($class, $method)) {
                    throw new LogicException(
                        sprintf(
                            'Class %s declaring as controller for route %s dont have method %s.',
                            $class,
                            $nameRoute,
                            $method
                        )
                    );
                }
            }
        }
    }
}