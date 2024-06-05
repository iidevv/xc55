<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Container
{
    private static ?ContainerInterface $container = null;

    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function getServiceLocator(): ServiceLocator
    {
        /** @var ServiceLocator $locator */
        $locator = self::getContainer()->get(ServiceLocator::class);

        return $locator;
    }
}
