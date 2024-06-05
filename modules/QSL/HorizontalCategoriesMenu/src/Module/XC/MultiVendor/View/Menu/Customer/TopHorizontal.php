<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Module\XC\MultiVendor\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;
use XC\MultiVendor\Logic;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"CDev\SimpleCMS", "XC\MultiVendor"})
 */
class TopHorizontal extends \XLite\View\Menu\Customer\Top
{
    /**
     * @inheritdoc
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        if (Logic\Vendors::getVendorSpecificMode()) {
            $params[] = Logic\Vendors::getVendorSpecificMode();
        }

        return $params;
    }
}
