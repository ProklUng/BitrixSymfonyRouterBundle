<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteChecker
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 *
 * @since 11.10.2020
 */
class RouteChecker
{
    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * @var Request $request Глобальный Request.
     */
    private $request;

    /**
     * @var RequestContext $requestContext RequestContext.
     */
    private $requestContext;

    /**
     * Routing constructor.
     *
     * @param RouteCollection $routeCollection Коллекция роутов.
     * @param Request         $request         Request.
     * @param RequestContext  $requestContext  Request context.
     */
    public function __construct(
        RouteCollection $routeCollection,
        Request $request,
        RequestContext $requestContext
    ) {
        $this->routeCollection = clone $routeCollection;
        $this->request = $request;
        $this->requestContext = $requestContext;
    }

    /**
     * Проверка роута на существование.
     *
     * @param string $uri URL.
     *
     * @return boolean
     *
     * @since 24.12.2020 Переработка.
     */
    public function isLiveRoute(string $uri): bool
    {
        $matcher = new UrlMatcher($this->routeCollection, $this->requestContext);

        try {
            $matcher->match($uri);

            return true;
        } catch (ResourceNotFoundException $e) {
            return false;
        } catch (MethodNotAllowedException $e) {
            return true;
        }
    }

    /**
     * Получить информацию о роуте.
     *
     * @param string $uri URL.
     *
     * @return array
     *
     */
    public function getRouteInfo(string $uri) : array
    {
        $matcher = new UrlMatcher($this->routeCollection, $this->requestContext);

        try {
            return $matcher->match($uri);
        } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
            return [];
        }
    }

    /**
     * Получить информацию о роуте по URL. С возвратом ControllerReference.
     *
     * @param string $uri     URL.
     * @param array  $options Опции.
     *
     * @return ControllerReference|null
     */
    public function getRouteInfoReference(string $uri, array $options = []) : ?ControllerReference
    {
        $matcher = new UrlMatcher($this->routeCollection, $this->requestContext);

        try {
            $routeData = $matcher->match($uri);

            $controllerRoute = $routeData['_controller'];
            unset($routeData['_controller'], $routeData['_route']);

            return new ControllerReference(
                $controllerRoute,
                array_merge($routeData, $options)
            );
        } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
            return null;
        }
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param integer     $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl(
        $route,
        $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) : string {
        $routeParams = $this->routeCollection->get($route);

        if ($routeParams === null) {
            return '';
        }

        $urlGenerator = new UrlGenerator(
            $this->routeCollection,
            $this->requestContext
        );

        try {
            return $urlGenerator->generate($route, $parameters, $referenceType);
        } catch (Exception $e) {
            return '';
        }
    }
}
