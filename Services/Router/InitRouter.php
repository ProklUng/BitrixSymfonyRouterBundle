<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Router;

use CHTTP;
use Exception;
use Prokl\BitrixSymfonyRouterBundle\Event\AfterHandleRequestEvent;
use Prokl\BitrixSymfonyRouterBundle\Event\KernelCustomEvents;
use Prokl\BitrixSymfonyRouterBundle\Services\Controllers\ErrorControllerInterface;
use Prokl\BitrixSymfonyRouterBundle\Services\Listeners\StringResponseListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class InitRouter
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Router
 *
 * @since 07.09.2020
 * @since 09.09.2020 Проброс Error Controller снаружи.
 * @since 11.09.2020 Переработка.
 * @since 16.09.2020 Доработка. RequestContext.
 * @since 30.10.2020 ArgumentResolver пробрасывается снаружи.
 * @since 19.11.2020 RequestStack пробрасывается снаружи.
 * @since 06.03.2021 Инициация события kernel.terminate.
 * @since 24.07.2021 Поддержка кэширования роутов.
 */
class InitRouter
{
    /**
     * @var RouteCollection[] $bundlesRoutes Роуты бандлов.
     */
    private static $bundlesRoutes = [];

    /**
     * @var RouterInterface $router Router.
     */
    private $router;

    /**
     * @var RouteCollection $routeCollection Коллекция роутов.
     */
    private $routeCollection;

    /**
     * @var Request $request Request.
     */
    private $request;

    /**
     * @var ErrorControllerInterface $errorController Error Controller.
     */
    private $errorController;

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
     * @var RequestStack $requestStack RequestStack.
     */
    protected $requestStack;

    /**
     * @var array $defaultSubscribers Подписчики на события по умолчанию.
     */
    private $defaultSubscribers;

    /**
     * InitRouter constructor.
     *
     * @param RouterInterface             $router             Роутер.
     * @param ErrorControllerInterface    $errorController    Error controller.
     * @param EventDispatcherInterface    $dispatcher         Event dispatcher.
     * @param ControllerResolverInterface $controllerResolver Controller resolver.
     * @param ArgumentResolverInterface   $argumentResolver   Argument resolver.
     * @param RequestStack                $requestStack       Request stack.
     * @param Request|null                $request            Request.
     *
     * @since 16.09.2020 Инициализация RequestContext.
     * @since 19.11.2020 RequestStack пробрасывается снаружи.
     */
    public function __construct(
        RouterInterface $router,
        ErrorControllerInterface $errorController,
        EventDispatcherInterface $dispatcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver,
        RequestStack $requestStack,
        Request $request = null
    ) {
        $this->request = $request ?? Request::createFromGlobals();
        $this->errorController = $errorController;
        $this->dispatcher = $dispatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;

        $this->router = $router;
        $this->routeCollection = $router->getRouteCollection();

        $this->requestStack = $requestStack;
        $this->requestStack->push($this->request);

        // RequestContext init.
        $requestContext = new RequestContext();
        $requestContext->fromRequest($this->request);

        // Роуты бандлов.
        $this->mixRoutesBundles();

        /** @psalm-suppress UndefinedInterfaceMethod */
        $matcher = $this->router->getMatcher();
        $matcher->setContext($requestContext);

        // Подписчики на события по умолчанию.
        $this->defaultSubscribers = [
            new RouterListener($matcher, $this->requestStack),
            new StringResponseListener(),
            new ErrorListener(
                [$this->errorController, 'exceptionAction']
            ),
            new ResponseListener('UTF-8')
        ];

        $this->addSubscribers($this->defaultSubscribers);
    }

    /**
     * Процесс обработки роутов.
     *
     * @return void
     * @throws Exception Ошибки роутера.
     */
    public function handle(): void
    {
        // Setup framework kernel
        $framework = new HttpKernel(
            $this->dispatcher,
            $this->controllerResolver,
            null,
            $this->argumentResolver
        );

        $response = $framework->handle($this->request);

        // Кастомное событие kernel.after_handle_request
        $this->dispatcher->dispatch(
            new AfterHandleRequestEvent($this->request, $response),
            KernelCustomEvents::AFTER_HANDLE_REQUEST
        );

        // Инициирует событие kernel.terminate.
        try {
            $framework->terminate($this->request, $response);
        } catch (Exception $e) {
            CHTTP::SetStatus($this->translateHttpResponseCode($e->getCode()));
            exit($e->getMessage());
        }

        // Handle if no route match found
        if ($response->getStatusCode() === 404) {
            // If no route found do nothing and let continue.
            return;
        }

        // Для внутренних нужд пометить роут Symfony
        $this->request->headers->set('X-Symfony-route', 1);

        // Перебиваю битриксовый 404 для роутов.
        CHTTP::SetStatus('200 OK');

        // Send the response to the browser and exit app.
        $response->send();

        // Инициирование события OnAfterEpilog
        $events = GetModuleEvents('main', 'OnAfterEpilog', true);
        foreach($events as $event) {
            ExecuteModuleEventEx($event, ['sfResponse' => $response, 'sfRequest' => $this->request]);
        }

        exit;
    }

    /**
     * Подмес роутов бандлов к общим роутам.
     *
     * @return void
     */
    public function mixRoutesBundles() : void
    {
        if (!self::$bundlesRoutes) {
            return;
        }

        foreach (self::$bundlesRoutes as $collection) {
            if ($collection instanceof RouteCollection) {
                $this->routeCollection->addCollection($collection);
            }
        }
    }

    /**
     * Добавить роуты бандлов.
     *
     * @param RouteCollection $routeCollection Коллкция роутов.
     *
     * @return void
     */
    public static function addRoutesBundle(RouteCollection $routeCollection) : void
    {
        self::$bundlesRoutes[] = $routeCollection;
    }

    /**
     * Задать Request.
     *
     * @param Request $request Request.
     *
     * @return InitRouter
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Кучно добавить слушателей событий.
     *
     * @param array $subscribers Подписчики.
     *
     * @return void
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

    /**
     * Превратить HTTP response code в текстовое сообщение.
     *
     * @param integer $code HTTP response code.
     *
     * @return string
     *
     * @since 03.05.2021
     */
    private function translateHttpResponseCode(int $code) : string
    {
        switch ($code) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
            default:
                return '';
                break;
        }

        return $code . ' ' . $text;
    }
}