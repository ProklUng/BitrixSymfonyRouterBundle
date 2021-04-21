<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Contracts;

use Prokl\BitrixSymfonyRouterBundle\Services\Utils\DispatchController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface DispatchControllerInterface
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Contracts
 *
 * @since 21.04.2021
 */
interface DispatchControllerInterface
{
    /**
     * Исполнить контроллер.
     *
     * @param string|array $controllerAction Класс и метод контроллера.
     * Вида \Local\Handler::action. Или массив [класс, метод].
     *
     * @return boolean
     *
     * @since 06.09.2020 Small rewrite. Массив в качестве параметра.
     */
    public function dispatch($controllerAction): bool;

    /**
     * Заслать Response в браузер.
     *
     * @return boolean
     */
    public function send(): bool;

    /**
     * Задать Request.
     *
     * @param Request $request Request.
     *
     * @return DispatchController
     */
    public function setRequest(Request $request): self;

    /**
     * Задать $_GET параметры.
     *
     * @param array $query Query параметры.
     *
     * @return $this
     *
     * @since 21.10.2020
     */
    public function setQuery(array $query) : self;

    /**
     * Задать заголовки запроса.
     *
     * @param array $headers Заголовки.
     *
     * @return $this
     *
     * @since 21.10.2020
     */
    public function setHeaders(array $headers): self;

    /**
     * Задать параметры Request.
     *
     * @param array $arParams Параметры (лягут в аттрибуты Request).
     *
     * @return DispatchController
     */
    public function setParams(array $arParams): self;

    /**
     * Задать дополнительного подписчика на события.
     *
     * @param mixed $listener
     *
     * @return $this
     *
     * @since 07.09.2020
     */
    public function addListener($listener) : self;

    /**
     * Получить Response.
     *
     * @return Response
     *
     * @since 21.10.2020
     */
    public function getResponse(): Response;
}
