<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Facades;

use Prokl\FacadeBundle\Services\AbstractFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RunController
 * Фасад DispatchController.
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Facades
 *
 * @since 22.04.2021
 *
 * @method static post(string $url, array $payload = [])
 * @method static get(string $url, array $payload = [])
 * @method static dispatch(string $url)
 * @method static send()
 * @method static initContext()
 * @method static setParams(array $arParams)
 * @method static setPost(array $post)
 * @method static setRoutes(RouteCollection $routes)
 * @method static setMethod(string $method)
 * @method static setHeaders(array $headers)
 * @method static setRequest(Request $request)
 * @method static setQuery(array $query)
 * @method static addListener($listener)
 * @method static getResponse()
 */
class RunController extends AbstractFacade
{
    /**
     * Сервис фасада.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'dispatcher.controller';
    }
}
