<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Cases;

use Prokl\BitrixSymfonyRouterBundle\Services\Router\Annotations\SearchAnnotatedClasses;
use Prokl\TestingTools\Base\BaseTestCase;

/**
 * Class AgnosticRoutesLoaderTest
 * @package Prokl\BitrixSymfonyRouterBundle\Tests
 * @coversDefaultClass SearchAnnotatedClasses
 *
 * @since 08.08.2021
 */
class SearchAnnotatedClassesTest extends BaseTestCase
{
    /**
     * @var SearchAnnotatedClasses $obTestObject Тестируемый объект.
     */
    protected $obTestObject;

      /**
     * @var string $paths
     */
    private $paths = [__DIR__ . '/../Fixture/AnnotatedClass'];

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->obTestObject = new SearchAnnotatedClasses($this->paths);

    }

    /**
     * collect().
     *
     * @return void
     */
    public function testCollect() : void
    {
        $result = $this->obTestObject->collect();

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
    }

    /**
     * collect(). Пустая переменная controller.annotations.path.
     *
     * @return void
     */
    public function testCollectEmptyParams() : void
    {
        $this->obTestObject = new SearchAnnotatedClasses([]);
        $result = $this->obTestObject->collect();

        $this->assertEmpty($result);
    }
}
