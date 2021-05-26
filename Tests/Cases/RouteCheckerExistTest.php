<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Cases;

use LogicException;
use Prokl\BitrixSymfonyRouterBundle\Services\Utils\RouteCheckerExist;
use Prokl\TestingTools\Base\BaseTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteCheckerExistTest
 * @package Prokl\BitrixSymfonyRouterBundle\Tests\Cases
 *
 * @since 26.05.2021
 */
class RouteCheckerExistTest extends BaseTestCase
{
    /**
     * @var RouteCheckerExist $obTestObject
     */
    protected $obTestObject;

    /**
     * @return void
     *
     * @internal Если все OK, то не будет выброшено исключение.
     */
    public function testCheck() : void
    {

        $this->obTestObject = new RouteCheckerExist(
            $this->getRouteCollection(
                'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\ExampleSimpleController::action'
            )
        );

        $this->obTestObject->check();

        $this->assertTrue(true);
    }

    /**
     * Invalid action.
     *
     * @return void
     *
     * @internal Если все OK, то не будет выброшено исключение.
     */
    public function testCheckInvalidAction() : void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Class Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\ExampleSimpleController declaring as controller for route test_route dont have method fake.'
        );

        $this->obTestObject = new RouteCheckerExist(
            $this->getRouteCollection(
                'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\ExampleSimpleController::fake'
            )
        );

        $this->obTestObject->check();
    }

    /**
     * Несуществующий контроллер.
     *
     * @return void
     *
     * @internal Если все OK, то не будет выброшено исключение.
     */
    public function testCheckInvalidController() : void
    {
        $this->obTestObject = new RouteCheckerExist(
            $this->getRouteCollection(
                'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\Unexists::action'
            )
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Class Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\Unexists declaring as controller for route test_route not exists.'
        );
        $this->obTestObject->check();
    }

    /**
     * Invokable контроллер.
     *
     * @return void
     *
     * @internal Если все OK, то не будет выброшено исключение.
     */
    public function testCheckInvokable() : void
    {

        $this->obTestObject = new RouteCheckerExist(
            $this->getRouteCollection(
                'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\InvokableController'
            )
        );

        $this->obTestObject->check();

        $this->assertTrue(true);
    }

    /**
     * Invokable контроллер. Без метода __invoke.
     *
     * @return void
     *
     * @internal Если все OK, то не будет выброшено исключение.
     */
    public function testCheckInvokableInvalid() : void
    {
        $this->obTestObject = new RouteCheckerExist(
            $this->getRouteCollection(
                'Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\ExampleSimpleController'
            )
        );

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Class Prokl\BitrixSymfonyRouterBundle\Tests\Fixture\ExampleSimpleController declaring as controller for route test_route dont have method __invoke.'
        );

        $this->obTestObject->check();
    }

    /**
     * RouteCollection.
     *
     * @param string $controllerString Контроллер + action.
     *
     * @return RouteCollection
     */
    private function getRouteCollection(
        string $controllerString
    ) : RouteCollection {
        $collection = new RouteCollection();
        $collection->add(
            'test_route',
            new Route(
                '/test/route',
                [
                    '_controller' => $controllerString
                ]
            )
        );

        return $collection;
    }
}
