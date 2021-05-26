<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExampleSimpleController
 * @package Prokl\BitrixSymfonyRouterBundle\Tests\Fixture
 *
 * @since 21.10.2020
 */
class ExampleSimpleController extends AbstractController
{
    public function action(Request $request, int $id): Response
    {
        return new Response(
            'OK'
        );
    }

    public function action2(Request $request): Response
    {
        return new Response(
            'OK'
        );
    }
}
