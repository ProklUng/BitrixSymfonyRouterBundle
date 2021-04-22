<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Facades;

use Prokl\FacadeBundle\Services\AbstractFacade;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RunRoute
 * Фасад DispatchRoute.
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Facades
 *
 * @since 18.09.2020
 * @since 21.09.2020 Изменено название сервиса symfony.get.routes на routes.collection.
 * @since 24.09.2020 Изменено назад.
 * @since 02.10.2020 Убрал AppFacade.
 *
 * @method static post(string $url, array $payload = [])
 * @method static get(string $url, array $payload = [])
 * @method static dispatch(string $url)
 * @method static initContext()
 * @method static setParams(array $arParams)
 * @method static setRoutes(RouteCollection $routes)
 * @method static setMethod(string $method)
 * @method static setHeaders(array $headers)
 */
class RunRoute extends AbstractFacade
{
    /**
     * Сервис фасада.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return 'dispatcher.route';
    }
}
