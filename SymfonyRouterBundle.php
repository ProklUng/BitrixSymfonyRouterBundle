<?php

namespace Prokl\BitrixSymfonyRouterBundle;

use Prokl\BitrixSymfonyRouterBundle\DependencyInjection\SymfonyRouterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SymfonyRouterBundle
 * @package Prokl\BitrixSymfonyRouterBundle
 *
 * @since 21.04.2021
 */
final class SymfonyRouterBundle extends Bundle
{
   /**
   * @inheritDoc
   */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new SymfonyRouterExtension();
        }

        return $this->extension;
    }
}
