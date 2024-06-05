<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Bestsellers\Tests\Controller\Customer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use CDev\Bestsellers\Controller\Customer\Bestsellers;

/**
 * @coversDefaultClass \CDev\Bestsellers\Controller\Customer\Bestsellers
 */
class BestsellersTest extends KernelTestCase
{
    public function testGetTitle(): void
    {
        self::bootKernel();

        $controller = new Bestsellers();

        static::assertEquals('Bestsellers', $controller->getTitle());
    }
}
