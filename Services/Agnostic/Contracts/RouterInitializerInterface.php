<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\Contracts;

use Prokl\BitrixSymfonyRouterBundle\Services\Router\InitRouter;

/**
 * Interface Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\Contracts
 * @package Local\Bitrix
 *
 * @since 24.07.2021
 */
interface RouterInitializerInterface
{
    /**
     * Инициализация роутера.
     *
     * @param InitRouter $router Инициализированный роутер.
     *
     * @return mixed
     */
    public function init(InitRouter $router);
}