<?php

namespace Prokl\BitrixSymfonyRouterBundle\Tests\Fixture;

/**
 * Class ExampleBitrixActionController
 * @package Prokl\BitrixSymfonyRouterBundle\Tests\Fixture
 *
 * @since 24.07.2021
 */
class ExampleBitrixActionController
{
    /**
     * @return string
     */
    public static function getControllerClass() {
        return ExampleBitrixActionController::class;
    }

    /**
     * @return string
     */
    public static function getDefaultName() {
        return 'testingAction';
    }

    public function cacheAction(string $country)
    {
        return ['cacheDir' => 'test', 'country' => $country];
    }

    public function configureActions()
    {
        return [
            'cache' => [
                'prefilters' => [], 'postfilters' => [],
            ],
        ];
    }
}