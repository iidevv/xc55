<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ChangeCouponTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasParameter('xcart.cdev.coupons.coupon_types')) {
            /** @var string[] $types */
            $types = $container->getParameter('xcart.cdev.coupons.coupon_types');
            $types[] = 'F';

            $container->setParameter('xcart.cdev.coupons.coupon_types', $types);
        }
    }
}
