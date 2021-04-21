<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Controllers;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ErrorController
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Controllers
 *
 * @since 05.09.2020
 * @since 09.09.2020 Implements interface.
 */
class ErrorController implements ErrorControllerInterface
{
    /**
     * Обработчик ошибок.
     *
     * @param FlattenException $exception Исключение.
     *
     * @return Response
     */
    public function exceptionAction(FlattenException $exception): Response
    {
        $msg = 'Something went wrong! ('.$exception->getMessage().')';

        return new Response($msg, $exception->getStatusCode());
    }
}
