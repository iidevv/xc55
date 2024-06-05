<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * Save requested product ID in the recently viewed statistics
     */
    public function handleRequest()
    {
        if (\XLite\Core\Config::getInstance()->CDev->ProductAdvisor->rv_enabled) {
            \CDev\ProductAdvisor\Main::saveProductIds($this->getProductId());
        }

        parent::handleRequest();
    }
}
