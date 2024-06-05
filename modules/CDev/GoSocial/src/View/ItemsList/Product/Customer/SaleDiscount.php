<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class SaleDiscount extends \CDev\Sale\View\ItemsList\Product\Customer\SaleDiscount
{
    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();

        if ($this->getSaleDiscount()) {
            $list[] = $this->getSaleDiscount()->getOpenGraphMetaTags();
        }

        return $list;
    }
}
