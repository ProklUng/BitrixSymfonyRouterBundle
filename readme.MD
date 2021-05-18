# Бандл для подключения Symfony Router к кастомным вариантам Bitrix-Symfony

## Зачем?

**INTERNAL**. Нужен для максимального разнесения функционала по пакетам. Плюс борьба с копипастой инструментов 
на проектах.

## Установка

1) `composer require proklung/bitrixsymfonyrouterbundle`

2) Подключение бандла в `standalone_bundles.php`

## Всякое

### Файл с роутами

Передается через параметр `router_config_file` конфигурации бандла (`symfony_router.yaml` в конфигурации бандлов). 
Файл может быть в любом поддерживаемом Symfony формате - Yaml, PHP, XML и т.д.

По умолчанию - `local/configs/routes.yaml`.