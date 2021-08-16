<?php

namespace Prokl\BitrixSymfonyRouterBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class AfterHandleRequestEvent
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 *
 * @since 16.08.2021
 */
class AfterHandleRequestEvent extends Event
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var Response $response
     */
    private $response;

    /**
     * @param Request  $request  Request.
     * @param Response $response Response.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}