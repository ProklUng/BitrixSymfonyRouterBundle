<?php

namespace Prokl\BitrixSymfonyRouterBundle\Event;

/**
 * Interface KernelCustomEvents
 *
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 * @since 16.08.2021
 */
interface KernelCustomEvents
{
    /**
     * Кастомное событие, запускаемое после обработки запроса роутером.
     *
     * Происходит после обработки запроса роутером, но до события kernel.terminate.
     * Необходимо, чтобы можно было как-то сопрягать запросы Symfony и нативный контекст.
     *
     * @Event("Prokl\BitrixSymfonyRouterBundle\Event\AfterHandleRequestEvent")
     */
    public const AFTER_HANDLE_REQUEST = 'kernel.after_handle_request';
}