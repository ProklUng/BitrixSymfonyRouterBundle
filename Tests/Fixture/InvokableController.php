<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvokableController
 * @package Prokl\BitrixSymfonyRouterBundle\Tests\Fixture
 *
 * @since 21.10.2020
 */
class InvokableController extends AbstractController
{
    public function __invoke(Request $request, int $id): Response
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
