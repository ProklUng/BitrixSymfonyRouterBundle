<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Supplemental
 * Маршруты API Symfony router.
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 */
class Supplemental
{
    /**
     * Handle root and any other routes
     *
     * Set to 404 response and allow app front to send back to wp.
     *
     * @param Request $request
     * @return Response
     */
    //if index return not found
    public static function indexRoute(Request $request) : Response
    {
        return new Response('catch-all', 404);
    }

    //if not found return 404
    public static function notFound(Request $request) : Response
    {
        return new Response('catch-all', 404);
    }
}
