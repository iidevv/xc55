<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class TopCategories extends \XLite\View\TopCategories
{
    /**
     * Get cache oarameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getCategoryId();

        $controller = \XLite::getController();
        if ($controller instanceof \CDev\Sale\Controller\Customer\SaleDiscount) {
            $list[] = $controller->getSaleDiscountId();
        }

        return $list;
    }
}
