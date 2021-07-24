<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Agnostic;

use Prokl\BitrixSymfonyRouterBundle\Services\Router\InitRouter;
use Prokl\WpSymfonyRouterBundle\Services\Agnostic\Contracts\RouterInitializerInterface;

/**
 * Class BitrixInitializerRouter
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Agnostic
 *
 * @since 24.07.2021
 */
class BitrixInitializerRouter implements RouterInitializerInterface
{
    /**
     * @inheritDoc
     */
    public function init(InitRouter $router)
    {
        AddEventHandler('main', 'OnProlog', [$router, 'handle']);
    }
}