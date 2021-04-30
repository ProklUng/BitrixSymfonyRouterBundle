<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Tools;

use Exception;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tools\Container\BuildContainer;

/**
 * Class ContainerAwareBaseTestCase
 * @packageProkl\BitrixSymfonyRouterBundle\Tests\Tools
 *
 * @since 23.04.2021
 */
class ContainerAwareBaseTestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = static::$testContainer = BuildContainer::getTestContainer(
            [
                'dev/test_container.yaml',
                'dev/local.yaml'
            ],
            '/Resources/config'
        );

        parent::setUp();
    }
}
