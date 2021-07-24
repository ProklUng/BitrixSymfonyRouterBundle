<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Cases;

use Prokl\BitrixSymfonyRouterBundle\Services\Agnostic\RoutesLoader;
use Prokl\TestingTools\Base\BaseTestCase;

/**
 * Class AgnosticRoutesLoaderTest
 * @package Prokl\BitrixSymfonyRouterBundle\Tests
 * @coversDefaultClass RoutesLoader
 *
 * @since 24.07.2021
 */
class AgnosticRoutesLoaderTest extends BaseTestCase
{
    /**
     * @var RoutesLoader $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->obTestObject = new RoutesLoader(
            __DIR__ . '/../Fixture/bitrix_routes.yaml',
            null,
            true
        );
    }

    /**
     * getRoutes().
     *
     * @return void
     */
    public function testGetRoutes() : void
    {
        $result = $this->obTestObject->getRoutes();

        $routes = $result->get('first_bitrix_route');

        $this->assertSame($routes->getPath(), '/foo/{param}/');
    }
}
