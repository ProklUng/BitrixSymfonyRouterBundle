<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Agnostic;

use Symfony\Component\Routing\RouterInterface;

/**
 * Class BitrixRoutes
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Agnostic
 *
 * @since 26.07.2021
 */
class BitrixRoutes extends BaseRoutesConfigurator
{
    /**
     * @var RouterInterface $router Роутер.
     */
    protected static $router;
}