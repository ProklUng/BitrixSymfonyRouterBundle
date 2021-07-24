# Бандл для подключения Symfony Router к кастомным вариантам Bitrix-Symfony

## Зачем?

**INTERNAL**. Нужен для максимального разнесения функционала по пакетам. Плюс борьба с копипастой инструментов 
на проектах.

## Установка

1) `composer require proklung/bitrixsymfonyrouterbundle`

2) Подключение бандла в `standalone_bundles.php`

## Параметры

Файл `symfony_router.yaml` в конфигурации бандлов:

- `enabled` - Использовать бандл. По умолчанию `true`.
- `controller_annotations_path` - массив с путями к контроллерам, использующим аннотации. 
- `router_cache_path` - путь к кэшу аннотаций (вида `%kernel.cache.dir%/routes`). По умолчанию `null`. 
   Если задан, то роуты будут кэшироваться. 
- `router_config_file` - путь к файлу с конфигурацией роутов. По умолчанию  `local/configs/routes.yaml`. Файл может быть в любом поддерживаемом Symfony формате - Yaml, PHP, XML и т.д.
- `router_check_exists_controller` - проверять на существование классы-контроллеры. По умолчанию `false`.

## Конфигурирование нативных роутов Битрикса через Yaml файл

С версии `21.400.0` (от 16.07.2021) главного модуля в Битриксе появился [сносный](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=013764&LESSON_PATH=3913.3516.5062.13764) роутер.

#### Зачем?

Чтобы использовать привычный способ конфигурирования роутов через Yaml.

#### Использование

Файл описания маршрутов (например, `/local/routes/web.php`): 

```php
use Prokl\ServiceProvider\ServiceProvider;
use Bitrix\Main\Routing\Controllers\PublicPageController;
use Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\RoutesLoader;
use Prokl\BitrixSymfonyRouterBundle\Services\Utils\BitrixRouteConvertor;
use Bitrix\Main\Routing\RoutingConfigurator;

// Не обязательно. Смотри ниже.
$container = ServiceProvider::instance();

$agnosticRouter = new RoutesLoader(
    $_SERVER['DOCUMENT_ROOT'] . '/local/configs/bitrix_routes.yaml', // Конфиг роутов
    $_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/routes', // Кэш; если null - без кэширования.
    $_ENV['DEBUG']
);
$routeCollection = $agnosticRouter->getRoutes();

$routeConvertor = new BitrixRouteConvertor($routeCollection);
// Не обязательно. Без контейнера контроллеры будут инстанцироваться через new,
// а не через контейнер. Но тогда уже без разрешения зависимостей.
$routeConvertor->setContainer($container);

return function (RoutingConfigurator $routes) use ($container, $routeConvertor, $routeCollection) {

    $routeConvertor->convertRoutes($routes);
};
```

В случае, если контейнер подключается, то возможна краткая форма:

```php
use Prokl\ServiceProvider\ServiceProvider;
use Bitrix\Main\Routing\Controllers\PublicPageController;
use Prokl\BitrixSymfonyRouterBundle\Services\Utils\BitrixRouteConvertor;
use Bitrix\Main\Routing\RoutingConfigurator;

// Не обязательно. Смотри ниже.
$container = ServiceProvider::instance();

$routeCollection = $container->get('bitrix_native_routes.routes.collection');

$routeConvertor = new BitrixRouteConvertor($routeCollection);
// Не обязательно. Без контейнера контроллеры будут инстанцироваться через new,
// а не через контейнер. Но тогда уже без разрешения зависимостей.
$routeConvertor->setContainer($container);

return function (RoutingConfigurator $routes) use ($container, $routeConvertor, $routeCollection) {

    $routeConvertor->convertRoutes($routes);

    $routes->get('/', new PublicPageController('/index.php'));

};
```

Пример файла с конфигурацией роутов (обычный yaml файл с роутами для Symfony):

```yaml
first_bitrix_route:
  path: /foo/{param}/
  controller: 'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture::cacheAction'
  methods: GET|POST
  requirements:
    param: '\d+'
  defaults:
    param: 'Russia'

# Старые статические страницы
# / => /index.php
public_page:
  path: /
  controller: ''
  defaults:
    _public: true # Ключевой признак

```

Если установлен Битрикс с версией модуля младше `21.400.0`, то соответствующие сервисы будут
удалены из бандла на стадии компиляции.

## Использование роутера без контейнера и вне фреймворка

`init.php`:

```php
use Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\RoutesLoader;
use Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\Router;
use Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\BitrixInitializerRouter;

$agnosticRouter = new RoutesLoader(
    $_SERVER['DOCUMENT_ROOT'] . '/local/configs/standalone_routes.yaml',
    $_SERVER['DOCUMENT_ROOT'] . '/bitrix/cache/routes_agnostic', // Кэш; если null - без кэширования.
    $_ENV['DEBUG'] // Режим отладки или нет
);

$agnosticRouterInstance = new Router(
    $agnosticRouter->getRouter(),
    new BitrixInitializerRouter()
);
```

Все. Подтянутся роуты из `/local/configs/standalone_routes.yaml`. Автоматически подцепятся события.

Допускается наличие нескольких таких "агностических" роутеров в один момент.