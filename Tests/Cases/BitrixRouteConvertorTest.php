<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Cases;

use LogicException;
use Prokl\BitrixSymfonyRouterBundle\Services\Utils\BitrixRouteConvertor;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tools\PHPUnitUtils;
use ReflectionException;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class BitrixRouteConvertorTest
 * @package Prokl\BitrixSymfonyRouterBundle\Tests
 * @coversDefaultClass BitrixRouteConvertor
 *
 * @since 24.07.2021
 */
class BitrixRouteConvertorTest extends BaseTestCase
{
    /**
     * @var BitrixRouteConvertor $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new BitrixRouteConvertor($this->getRouteCollection());
    }

    /**
     * parseControllerString(). Неправильное название action.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringInvalidAction() : void
    {
        $this->expectException(LogicException::class);

        PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', 'Controller::action']
        );
    }

    /**
     * parseControllerString(). Неправильное название action.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringInvalidArgument() : void
    {
        $this->expectException(LogicException::class);

        PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', '']
        );
    }

    /**
     * parseControllerString(). Неправильное название action.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringValidArgument() : void
    {
        $result = PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', 'Controller::doAction']
        );

        $this->assertEquals(
            ['Controller', 'doAction'],
            $result
        );
    }

    /**
     * parseControllerString(). Неправильное название action. Массив.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringInvalidArray() : void
    {
        $this->expectException(LogicException::class);

        PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', ['Controller', 'do']]
        );
    }

    /**
     * parseControllerString(). Массив.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringValidArray() : void
    {
        $result = PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', ['Controller', 'doAction']]
        );

        $this->assertEquals(
            ['Controller', 'doAction'],
            $result
        );
    }

    /**
     * parseControllerString(). Неправильное название action.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseControllerStringInvoked() : void
    {
        $this->expectException(LogicException::class);

        PHPUnitUtils::callMethod(
            $this->obTestObject,
            'parseControllerString',
            ['fooName', 'InvokedController']
        );
    }

    /**
     * @return RouteCollection
     */
    private function getRouteCollection() : RouteCollection
    {
        $collection = new RouteCollection();

        return $collection;

    }
}
