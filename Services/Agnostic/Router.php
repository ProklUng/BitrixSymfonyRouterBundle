<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Agnostic;

use Prokl\BitrixSymfonyRouterBundle\Services\Controllers\ErrorJsonController;
use Prokl\BitrixSymfonyRouterBundle\Services\Router\InitRouter;
use Prokl\WpSymfonyRouterBundle\Services\Agnostic\Contracts\RouterInitializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class Router
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Agnostic
 *
 * @since 24.07.2021
 */
class Router
{
    /**
     * @var InitRouter $router Инициализированный экземпляр роутера.
     */
    private $router;

    /**
     * AgnosticRouter constructor.
     *
     * @param RouterInterface            $router            Инициализированный экземпляр роутера.
     * @param RouterInitializerInterface $routerInitializer Инициализатор роутера.
     */
    public function __construct(
        RouterInterface $router,
        RouterInitializerInterface $routerInitializer
    ) {
        $this->router = new InitRouter(
            $router,
            new ErrorJsonController(
                new Serializer(
                    [new ObjectNormalizer],
                    [new JsonEncoder]

                )
            ),
            new EventDispatcher(),
            new ControllerResolver(),
            new ArgumentResolver(
                new ArgumentMetadataFactory(),
                [
                    new RequestAttributeValueResolver(),
                    new RequestValueResolver(),
                    new SessionValueResolver(),
                    new DefaultValueResolver(),
                    new VariadicValueResolver(),
                ]
            ),
            new RequestStack()
        );

        $routerInitializer->init($this->router);
    }
}
