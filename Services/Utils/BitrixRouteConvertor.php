<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Routing\RoutingConfigurator;
use LogicException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class BitrixRouteConvertor
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 *
 * @since 23.07.2021
 */
class BitrixRouteConvertor
{
    use ContainerAwareTrait;

    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * BitrixRouteConvertor constructor.
     *
     * @param RouteCollection $routeCollection Коллекция роутов.
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    /**
     * Конвертировать все роуты в битриксовые.
     *
     * @param RoutingConfigurator $routes Битриксовые роуты.
     *
     * @return void
     */
    public function convertRoutes(RoutingConfigurator $routes) : void
    {
        $allRoutes = $this->routeCollection->all();

        foreach ($allRoutes as $name => $route) {
            $this->convertRoute($name, $route, $routes);
        }
    }

    /**
     * Конвертировать симфонический роут в битриксовый.
     *
     * @param string              $name   Название роута.
     * @param Route               $route  Роут.
     * @param RoutingConfigurator $routes Битриксовые роуты.
     *
     * @return void
     * @throws RuntimeException | LogicException Когда что-то не так с классами контроллера.
     *
     * @internal
     * Контроллеры __invoke не поддерживаются.
     * Контроллер должен наследоваться от Bitrix\Main\Engine\Controller.
     * Если не задан контейнер, то класс контроллера инстанцируется обычным образом.
     * В методе контроллера обязательно должно содержаться Action (битриксовое требование).
     */
    public function convertRoute(string $name, Route $route, RoutingConfigurator $routes) : void
    {
        $methods = [];
        foreach ((array)$route->getMethods() as $method) {
            $methods = array_merge($methods, explode('|', $method));
        }

        $path = $route->getPath();
        $controller = $this->parseControllerString($name, $route->getDefault('_controller'));

        if (!is_subclass_of($controller[0], Controller::class)) {
            throw new RuntimeException(
                sprintf('Controller %s must extend \Bitrix\Main\Engine\Controller class.', $controller[0])
            );
        }

        // Достаю из контейнера, только если он задан.
        if ($this->container !== null) {
            if (!$this->container->has($controller[0])) {
                throw new RuntimeException(
                    sprintf('Controller %s not registered as service', $controller[0])
                );
            }

            $service = $this->container->get($controller[0]);
        } else {
            if (!class_exists($controller[0])) {
                throw new RuntimeException(
                    sprintf('Class %s not exist.', $controller[0])
                );
            }
            $service = new $controller[0];
        }

        $processedRoute = $routes->any($path, [$service, $controller[1]]);

        $processedRoute = $processedRoute->methods($methods)
                                          ->name($name);

        foreach ($route->getRequirements() as $reqParam => $reqValue) {
            $processedRoute = $processedRoute->where($reqParam, $reqValue);
        }

        foreach ($route->getDefaults() as $defaultParam => $defaultValue) {
            if (stripos($defaultParam, '_') !== false) {
                continue;
            }
            $processedRoute = $processedRoute->default($defaultParam, $defaultValue);
        }
    }

    /**
     * Парсинг строки _controller.
     *
     * @param string       $name       Название роута.
     * @param array|string $controller Контроллер.
     *
     * @return array
     * @throws LogicException
     */
    private function parseControllerString(string $name, $controller) : array
    {
        $argument = $controller;
        if (is_string($controller)) {
            if (strpos($controller, '::') !== false) {
                $controller = explode('::', $controller, 2);
                if (strpos($controller[1], 'Action') === false) {
                    // В методе контроллера обязательно должно содержаться Action
                    // (особенность битриксовых контроллеров)
                    throw new LogicException(
                        sprintf(
                            'Route %s. Action %s name of controller must contain Action.',
                            $name,
                            $argument
                        )
                    );
                }

                return $controller;
            } else {
                // Invoked controller (не поддерживается).
                throw new LogicException(
                    sprintf('Route %s. Invokable controller %s not supporting.', $name, $controller)
                );
            }
        }

        throw new LogicException(
            sprintf('Route %s. Invalid _controller param.', $name)
        );
    }
}