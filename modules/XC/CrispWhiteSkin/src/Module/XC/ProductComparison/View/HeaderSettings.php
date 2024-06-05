<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;
use XC\ProductComparison\Core\Data;

/**
 * Product comparison widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductComparison")
 */
class HeaderSettings extends \XC\CrispWhiteSkin\View\HeaderSettings
{
    /**
     * Check if recently updated
     *
     * @return bool
     */
    protected function isRecentlyUpdated()
    {
        return parent::isRecentlyUpdated() || (Data::getInstance()->getProductsCount() > 0 && Data::getInstance()->isRecentlyUpdated());
    }
}
