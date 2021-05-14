<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Utils;

use Exception;
use Prokl\BitrixSymfonyRouterBundle\Services\Contracts\DispatchControllerInterface;
use Prokl\BitrixSymfonyRouterBundle\Services\Controllers\ErrorJsonController;
use Prokl\BitrixSymfonyRouterBundle\Services\Listeners\StringResponseListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;

/**
 * Class DispatchController
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Utils
 *
 * @since 05.09.2020
 * @since 07.09.2020 Light rewriting.
 * @since 11.09.2020 Доработки.
 * @since 21.10.2020 Доработки. Сеттеры и геттеры. Заголовки.
 * @since 24.10.2020 ErrorJsonController прибывает снаружи.
 * @since 31.10.2020 ArgumentResolverInterface пробрасывается снаружи.
 */
class DispatchController implements DispatchControllerInterface
{
    /**
     * @var Request $request Request.
     */
    private $request;

    /**
     * @var Response | null $response Response.
     */
    private $response;

    /**
     * @var EventDispatcherInterface $dispatcher Диспетчер событий.
     */
    private $dispatcher;

    /**
     * @var ControllerResolverInterface $controllerResolver Разрешитель контроллеров.
     */
    private $controllerResolver;

    /**
     * @var ArgumentResolverInterface $argumentResolver Argument Resolver.
     */
    protected $argumentResolver;

    /**
     * @var array $defaultSubscribers Подписчики на события по умолчанию.
     */
    private $defaultSubscribers;

    /**
     * @var array $headers Заголовки запроса.
     */
    private $headers = [];

    /**
     * DispatchController constructor.
     *
     * @param EventDispatcherInterface    $dispatcher          Диспетчер событий.
     * @param ControllerResolverInterface $controllerResolver  Разрешитель контроллеров.
     * @param ArgumentResolverInterface   $argumentResolver    Argument resolver.
     * @param ErrorJsonController         $errorJsonController Ошибки в JSON.
     * @param Request|null                $request             Request.
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver,
        ErrorJsonController $errorJsonController,
        Request $request = null
    ) {
        $this->dispatcher = $dispatcher;

        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;

        $this->request = $request ?? Request::createFromGlobals();

        // Подписчики на события по умолчанию.
        $this->defaultSubscribers = [
            new StringResponseListener(),
            new ErrorListener(
                [$errorJsonController, 'exceptionAction']
            ),
            new ResponseListener('UTF-8')
        ];
    }

    /**
     * @inheritDoc
     */
    public function dispatch(
        $controllerAction
    ): bool {
        // Задать контроллер
        $this->request->attributes->set('_controller', $controllerAction);

        $this->request->headers->add($this->headers);

        $this->addSubscribers($this->defaultSubscribers);

        $framework = new HttpKernel(
            $this->dispatcher,
            $this->controllerResolver,
            null,
            $this->argumentResolver
        );

        try {
            $this->response = $framework->handle(
                $this->request
            );

            $framework->terminate($this->request, $this->response);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        if ($this->response) {
            $this->response->send();
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setQuery(array $query) : self
    {
        $this->request->query->add($query);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParams(array $arParams): self
    {
        $this->request->attributes->add($arParams);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addListener($listener) : self
    {
        if (is_object($listener)) {
            $this->defaultSubscribers[] = $listener;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Кучно добавить слушателей событий.
     *
     * @param array $subscribers
     */
    private function addSubscribers(array $subscribers = []) : void
    {
        foreach ($subscribers as $subscriber) {
            if (!is_object($subscriber)) {
                continue;
            }
            $this->dispatcher->addSubscriber($subscriber);
        }
    }
}
