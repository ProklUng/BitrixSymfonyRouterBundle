<?php

namespace Prokl\BitrixSymfonyRouterBundle\Services\Agnostic;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\SelfCheckingResourceChecker;
use Symfony\Component\Config\ResourceCheckerConfigCacheFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RoutesLoader
 * Независимый от контейнера загрузчик роутов.
 * @package Prokl\BitrixSymfonyRouterBundle\Services\Agnostic
 *
 * @since 24.07.2021
 */
class RoutesLoader
{
    /**
     * @var RouterInterface $router Роутер.
     */
    private $router;

    /**
     * AgnosticRouteLoader constructor.
     *
     * @param string      $configFile Yaml/php/xml файл с конфигурацией роутов.
     * @param string|null $cacheDir   Путь к кэшу. Null -> не кэшировать.
     * @param boolean     $debug      Режим отладки.
     */
    public function __construct(
        string $configFile,
        ?string $cacheDir = null,
        bool $debug = true
    ) {
        $resolver = new LoaderResolver(
            [
                new YamlFileLoader(new FileLocator()),
                new PhpFileLoader(new FileLocator()),
                new XmlFileLoader(new FileLocator()),
            ]
        );

        $delegatingLoader = new DelegatingLoader($resolver);

        $requestContext = new RequestContext();
        $request = Request::createFromGlobals();

        $checker = new SelfCheckingResourceChecker();
        $cacheFactory = new ResourceCheckerConfigCacheFactory([$checker]);

        $this->router = new Router(
            $delegatingLoader,
            $configFile,
            [
                'cache_dir' => $cacheDir,
                'debug' => $debug,
                'generator_class' => 'Symfony\Component\Routing\Generator\CompiledUrlGenerator',
                'generator_dumper_class' => 'Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper',
                'matcher_class' =>  'Symfony\Bundle\FrameworkBundle\Routing\RedirectableCompiledUrlMatcher',
                'matcher_dumper_class' => 'Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper'
            ],
            $requestContext->fromRequest($request)
        );

        if ($cacheDir) {
            $this->router->setConfigCacheFactory($cacheFactory);
        }
    }

    /**
     * Роуты.
     *
     * @return RouteCollection
     */
    public function getRoutes() : RouteCollection
    {
        return $this->router->getRouteCollection();
    }
}